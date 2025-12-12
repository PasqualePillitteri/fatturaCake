<?php
declare(strict_types=1);

namespace App\Controller;

use App\Rbac\DatabaseRbac;
use Cake\Event\EventInterface;

/**
 * Roles Controller
 *
 * Manages role definitions and permission assignments.
 * Only accessible by superadmin.
 *
 * @property \App\Model\Table\RolesTable $Roles
 * @property \App\Model\Table\PermissionsTable $Permissions
 */
class RolesController extends AppController
{
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
    }

    /**
     * Before filter - restrict to superadmin.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event): ?\Cake\Http\Response
    {
        parent::beforeFilter($event);

        // Only superadmin can manage roles
        if (!$this->hasRole('superadmin')) {
            $this->Flash->error(__('Non hai i permessi per accedere a questa sezione.'));

            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        return null;
    }

    /**
     * Index method - list all roles with user count.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function ($event) {
            $event->getSubject()->query
                ->contain(['Users' => function ($q) {
                    return $q->select(['id', 'role_id']);
                }])
                ->orderBy(['Roles.priority' => 'DESC']);
        });

        return $this->Crud->execute();
    }

    /**
     * View method - show role with its permissions.
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $this->Crud->on('beforeFind', function ($event) {
            $event->getSubject()->query->contain(['Permissions', 'Users']);
        });

        return $this->Crud->execute();
    }

    /**
     * Add method.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $this->Crud->on('beforeRender', function ($event) {
            $permissions = $this->fetchTable('Permissions')->getGroupedPermissions();
            $this->set('groupedPermissions', $permissions);
        });

        return $this->Crud->execute();
    }

    /**
     * Edit method - edit role and manage permissions.
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $this->Crud->on('beforeFind', function ($event) {
            $event->getSubject()->query->contain(['Permissions']);
        });

        $this->Crud->on('beforeRender', function ($event) {
            $permissions = $this->fetchTable('Permissions')->getGroupedPermissions();
            $this->set('groupedPermissions', $permissions);
        });

        $this->Crud->on('afterSave', function ($event) {
            // Clear RBAC cache after saving
            $rbac = new DatabaseRbac();
            $rbac->clearCache();
        });

        return $this->Crud->execute();
    }

    /**
     * Delete method.
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        $role = $this->Roles->get($id);

        // Prevent deletion of system roles
        if ($role->is_system) {
            $this->Flash->error(__('I ruoli di sistema non possono essere eliminati.'));

            return $this->redirect(['action' => 'index']);
        }

        // Check if role has users
        $userCount = $this->Roles->Users->find()
            ->where(['role_id' => $id])
            ->count();

        if ($userCount > 0) {
            $this->Flash->error(__('Impossibile eliminare: ci sono {0} utenti con questo ruolo.', $userCount));

            return $this->redirect(['action' => 'index']);
        }

        return $this->Crud->execute();
    }

    /**
     * Permissions matrix view.
     *
     * Shows all permissions with checkboxes for each role.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function matrix()
    {
        $rbac = new DatabaseRbac();
        $matrixData = $rbac->getAllPermissionsMatrix();

        $this->set('roles', $matrixData['roles']);
        $this->set('permissions', $matrixData['permissions']);
        $this->set('matrix', $matrixData['matrix']);

        // Group permissions by group_name
        $groupedPermissions = [];
        foreach ($matrixData['permissions'] as $permission) {
            $group = $permission->group_name ?: 'Altro';
            $groupedPermissions[$group][] = $permission;
        }
        $this->set('groupedPermissions', $groupedPermissions);
    }

    /**
     * Save permissions matrix.
     *
     * @return \Cake\Http\Response|null
     */
    public function saveMatrix()
    {
        $this->request->allowMethod(['post']);

        $data = $this->request->getData('permissions', []);
        $rolesPermissionsTable = $this->fetchTable('RolesPermissions');

        // Delete all existing permissions
        $rolesPermissionsTable->deleteAll([]);

        // Insert new permissions
        $count = 0;
        foreach ($data as $roleId => $permissionIds) {
            foreach ($permissionIds as $permissionId => $enabled) {
                if ($enabled) {
                    $entity = $rolesPermissionsTable->newEntity([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId,
                    ]);
                    if ($rolesPermissionsTable->save($entity)) {
                        $count++;
                    }
                }
            }
        }

        // Clear cache
        $rbac = new DatabaseRbac();
        $rbac->clearCache();

        $this->Flash->success(__('Permessi aggiornati: {0} assegnazioni salvate.', $count));

        return $this->redirect(['action' => 'matrix']);
    }

    /**
     * Sync permissions from controllers.
     *
     * @return \Cake\Http\Response|null
     */
    public function syncPermissions()
    {
        $this->request->allowMethod(['post']);

        $result = $this->fetchTable('Permissions')->syncFromControllers();

        $this->Flash->success(__(
            'Sincronizzazione completata: {0} nuovi permessi creati, {1} esistenti.',
            $result['created'],
            $result['existing']
        ));

        return $this->redirect(['action' => 'matrix']);
    }
}
