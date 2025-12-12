<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * ConfigurazioniSdi Controller
 *
 * @property \App\Model\Table\ConfigurazioniSdiTable $ConfigurazioniSdi
 */
class ConfigurazioniSdiController extends AppController
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
}
