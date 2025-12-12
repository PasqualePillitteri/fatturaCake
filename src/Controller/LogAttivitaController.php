<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * LogAttivita Controller
 *
 * @property \App\Model\Table\LogAttivitaTable $LogAttivita
 */
class LogAttivitaController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Log attivita is read-only
        $this->Crud->mapAction('index', 'Crud.Index');
        $this->Crud->mapAction('view', 'Crud.View');
    }
}
