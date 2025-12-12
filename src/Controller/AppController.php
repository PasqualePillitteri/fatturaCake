<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Model\Behavior\AuditLogBehavior;
use App\Model\Behavior\TenantScopeBehavior;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;
use Crud\Controller\ControllerTrait;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 * @property \Authorization\Controller\Component\AuthorizationComponent $Authorization
 */
class AppController extends Controller
{
    use ControllerTrait;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');

        // Authentication Component
        $this->loadComponent('Authentication.Authentication');

        // Authorization Component
        $this->loadComponent('Authorization.Authorization');

        // Load CRUD component with default actions
        $this->loadComponent('Crud.Crud', [
            'actions' => [
                'Crud.Index',
                'Crud.View',
                'Crud.Add',
                'Crud.Edit',
                'Crud.Delete',
            ],
            'listeners' => [
                'Crud.Api',
                'Crud.ApiPagination',
                'Crud.Search',
                'Crud.RelatedModels',
            ],
        ]);

        // Form protection component for CSRF and form tampering protection
        // Disable for common actions that have dynamic fields or nested forms (postLink inside forms)
        $this->loadComponent('FormProtection', [
            'unlockedActions' => ['add', 'edit', 'delete', 'addAttiva', 'addPassiva', 'addCliente', 'addFornitore', 'login', 'register'],
        ]);

        // Set default layout
        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Before filter callback.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event): ?\Cake\Http\Response
    {
        parent::beforeFilter($event);

        // Store tenant_id in session and set context for TenantScopeBehavior
        $identity = $this->request->getAttribute('identity');
        if ($identity) {
            $tenantId = $identity->get('tenant_id');
            $role = $identity->get('role');

            // Store in session
            $this->request->getSession()->write('Auth.tenant_id', $tenantId);
            $this->request->getSession()->write('Auth.role', $role);

            // Set static context for TenantScopeBehavior
            TenantScopeBehavior::setTenantContext(
                $tenantId ? (int)$tenantId : null,
                $role
            );

            // Set context for AuditLogBehavior
            AuditLogBehavior::setUserContext([
                'user_id' => $identity->get('id') ? (int)$identity->get('id') : null,
                'tenant_id' => $tenantId ? (int)$tenantId : null,
                'ip_address' => $this->request->clientIp(),
                'user_agent' => $this->request->getHeaderLine('User-Agent'),
            ]);

            // Set user info for views
            $this->set('currentUser', $identity);

            // Check subscription status (skip for superadmin, no tenant, and specific controllers)
            $skipControllers = ['Users', 'Abbonamenti', 'Piani', 'Tenants', 'Roles'];
            $currentController = $this->request->getParam('controller');

            if ($role !== 'superadmin' && $tenantId && !in_array($currentController, $skipControllers)) {
                $subscriptionCheck = $this->checkTenantSubscription((int)$tenantId);
                if (!$subscriptionCheck['valid']) {
                    // Logout user
                    $this->Authentication->logout();

                    $this->Flash->error($subscriptionCheck['message']);

                    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                }
            }
        }

        return null;
    }

    /**
     * Check if tenant has an active subscription.
     *
     * @param int $tenantId Tenant ID
     * @return array{valid: bool, message: string}
     */
    protected function checkTenantSubscription(int $tenantId): array
    {
        $abbonamentiTable = TableRegistry::getTableLocator()->get('Abbonamenti');

        $today = Date::now();

        // Find active subscription for tenant
        $abbonamento = $abbonamentiTable->find()
            ->where([
                'Abbonamenti.tenant_id' => $tenantId,
                'Abbonamenti.stato' => 'attivo',
                'Abbonamenti.data_inizio <=' => $today,
                'OR' => [
                    'Abbonamenti.data_fine IS' => null,
                    'Abbonamenti.data_fine >=' => $today,
                ],
            ])
            ->first();

        if ($abbonamento) {
            return ['valid' => true, 'message' => ''];
        }

        // Check if there's an expired subscription
        $expiredAbbonamento = $abbonamentiTable->find()
            ->contain(['Piani'])
            ->where([
                'Abbonamenti.tenant_id' => $tenantId,
            ])
            ->orderBy(['Abbonamenti.data_fine' => 'DESC'])
            ->first();

        if ($expiredAbbonamento) {
            return [
                'valid' => false,
                'message' => __('Il tuo abbonamento è scaduto. Contatta l\'amministratore per rinnovarlo.'),
            ];
        }

        return [
            'valid' => false,
            'message' => __('Nessun abbonamento attivo trovato. Contatta l\'amministratore.'),
        ];
    }

    /**
     * Get the current authenticated user.
     *
     * @return \Authentication\IdentityInterface|null
     */
    protected function getCurrentUser(): ?\Authentication\IdentityInterface
    {
        return $this->request->getAttribute('identity');
    }

    /**
     * Check if user has a specific role.
     *
     * @param string|array $roles Role(s) to check
     * @return bool
     */
    protected function hasRole(string|array $roles): bool
    {
        $identity = $this->getCurrentUser();
        if (!$identity) {
            return false;
        }

        $userRole = $identity->get('role');
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        return $userRole === $roles;
    }
}
