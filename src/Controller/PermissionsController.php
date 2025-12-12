<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Permissions Controller
 *
 * Manages permission definitions.
 * Only accessible by superadmin.
 *
 * @property \App\Model\Table\PermissionsTable $Permissions
 */
class PermissionsController extends AppController
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

        // Only superadmin can manage permissions
        if (!$this->hasRole('superadmin')) {
            $this->Flash->error(__('Non hai i permessi per accedere a questa sezione.'));

            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        return null;
    }

    /**
     * Index method - list all permissions grouped.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function ($event) {
            $event->getSubject()->query
                ->contain(['Roles'])
                ->orderBy([
                    'Permissions.group_name' => 'ASC',
                    'Permissions.controller' => 'ASC',
                    'Permissions.action' => 'ASC',
                ]);
        });

        return $this->Crud->execute();
    }

    /**
     * View method.
     *
     * @param string|null $id Permission id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $this->Crud->on('beforeFind', function ($event) {
            $event->getSubject()->query->contain(['Roles']);
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
            $this->set('groups', $this->getGroupOptions());
            $this->set('controllers', $this->getControllerOptions());
            $this->set('actions', $this->getActionOptions());
        });

        return $this->Crud->execute();
    }

    /**
     * Edit method.
     *
     * @param string|null $id Permission id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $this->Crud->on('beforeRender', function ($event) {
            $this->set('groups', $this->getGroupOptions());
            $this->set('controllers', $this->getControllerOptions());
            $this->set('actions', $this->getActionOptions());
        });

        return $this->Crud->execute();
    }

    /**
     * Delete method.
     *
     * @param string|null $id Permission id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        return $this->Crud->execute();
    }

    /**
     * Get available group options.
     *
     * @return array
     */
    protected function getGroupOptions(): array
    {
        return [
            'Fatturazione' => 'Fatturazione',
            'Anagrafiche' => 'Anagrafiche',
            'Prodotti' => 'Prodotti',
            'Utenti' => 'Utenti',
            'Sistema' => 'Sistema',
            'Altro' => 'Altro',
        ];
    }

    /**
     * Get available controller options from existing permissions + controllers.
     *
     * @return array
     */
    protected function getControllerOptions(): array
    {
        // Get existing controllers from permissions
        $existing = $this->Permissions->find()
            ->select(['controller'])
            ->distinct(['controller'])
            ->orderBy(['controller' => 'ASC'])
            ->all()
            ->extract('controller')
            ->toArray();

        // Add common controllers
        $common = [
            '*' => '* (Tutti)',
            'Dashboard' => 'Dashboard',
            'Fatture' => 'Fatture',
            'Anagrafiche' => 'Anagrafiche',
            'Prodotti' => 'Prodotti',
            'Users' => 'Users',
            'Roles' => 'Roles',
            'Permissions' => 'Permissions',
            'Tenants' => 'Tenants',
        ];

        foreach ($existing as $controller) {
            if (!isset($common[$controller])) {
                $common[$controller] = $controller;
            }
        }

        return $common;
    }

    /**
     * Get available action options.
     *
     * @return array
     */
    protected function getActionOptions(): array
    {
        return [
            '*' => '* (Tutte)',
            'index' => 'index (Elenco)',
            'view' => 'view (Dettaglio)',
            'add' => 'add (Aggiungi)',
            'edit' => 'edit (Modifica)',
            'delete' => 'delete (Elimina)',
        ];
    }
}
