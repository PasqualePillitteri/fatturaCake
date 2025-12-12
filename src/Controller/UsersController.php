<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Role priorities for permission checking.
     */
    protected array $rolePriorities = [
        'user' => 10,
        'staff' => 20,
        'admin' => 50,
        'superadmin' => 100,
    ];

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->mapAction('index', 'Crud.Index');
        $this->Crud->mapAction('view', 'Crud.View');
        $this->Crud->mapAction('add', 'Crud.Add');
        $this->Crud->mapAction('edit', 'Crud.Edit');
        $this->Crud->mapAction('delete', 'Crud.Delete');

        // Add event listener to validate role on save
        $this->Crud->on('beforeSave', function (\Cake\Event\EventInterface $event) {
            if (!$this->_validateRolePermission($event->getSubject()->entity)) {
                $event->stopPropagation();
                $event->getSubject()->success = false;
            }
        });
    }

    /**
     * Validate that user cannot assign a role higher than their own.
     * Also validates tenant_id manipulation.
     *
     * @param \App\Model\Entity\User $entity User entity being saved
     * @return bool True if valid, false if blocked
     */
    protected function _validateRolePermission($entity): bool
    {
        $currentUser = $this->Authentication->getIdentity();
        $currentRole = $currentUser->get('role') ?? 'user';
        $currentPriority = $this->rolePriorities[$currentRole] ?? 10;
        $currentTenantId = $currentUser->get('tenant_id');

        $newRole = $entity->role ?? 'user';
        $newPriority = $this->rolePriorities[$newRole] ?? 10;

        // Block if trying to assign a higher role than allowed
        if ($newPriority > $currentPriority) {
            $this->Flash->error(__('Operazione non consentita: non puoi assegnare il ruolo "{0}".', $newRole));
            return false;
        }

        // Non-superadmin cannot change tenant_id
        if ($currentRole !== 'superadmin') {
            // Force tenant_id to current user's tenant
            if ($entity->isNew()) {
                $entity->tenant_id = $currentTenantId;
            } elseif ($entity->isDirty('tenant_id') && $entity->tenant_id != $currentTenantId) {
                $this->Flash->error(__('Operazione non consentita: non puoi modificare il tenant.'));
                return false;
            }
        }

        // Prevent non-superadmin from editing superadmin users
        if ($currentRole !== 'superadmin' && !$entity->isNew()) {
            $originalRole = $entity->getOriginal('role');
            if ($originalRole === 'superadmin') {
                $this->Flash->error(__('Operazione non consentita: non puoi modificare un Super Admin.'));
                return false;
            }
        }

        // Prevent escalation: non-admin cannot edit admin users
        if (!in_array($currentRole, ['superadmin', 'admin']) && !$entity->isNew()) {
            $originalRole = $entity->getOriginal('role');
            if ($originalRole === 'admin') {
                $this->Flash->error(__('Operazione non consentita: non puoi modificare un Amministratore.'));
                return false;
            }
        }

        return true;
    }

    /**
     * Before filter - allow unauthenticated actions.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event): ?\Cake\Http\Response
    {
        parent::beforeFilter($event);

        // Allow unauthenticated actions
        $this->Authentication->addUnauthenticatedActions([
            'login',
            'logout',
            'forgotPassword',
            'resetPassword',
            'register',
        ]);

        // Skip authorization for public actions
        $publicActions = ['login', 'logout', 'forgotPassword', 'resetPassword', 'register'];
        if (in_array($this->request->getParam('action'), $publicActions)) {
            $this->Authorization->skipAuthorization();
        }

        return null;
    }

    /**
     * Login action.
     *
     * @return \Cake\Http\Response|null
     */
    public function login(): ?\Cake\Http\Response
    {
        // Use auth layout
        $this->viewBuilder()->setLayout('auth');

        $result = $this->Authentication->getResult();

        // If already logged in, redirect to dashboard
        if ($result && $result->isValid()) {
            $identity = $this->Authentication->getIdentity();
            $userId = $identity ? $identity->getIdentifier() : null;

            // If identity is corrupted, logout and show login form
            if ($userId === null) {
                $this->Authentication->logout();
                $this->Flash->warning(__('Sessione scaduta. Effettua nuovamente il login.'));

                return null;
            }

            // Update last login
            $user = $this->Users->get($userId);
            $user->last_login = \Cake\I18n\DateTime::now();
            $this->Users->save($user);

            $redirect = $this->request->getQuery('redirect', '/dashboard');

            return $this->redirect($redirect);
        }

        // Show error if form was submitted but login failed
        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Email o password non validi.'));
        }

        return null;
    }

    /**
     * Logout action.
     *
     * @return \Cake\Http\Response
     */
    public function logout(): \Cake\Http\Response
    {
        $this->Authentication->logout();
        $this->Flash->success(__('Sei stato disconnesso.'));

        return $this->redirect(['action' => 'login']);
    }

    /**
     * Register action - create new account with tenant.
     *
     * @return \Cake\Http\Response|null
     */
    public function register(): ?\Cake\Http\Response
    {
        $this->viewBuilder()->setLayout('auth');

        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Verify password confirmation
            if ($data['password'] !== $data['password_confirm']) {
                $this->Flash->error(__('Le password non coincidono.'));

                $this->set(compact('user'));

                return null;
            }

            // Check if email already exists
            $existingUser = $this->Users->find()
                ->where(['email' => $data['email']])
                ->first();

            if ($existingUser) {
                $this->Flash->error(__('Questa email è già registrata.'));

                $this->set(compact('user'));

                return null;
            }

            // Create Tenant first
            $tenantsTable = $this->fetchTable('Tenants');

            // Generate unique slug from company name
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $data['nome_azienda']));
            $slug = trim($slug, '-');
            if (empty($slug)) {
                $slug = 'azienda';
            }
            $baseSlug = $slug;
            $counter = 1;

            // Ensure slug is unique (include soft-deleted records)
            $checkSlugExists = function ($slugToCheck) use ($tenantsTable) {
                return $tenantsTable->find('withTrashed')
                    ->where(['slug' => $slugToCheck])
                    ->count() > 0;
            };

            while ($checkSlugExists($slug)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $tenant = $tenantsTable->newEntity([
                'nome' => $data['nome_azienda'],
                'partita_iva' => $data['partita_iva'] ?? null,
                'email' => $data['email'],
                'slug' => $slug,
                'is_active' => true,
            ]);

            try {
                if (!$tenantsTable->save($tenant)) {
                    $this->Flash->error(__('Impossibile creare l\'azienda. Verifica i dati inseriti.'));

                    $this->set(compact('user'));

                    return null;
                }
            } catch (\Cake\Database\Exception\QueryException $e) {
                // Handle duplicate slug error gracefully
                if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'slug')) {
                    $this->Flash->error(__('Il nome azienda genera uno slug già esistente. Prova con un nome diverso.'));
                } else {
                    $this->Flash->error(__('Errore durante la creazione dell\'azienda. Riprova.'));
                }

                $this->set(compact('user'));

                return null;
            }

            // Set tenant context for TenantScopeBehavior (required for user creation)
            \App\Model\Behavior\TenantScopeBehavior::setTenantContext($tenant->id, 'admin');

            // Create User with admin role
            $user = $this->Users->newEntity([
                'tenant_id' => $tenant->id,
                'username' => $data['email'],
                'email' => $data['email'],
                'password' => $data['password'],
                'nome' => $data['nome'],
                'cognome' => $data['cognome'],
                'role' => 'admin',
                'is_active' => true,
            ], [
                'accessibleFields' => [
                    'tenant_id' => true,
                    'role' => true,
                    'is_active' => true,
                ],
            ]);

            if ($this->Users->save($user)) {
                $this->Flash->success(__(
                    'Registrazione completata! Il tuo account demo è attivo per 30 giorni. Effettua il login.'
                ));

                return $this->redirect(['action' => 'login']);
            }

            // If user creation fails, delete the tenant
            $tenantsTable->delete($tenant);
            $this->Flash->error(__('Impossibile completare la registrazione. Riprova.'));
        }

        $this->set(compact('user'));

        return null;
    }

    /**
     * Profile action - view/edit current user profile.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function profile()
    {
        $this->Authorization->skipAuthorization();

        $user = $this->Users->get(
            $this->Authentication->getIdentity()->getIdentifier(),
            contain: ['Tenants']
        );

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'accessibleFields' => [
                    'nome' => true,
                    'cognome' => true,
                    'telefono' => true,
                    'avatar' => true,
                    // Prevent changing sensitive fields
                    'role' => false,
                    'tenant_id' => false,
                    'is_active' => false,
                ],
            ]);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Profilo aggiornato.'));

                return $this->redirect(['action' => 'profile']);
            }
            $this->Flash->error(__('Impossibile aggiornare il profilo.'));
        }

        $this->set(compact('user'));
    }

    /**
     * Change password action.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function changePassword()
    {
        $this->Authorization->skipAuthorization();

        $user = $this->Users->get($this->Authentication->getIdentity()->getIdentifier());

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Verify current password
            $passwordHasher = new \Authentication\PasswordHasher\DefaultPasswordHasher();
            if (!$passwordHasher->check($data['current_password'] ?? '', $user->password)) {
                $this->Flash->error(__('Password attuale non corretta.'));

                return null;
            }

            // Verify new password confirmation
            if ($data['password'] !== $data['password_confirm']) {
                $this->Flash->error(__('Le password non coincidono.'));

                return null;
            }

            $user = $this->Users->patchEntity($user, [
                'password' => $data['password'],
            ]);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Password aggiornata.'));

                return $this->redirect(['action' => 'profile']);
            }
            $this->Flash->error(__('Impossibile aggiornare la password.'));
        }

        $this->set(compact('user'));
    }

    /**
     * Forgot password action - request password reset.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function forgotPassword()
    {
        $this->viewBuilder()->setLayout('auth');

        if ($this->request->is('post')) {
            $email = $this->request->getData('email');

            $user = $this->Users->find()
                ->where([
                    'email' => $email,
                    'is_active' => true,
                    'deleted IS' => null,
                ])
                ->first();

            if ($user) {
                // Generate secure token
                $token = bin2hex(random_bytes(32));
                $expires = \Cake\I18n\DateTime::now()->addHours(2);

                $user->reset_token = $token;
                $user->reset_token_expires = $expires;

                if ($this->Users->save($user)) {
                    // Send email
                    $this->_sendPasswordResetEmail($user, $token);
                }
            }

            // Always show success message to prevent email enumeration
            $this->Flash->success(__(
                'Se l\'indirizzo email è registrato, riceverai un link per reimpostare la password.'
            ));

            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Reset password action - set new password with token.
     *
     * @param string|null $token Reset token
     * @return \Cake\Http\Response|null|void
     */
    public function resetPassword(?string $token = null)
    {
        $this->viewBuilder()->setLayout('auth');

        if (!$token) {
            $this->Flash->error(__('Token non valido.'));

            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->find()
            ->where([
                'reset_token' => $token,
                'reset_token_expires >' => \Cake\I18n\DateTime::now(),
                'is_active' => true,
                'deleted IS' => null,
            ])
            ->first();

        if (!$user) {
            $this->Flash->error(__('Il link per reimpostare la password è scaduto o non valido.'));

            return $this->redirect(['action' => 'forgotPassword']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Verify password confirmation
            if ($data['password'] !== $data['password_confirm']) {
                $this->Flash->error(__('Le password non coincidono.'));

                return null;
            }

            $user->password = $data['password'];
            $user->reset_token = null;
            $user->reset_token_expires = null;

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Password reimpostata con successo. Ora puoi accedere.'));

                return $this->redirect(['action' => 'login']);
            }

            $this->Flash->error(__('Impossibile reimpostare la password.'));
        }

        $this->set(compact('user', 'token'));
    }

    /**
     * Delete action - prevent user from deleting themselves or higher-role users.
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null): ?\Cake\Http\Response
    {
        $this->request->allowMethod(['post', 'delete']);

        $currentUser = $this->Authentication->getIdentity();
        $currentUserId = $currentUser->getIdentifier();
        $currentRole = $currentUser->get('role') ?? 'user';
        $currentPriority = $this->rolePriorities[$currentRole] ?? 10;

        // Cannot delete yourself
        if ((int)$id === (int)$currentUserId) {
            $this->Flash->error(__('Non puoi eliminare il tuo stesso account.'));
            return $this->redirect(['action' => 'index']);
        }

        $user = $this->Users->get($id);

        // Cannot delete user with higher or equal role (except yourself, already checked)
        $targetPriority = $this->rolePriorities[$user->role] ?? 10;
        if ($targetPriority >= $currentPriority && $currentRole !== 'superadmin') {
            $this->Flash->error(__('Non puoi eliminare un utente con ruolo "{0}".', $user->role));
            return $this->redirect(['action' => 'index']);
        }

        // Superadmin cannot be deleted by anyone except another superadmin
        if ($user->role === 'superadmin' && $currentRole !== 'superadmin') {
            $this->Flash->error(__('Non puoi eliminare un Super Admin.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->Authorization->authorize($user);

        if ($this->Users->delete($user)) {
            $this->Flash->success(__('Utente eliminato.'));
        } else {
            $this->Flash->error(__('Impossibile eliminare l\'utente.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Send password reset email.
     *
     * @param \App\Model\Entity\User $user User entity
     * @param string $token Reset token
     * @return void
     */
    protected function _sendPasswordResetEmail($user, string $token): void
    {
        $resetUrl = \Cake\Routing\Router::url([
            'controller' => 'Users',
            'action' => 'resetPassword',
            $token,
        ], true);

        try {
            $mailer = new \Cake\Mailer\Mailer('default');
            $mailer
                ->setTo($user->email)
                ->setSubject(__('Reimposta la tua password - FatturaCake'))
                ->setEmailFormat('html')
                ->setViewVars([
                    'user' => $user,
                    'resetUrl' => $resetUrl,
                ])
                ->viewBuilder()
                    ->setTemplate('password_reset')
                    ->setLayout('default');

            $mailer->deliver();
        } catch (\Exception $e) {
            // Log error but don't expose it to user
            \Cake\Log\Log::error('Password reset email failed: ' . $e->getMessage());
        }
    }
}
