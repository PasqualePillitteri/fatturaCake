<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Piani Controller
 *
 * Manages subscription plans.
 * Only accessible by superadmin.
 *
 * @property \App\Model\Table\PianiTable $Piani
 */
class PianiController extends AppController
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

        // Only superadmin can manage plans
        if (!$this->hasRole('superadmin')) {
            $this->Flash->error(__('Non hai i permessi per accedere a questa sezione.'));

            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        return null;
    }

    /**
     * Index method - list all plans.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function ($event) {
            $event->getSubject()->query
                ->orderBy(['Piani.sort_order' => 'ASC', 'Piani.nome' => 'ASC']);
        });

        return $this->Crud->execute();
    }

    /**
     * View method - show plan with its subscriptions.
     *
     * @param string|null $id Piano id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $this->Crud->on('beforeFind', function ($event) {
            $event->getSubject()->query->contain(['Abbonamenti' => ['Tenants']]);
        });

        $this->Crud->on('beforeRender', function ($event) {
            $this->set('piano', $event->getSubject()->entity);
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
            $this->set('piano', $event->getSubject()->entity);
        });

        return $this->Crud->execute();
    }

    /**
     * Edit method.
     *
     * @param string|null $id Piano id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $this->Crud->on('beforeRender', function ($event) {
            $this->set('piano', $event->getSubject()->entity);
        });

        return $this->Crud->execute();
    }

    /**
     * Delete method.
     *
     * @param string|null $id Piano id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        // Check if plan has active subscriptions
        $subscriptionCount = $this->Piani->Abbonamenti->find()
            ->where([
                'piano_id' => $id,
                'stato' => 'attivo',
            ])
            ->count();

        if ($subscriptionCount > 0) {
            $this->Flash->error(__('Impossibile eliminare: ci sono {0} abbonamenti attivi con questo piano.', $subscriptionCount));

            return $this->redirect(['action' => 'index']);
        }

        return $this->Crud->execute();
    }
}
