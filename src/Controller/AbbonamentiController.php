<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Abbonamenti Controller
 *
 * Manages tenant subscriptions.
 * Only accessible by superadmin.
 *
 * @property \App\Model\Table\AbbonamentiTable $Abbonamenti
 */
class AbbonamentiController extends AppController
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

        // Only superadmin can manage subscriptions
        if (!$this->hasRole('superadmin')) {
            $this->Flash->error(__('Non hai i permessi per accedere a questa sezione.'));

            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        return null;
    }

    /**
     * Index method - list all subscriptions.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function ($event) {
            $event->getSubject()->query
                ->contain(['Tenants', 'Piani'])
                ->orderBy(['Abbonamenti.data_inizio' => 'DESC']);
        });

        return $this->Crud->execute();
    }

    /**
     * View method - show subscription details.
     *
     * @param string|null $id Abbonamento id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $this->Crud->on('beforeFind', function ($event) {
            $event->getSubject()->query->contain(['Tenants', 'Piani']);
        });

        $this->Crud->on('beforeRender', function ($event) {
            $this->set('abbonamento', $event->getSubject()->entity);
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
            $abbonamento = $event->getSubject()->entity;
            $tenants = $this->Abbonamenti->Tenants->find('list', limit: 200)
                ->where(['is_active' => true])
                ->orderBy(['nome' => 'ASC'])
                ->toArray();
            $piani = $this->Abbonamenti->Piani->find('list', limit: 200)
                ->where(['is_active' => true])
                ->orderBy(['sort_order' => 'ASC', 'nome' => 'ASC'])
                ->toArray();
            $this->set(compact('abbonamento', 'tenants', 'piani'));
        });

        return $this->Crud->execute();
    }

    /**
     * Edit method.
     *
     * @param string|null $id Abbonamento id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $this->Crud->on('beforeRender', function ($event) {
            $abbonamento = $event->getSubject()->entity;
            $tenants = $this->Abbonamenti->Tenants->find('list', limit: 200)
                ->where(['is_active' => true])
                ->orderBy(['nome' => 'ASC'])
                ->toArray();
            $piani = $this->Abbonamenti->Piani->find('list', limit: 200)
                ->where(['is_active' => true])
                ->orderBy(['sort_order' => 'ASC', 'nome' => 'ASC'])
                ->toArray();
            $this->set(compact('abbonamento', 'tenants', 'piani'));
        });

        return $this->Crud->execute();
    }

    /**
     * Delete method.
     *
     * @param string|null $id Abbonamento id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        return $this->Crud->execute();
    }
}
