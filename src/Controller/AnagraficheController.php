<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Anagrafiche Controller
 *
 * @property \App\Model\Table\AnagraficheTable $Anagrafiche
 */
class AnagraficheController extends AppController
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
     * Index Clienti - Solo anagrafiche di tipo cliente o entrambi
     *
     * @return \Cake\Http\Response|null|void
     */
    public function indexClienti()
    {
        $query = $this->Anagrafiche->find('search', search: $this->request->getQueryParams())
            ->where(['Anagrafiche.tipo IN' => ['cliente', 'entrambi']]);

        $anagrafiche = $this->paginate($query);

        $this->set(compact('anagrafiche'));
    }

    /**
     * Index Fornitori - Solo anagrafiche di tipo fornitore o entrambi
     *
     * @return \Cake\Http\Response|null|void
     */
    public function indexFornitori()
    {
        $query = $this->Anagrafiche->find('search', search: $this->request->getQueryParams())
            ->where(['Anagrafiche.tipo IN' => ['fornitore', 'entrambi']]);

        $anagrafiche = $this->paginate($query);

        $this->set(compact('anagrafiche'));
    }

    /**
     * Add Cliente - Nuova anagrafica di tipo cliente
     *
     * @return \Cake\Http\Response|null|void
     */
    public function addCliente()
    {
        $anagrafiche = $this->Anagrafiche->newEmptyEntity();
        $anagrafiche->tipo = 'cliente';

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['tipo'] = 'cliente';
            $anagrafiche = $this->Anagrafiche->patchEntity($anagrafiche, $data);

            if ($this->Anagrafiche->save($anagrafiche)) {
                $this->Flash->success(__('Il cliente è stato salvato.'));
                return $this->redirect(['action' => 'indexClienti']);
            }
            $this->Flash->error(__('Impossibile salvare il cliente. Riprova.'));
        }

        $this->set(compact('anagrafiche'));
    }

    /**
     * Add Fornitore - Nuova anagrafica di tipo fornitore
     *
     * @return \Cake\Http\Response|null|void
     */
    public function addFornitore()
    {
        $anagrafiche = $this->Anagrafiche->newEmptyEntity();
        $anagrafiche->tipo = 'fornitore';

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['tipo'] = 'fornitore';
            $anagrafiche = $this->Anagrafiche->patchEntity($anagrafiche, $data);

            if ($this->Anagrafiche->save($anagrafiche)) {
                $this->Flash->success(__('Il fornitore è stato salvato.'));
                return $this->redirect(['action' => 'indexFornitori']);
            }
            $this->Flash->error(__('Impossibile salvare il fornitore. Riprova.'));
        }

        $this->set(compact('anagrafiche'));
    }
}
