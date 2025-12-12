<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * SetupWizard Controller
 *
 * Gestisce la procedura guidata di configurazione iniziale per nuovi tenant.
 */
class SetupWizardController extends AppController
{
    /**
     * Steps del wizard.
     */
    protected array $steps = [
        1 => 'azienda',
        2 => 'categorie',
        3 => 'listino',
        4 => 'prodotti',
        5 => 'completato',
    ];

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Before filter - skip authorization for wizard.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): ?\Cake\Http\Response
    {
        parent::beforeFilter($event);
        $this->Authorization->skipAuthorization();

        return null;
    }

    /**
     * Main wizard page.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $identity = $this->Authentication->getIdentity();
        $tenantId = $identity->get('tenant_id');

        // Check if wizard already completed
        $tenant = $this->fetchTable('Tenants')->get($tenantId);
        if ($tenant->wizard_completed) {
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        // Get current step from session or default to 1
        $currentStep = $this->request->getSession()->read('SetupWizard.step') ?? 1;

        // Get counts for progress display
        $stats = $this->_getSetupStats();

        $this->set(compact('currentStep', 'stats', 'tenant'));
        $this->set('steps', $this->steps);
        $this->set('totalSteps', count($this->steps));
    }

    /**
     * Step 1: Dati Azienda.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function azienda()
    {
        $identity = $this->Authentication->getIdentity();
        $tenantId = $identity->get('tenant_id');

        $tenantsTable = $this->fetchTable('Tenants');
        $tenant = $tenantsTable->get($tenantId);

        if ($this->request->is(['post', 'put'])) {
            $tenant = $tenantsTable->patchEntity($tenant, $this->request->getData(), [
                'fields' => [
                    'nome', 'partita_iva', 'codice_fiscale', 'indirizzo',
                    'citta', 'provincia', 'cap', 'telefono', 'email', 'pec',
                    'codice_sdi', 'regime_fiscale',
                ],
            ]);

            if ($tenantsTable->save($tenant)) {
                $this->request->getSession()->write('SetupWizard.step', 2);
                $this->Flash->success(__('Dati azienda salvati.'));

                return $this->redirect(['action' => 'categorie']);
            }

            $this->Flash->error(__('Impossibile salvare i dati. Riprova.'));
        }

        $this->request->getSession()->write('SetupWizard.step', 1);
        $this->set(compact('tenant'));
        $this->set('currentStep', 1);
        $this->set('steps', $this->steps);
    }

    /**
     * Step 2: Categorie Prodotti.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function categorie()
    {
        $categorieTable = $this->fetchTable('CategorieProdotti');
        $categorie = $categorieTable->find()->toArray();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Create sample categories if requested
            if (!empty($data['crea_esempi'])) {
                $this->_createSampleCategories();
                $this->Flash->success(__('Categorie di esempio create.'));

                return $this->redirect(['action' => 'categorie']);
            }

            // Create custom category
            if (!empty($data['nome'])) {
                $categoria = $categorieTable->newEntity([
                    'nome' => $data['nome'],
                    'descrizione' => $data['descrizione'] ?? null,
                ]);

                if ($categorieTable->save($categoria)) {
                    $this->Flash->success(__('Categoria "{0}" creata.', $data['nome']));

                    return $this->redirect(['action' => 'categorie']);
                }

                $this->Flash->error(__('Impossibile creare la categoria.'));
            }

            // Next step
            if (!empty($data['next_step'])) {
                $this->request->getSession()->write('SetupWizard.step', 3);

                return $this->redirect(['action' => 'listino']);
            }
        }

        $this->request->getSession()->write('SetupWizard.step', 2);
        $this->set(compact('categorie'));
        $this->set('currentStep', 2);
        $this->set('steps', $this->steps);
    }

    /**
     * Step 3: Listino Prezzi.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function listino()
    {
        $listiniTable = $this->fetchTable('Listini');
        $listini = $listiniTable->find()->toArray();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Create default listino if requested
            if (!empty($data['crea_default'])) {
                $this->_createDefaultListino();
                $this->Flash->success(__('Listino predefinito creato.'));

                return $this->redirect(['action' => 'listino']);
            }

            // Create custom listino
            if (!empty($data['nome'])) {
                $listino = $listiniTable->newEntity([
                    'nome' => $data['nome'],
                    'descrizione' => $data['descrizione'] ?? null,
                    'is_default' => empty($listini),
                    'is_active' => true,
                ]);

                if ($listiniTable->save($listino)) {
                    $this->Flash->success(__('Listino "{0}" creato.', $data['nome']));

                    return $this->redirect(['action' => 'listino']);
                }

                $this->Flash->error(__('Impossibile creare il listino.'));
            }

            // Next step
            if (!empty($data['next_step'])) {
                $this->request->getSession()->write('SetupWizard.step', 4);

                return $this->redirect(['action' => 'prodotti']);
            }
        }

        $this->request->getSession()->write('SetupWizard.step', 3);
        $this->set(compact('listini'));
        $this->set('currentStep', 3);
        $this->set('steps', $this->steps);
    }

    /**
     * Step 4: Prodotti.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function prodotti()
    {
        $prodottiTable = $this->fetchTable('Prodotti');
        $categorieTable = $this->fetchTable('CategorieProdotti');
        $listiniTable = $this->fetchTable('Listini');

        $prodotti = $prodottiTable->find()->contain(['Categorias'])->toArray();
        $categorie = $categorieTable->find('list')->toArray();
        $listini = $listiniTable->find('list')->toArray();

        // Get default listino
        $defaultListino = $listiniTable->find()->where(['is_default' => true])->first();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Create sample products if requested
            if (!empty($data['crea_esempi'])) {
                $this->_createSampleProducts();
                $this->Flash->success(__('Prodotti di esempio creati.'));

                return $this->redirect(['action' => 'prodotti']);
            }

            // Create custom product
            if (!empty($data['nome'])) {
                $prodotto = $prodottiTable->newEntity([
                    'codice' => $data['codice'] ?? $this->_generateProductCode(),
                    'nome' => $data['nome'],
                    'descrizione' => $data['descrizione'] ?? null,
                    'categoria_id' => $data['categoria_id'] ?? null,
                    'prezzo' => $data['prezzo'] ?? 0,
                    'aliquota_iva' => $data['aliquota_iva'] ?? 22,
                    'unita_misura' => $data['unita_misura'] ?? 'NR',
                    'is_active' => true,
                ]);

                if ($prodottiTable->save($prodotto)) {
                    $this->Flash->success(__('Prodotto "{0}" creato.', $data['nome']));

                    return $this->redirect(['action' => 'prodotti']);
                }

                $this->Flash->error(__('Impossibile creare il prodotto.'));
            }

            // Next step (complete)
            if (!empty($data['next_step'])) {
                return $this->redirect(['action' => 'completato']);
            }
        }

        $this->request->getSession()->write('SetupWizard.step', 4);
        $this->set(compact('prodotti', 'categorie', 'listini', 'defaultListino'));
        $this->set('currentStep', 4);
        $this->set('steps', $this->steps);
    }

    /**
     * Step 5: Completamento.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function completato()
    {
        $identity = $this->Authentication->getIdentity();
        $tenantId = $identity->get('tenant_id');

        $tenantsTable = $this->fetchTable('Tenants');
        $tenant = $tenantsTable->get($tenantId);

        if ($this->request->is('post')) {
            // Mark wizard as completed
            $tenant->wizard_completed = true;
            $tenantsTable->save($tenant);

            $this->request->getSession()->delete('SetupWizard');
            $this->Flash->success(__('Configurazione completata! Benvenuto in FatturaCake.'));

            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        // Get stats for summary
        $stats = $this->_getSetupStats();

        $this->request->getSession()->write('SetupWizard.step', 5);
        $this->set(compact('tenant', 'stats'));
        $this->set('currentStep', 5);
        $this->set('steps', $this->steps);
    }

    /**
     * Skip wizard.
     *
     * @return \Cake\Http\Response
     */
    public function skip(): \Cake\Http\Response
    {
        $identity = $this->Authentication->getIdentity();
        $tenantId = $identity->get('tenant_id');

        $tenantsTable = $this->fetchTable('Tenants');
        $tenant = $tenantsTable->get($tenantId);
        $tenant->wizard_completed = true;
        $tenantsTable->save($tenant);

        $this->request->getSession()->delete('SetupWizard');
        $this->Flash->info(__('Wizard saltato. Puoi configurare tutto manualmente dalle impostazioni.'));

        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }

    /**
     * Get setup statistics.
     *
     * @return array
     */
    protected function _getSetupStats(): array
    {
        return [
            'categorie' => $this->fetchTable('CategorieProdotti')->find()->count(),
            'listini' => $this->fetchTable('Listini')->find()->count(),
            'prodotti' => $this->fetchTable('Prodotti')->find()->count(),
        ];
    }

    /**
     * Create sample categories.
     *
     * @return void
     */
    protected function _createSampleCategories(): void
    {
        $categorieTable = $this->fetchTable('CategorieProdotti');

        $samples = [
            ['nome' => 'Servizi Professionali', 'descrizione' => 'Servizi di consulenza e assistenza'],
            ['nome' => 'Prodotti', 'descrizione' => 'Prodotti fisici e materiali'],
            ['nome' => 'Consulenza', 'descrizione' => 'Attività di consulenza specialistica'],
            ['nome' => 'Formazione', 'descrizione' => 'Corsi e attività formative'],
        ];

        foreach ($samples as $sample) {
            // Check if already exists
            $exists = $categorieTable->find()->where(['nome' => $sample['nome']])->first();
            if (!$exists) {
                $entity = $categorieTable->newEntity($sample);
                $categorieTable->save($entity);
            }
        }
    }

    /**
     * Create default listino.
     *
     * @return void
     */
    protected function _createDefaultListino(): void
    {
        $listiniTable = $this->fetchTable('Listini');

        // Check if already exists
        $exists = $listiniTable->find()->where(['is_default' => true])->first();
        if (!$exists) {
            $listino = $listiniTable->newEntity([
                'nome' => 'Listino Base',
                'descrizione' => 'Listino prezzi predefinito',
                'is_default' => true,
                'is_active' => true,
            ]);
            $listiniTable->save($listino);
        }
    }

    /**
     * Create sample products.
     *
     * @return void
     */
    protected function _createSampleProducts(): void
    {
        $prodottiTable = $this->fetchTable('Prodotti');
        $categorieTable = $this->fetchTable('CategorieProdotti');

        // Get first category
        $categoria = $categorieTable->find()->first();

        $samples = [
            [
                'codice' => 'CONS-001',
                'nome' => 'Consulenza oraria',
                'descrizione' => 'Attività di consulenza professionale',
                'prezzo' => 80.00,
                'aliquota_iva' => 22,
                'unita_misura' => 'HUR',
            ],
            [
                'codice' => 'SVIL-001',
                'nome' => 'Sviluppo software',
                'descrizione' => 'Sviluppo e implementazione software',
                'prezzo' => 500.00,
                'aliquota_iva' => 22,
                'unita_misura' => 'NR',
            ],
            [
                'codice' => 'ASSI-001',
                'nome' => 'Assistenza tecnica',
                'descrizione' => 'Supporto tecnico e manutenzione',
                'prezzo' => 50.00,
                'aliquota_iva' => 22,
                'unita_misura' => 'HUR',
            ],
        ];

        foreach ($samples as $sample) {
            // Check if already exists
            $exists = $prodottiTable->find()->where(['codice' => $sample['codice']])->first();
            if (!$exists) {
                $sample['categoria_id'] = $categoria?->id;
                $sample['is_active'] = true;
                $entity = $prodottiTable->newEntity($sample);
                $prodottiTable->save($entity);
            }
        }
    }

    /**
     * Generate product code.
     *
     * @return string
     */
    protected function _generateProductCode(): string
    {
        $prodottiTable = $this->fetchTable('Prodotti');
        $count = $prodottiTable->find()->count() + 1;

        return sprintf('PROD-%03d', $count);
    }
}
