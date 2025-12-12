<?php
declare(strict_types=1);

namespace App\Test\TestCase\Integration;

use App\Model\Behavior\TenantScopeBehavior;
use App\Model\Table\AnagraficheTable;
use App\Model\Table\CategorieProdottiTable;
use App\Model\Table\FatturaRigheTable;
use App\Model\Table\FattureTable;
use App\Model\Table\ProdottiTable;
use App\Model\Table\TenantsTable;
use Cake\TestSuite\TestCase;

/**
 * Test di Integrazione per la Creazione di Fatture
 *
 * Questo test verifica l'intero flusso di creazione di una fattura,
 * incluse tutte le anagrafiche accessorie necessarie.
 */
class FatturaCreationTest extends TestCase
{
    /**
     * @var \App\Model\Table\TenantsTable
     */
    protected TenantsTable $Tenants;

    /**
     * @var \App\Model\Table\AnagraficheTable
     */
    protected AnagraficheTable $Anagrafiche;

    /**
     * @var \App\Model\Table\CategorieProdottiTable
     */
    protected CategorieProdottiTable $CategorieProdotti;

    /**
     * @var \App\Model\Table\ProdottiTable
     */
    protected ProdottiTable $Prodotti;

    /**
     * @var \App\Model\Table\FattureTable
     */
    protected FattureTable $Fatture;

    /**
     * @var \App\Model\Table\FatturaRigheTable
     */
    protected FatturaRigheTable $FatturaRighe;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Tenants',
        'app.Anagrafiche',
        'app.CategorieProdotti',
        'app.Prodotti',
        'app.Fatture',
        'app.FatturaRighe',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set tenant context as admin - il behavior inietterà automaticamente tenant_id
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $this->Tenants = $this->getTableLocator()->get('Tenants');
        $this->Anagrafiche = $this->getTableLocator()->get('Anagrafiche');
        $this->CategorieProdotti = $this->getTableLocator()->get('CategorieProdotti');
        $this->Prodotti = $this->getTableLocator()->get('Prodotti');
        $this->Fatture = $this->getTableLocator()->get('Fatture');
        $this->FatturaRighe = $this->getTableLocator()->get('FatturaRighe');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Reset tenant context
        TenantScopeBehavior::setTenantContext(null, null);

        unset(
            $this->Tenants,
            $this->Anagrafiche,
            $this->CategorieProdotti,
            $this->Prodotti,
            $this->Fatture,
            $this->FatturaRighe
        );

        parent::tearDown();
    }

    /**
     * Test che verifica la presenza del tenant di test nelle fixture
     *
     * @return void
     */
    public function testTenantExists(): void
    {
        // Per leggere i tenants serve accesso superadmin
        TenantScopeBehavior::setTenantContext(1, 'superadmin');
        $this->getTableLocator()->clear();
        $tenants = $this->getTableLocator()->get('Tenants');

        $tenant = $tenants->get(1);

        $this->assertNotNull($tenant);
        $this->assertEquals('Tenant Uno', $tenant->nome);
        $this->assertEquals('12345678901', $tenant->partita_iva);

        // Reset al contesto normale
        TenantScopeBehavior::setTenantContext(1, 'admin');
    }

    /**
     * Test creazione di una nuova anagrafica cliente
     *
     * @return void
     */
    public function testCreateAnagrafica(): void
    {
        $data = [
            'tenant_id' => 1,
            'tipo' => 'cliente',
            'denominazione' => 'Nuovo Cliente Test Srl',
            'partita_iva' => '55555555555',
            'codice_fiscale' => '55555555555',
            'regime_fiscale' => 'RF01',
            'indirizzo' => 'Via Nuova 123',
            'cap' => '00200',
            'comune' => 'Roma',
            'provincia' => 'RM',
            'nazione' => 'IT',
            'email' => 'nuovo@cliente.it',
            'pec' => 'nuovo@pec.it',
            'codice_sdi' => 'XXXXXXX',
            'split_payment' => false,
            'is_active' => true,
        ];

        $anagrafica = $this->Anagrafiche->newEntity($data);
        $result = $this->Anagrafiche->save($anagrafica);

        $this->assertNotFalse($result, 'Salvataggio anagrafica fallito: ' . json_encode($anagrafica->getErrors()));
        $this->assertNotNull($result->id);
        $this->assertEquals('Nuovo Cliente Test Srl', $result->denominazione);
        $this->assertEquals(1, $result->tenant_id);
    }

    /**
     * Test creazione di una categoria prodotti
     *
     * @return void
     */
    public function testCreateCategoriaProdotti(): void
    {
        $data = [
            'tenant_id' => 1,
            'parent_id' => null,
            'nome' => 'Nuova Categoria Test',
            'descrizione' => 'Descrizione della nuova categoria',
            'sort_order' => 10,
            'is_active' => true,
        ];

        $categoria = $this->CategorieProdotti->newEntity($data);
        $result = $this->CategorieProdotti->save($categoria);

        $this->assertNotFalse($result, 'Salvataggio categoria fallito: ' . json_encode($categoria->getErrors()));
        $this->assertNotNull($result->id);
        $this->assertEquals('Nuova Categoria Test', $result->nome);
    }

    /**
     * Test creazione di un prodotto
     *
     * @return void
     */
    public function testCreateProdotto(): void
    {
        $data = [
            'tenant_id' => 1,
            'categoria_id' => 1,
            'tipo' => 'servizio',
            'codice' => 'TEST001',
            'nome' => 'Prodotto Test',
            'descrizione' => 'Descrizione prodotto test',
            'unita_misura' => 'pz',
            'prezzo_vendita' => 75.00,
            'aliquota_iva' => 22.00,
            'is_active' => true,
        ];

        $prodotto = $this->Prodotti->newEntity($data);
        $result = $this->Prodotti->save($prodotto);

        $this->assertNotFalse($result, 'Salvataggio prodotto fallito: ' . json_encode($prodotto->getErrors()));
        $this->assertNotNull($result->id);
        $this->assertEquals('Prodotto Test', $result->nome);
        $this->assertEquals(75.00, $result->prezzo_vendita);
    }

    /**
     * Test creazione di una fattura senza righe
     *
     * @return void
     */
    public function testCreateFatturaSenzaRighe(): void
    {
        $data = [
            'tenant_id' => 1,
            'anagrafica_id' => 1,
            'tipo_documento' => 'TD01',
            'direzione' => 'emessa',
            'numero' => '100',
            'data' => '2025-12-12',
            'anno' => 2025,
            'divisa' => 'EUR',
            'imponibile_totale' => 0.00,
            'iva_totale' => 0.00,
            'totale_documento' => 0.00,
            'bollo_virtuale' => false,
            'cassa_previdenziale' => false,
            'esigibilita_iva' => 'I',
            'condizioni_pagamento' => 'TP02',
            'modalita_pagamento' => 'MP05',
            'stato_sdi' => 'bozza',
            'is_active' => true,
        ];

        $fattura = $this->Fatture->newEntity($data);
        $result = $this->Fatture->save($fattura);

        $this->assertNotFalse($result, 'Salvataggio fattura fallito: ' . json_encode($fattura->getErrors()));
        $this->assertNotNull($result->id);
        $this->assertEquals('100', $result->numero);
        $this->assertEquals(2025, $result->anno);
    }

    /**
     * Test creazione di una riga fattura
     *
     * @return void
     */
    public function testCreateRigaFattura(): void
    {
        // Crea una riga - usiamo accessibleFields per permettere fattura_id
        $nuovaRiga = $this->FatturaRighe->newEntity([
            'fattura_id' => 1,
            'prodotto_id' => 1,
            'numero_linea' => 10,
            'descrizione' => 'Nuova riga di test',
            'quantita' => 2.00,
            'unita_misura' => 'pz',
            'prezzo_unitario' => 100.00,
            'prezzo_totale' => 200.00,
            'aliquota_iva' => 22.00,
            'ritenuta' => false,
            'sort_order' => 10,
        ], ['accessibleFields' => ['fattura_id' => true]]);

        $result = $this->FatturaRighe->save($nuovaRiga);

        $this->assertNotFalse($result, 'Salvataggio riga fallito: ' . json_encode($nuovaRiga->getErrors()));
        $this->assertNotNull($result->id);
        $this->assertEquals(200.00, $result->prezzo_totale);
        $this->assertEquals(1, $result->fattura_id);
    }

    /**
     * Test creazione fattura completa con righe (workflow end-to-end)
     *
     * @return void
     */
    public function testCreateFatturaCompletaConRighe(): void
    {
        // 1. Crea la fattura
        $fatturaData = [
            'tenant_id' => 1,
            'anagrafica_id' => 1,
            'tipo_documento' => 'TD01',
            'direzione' => 'emessa',
            'numero' => '200',
            'data' => '2025-12-12',
            'anno' => 2025,
            'divisa' => 'EUR',
            'imponibile_totale' => 1500.00,
            'iva_totale' => 330.00,
            'totale_documento' => 1830.00,
            'bollo_virtuale' => false,
            'cassa_previdenziale' => false,
            'esigibilita_iva' => 'I',
            'condizioni_pagamento' => 'TP02',
            'modalita_pagamento' => 'MP05',
            'stato_sdi' => 'bozza',
            'is_active' => true,
            'fattura_righe' => [
                [
                    'numero_linea' => 1,
                    'prodotto_id' => 1,
                    'descrizione' => 'Consulenza IT - 10 ore',
                    'quantita' => 10.00,
                    'unita_misura' => 'ore',
                    'prezzo_unitario' => 50.00,
                    'prezzo_totale' => 500.00,
                    'aliquota_iva' => 22.00,
                    'ritenuta' => false,
                    'sort_order' => 1,
                ],
                [
                    'numero_linea' => 2,
                    'prodotto_id' => 2,
                    'descrizione' => 'Sviluppo Software - 10 ore',
                    'quantita' => 10.00,
                    'unita_misura' => 'ore',
                    'prezzo_unitario' => 100.00,
                    'prezzo_totale' => 1000.00,
                    'aliquota_iva' => 22.00,
                    'ritenuta' => false,
                    'sort_order' => 2,
                ],
            ],
        ];

        $fattura = $this->Fatture->newEntity($fatturaData, [
            'associated' => ['FatturaRighe'],
        ]);

        $result = $this->Fatture->save($fattura, [
            'associated' => ['FatturaRighe'],
        ]);

        $this->assertNotFalse($result, 'Salvataggio fattura completa fallito: ' . json_encode($fattura->getErrors()));
        $this->assertNotNull($result->id);
        $this->assertEquals('200', $result->numero);

        // Verifica che le righe siano state salvate
        $this->assertCount(2, $result->fattura_righe);
        $this->assertEquals(500.00, $result->fattura_righe[0]->prezzo_totale);
        $this->assertEquals(1000.00, $result->fattura_righe[1]->prezzo_totale);

        // Carica la fattura con le righe per verifica
        $fatturaCaricata = $this->Fatture->get($result->id, contain: ['FatturaRighe']);
        $this->assertCount(2, $fatturaCaricata->fattura_righe);
    }

    /**
     * Test validazione calcolo importi fattura
     *
     * @return void
     */
    public function testCalcoloImportiFattura(): void
    {
        $righe = [
            ['quantita' => 10, 'prezzo_unitario' => 50.00, 'aliquota_iva' => 22.00],
            ['quantita' => 5, 'prezzo_unitario' => 100.00, 'aliquota_iva' => 22.00],
        ];

        $imponibile = 0;
        $iva = 0;

        foreach ($righe as $riga) {
            $prezzoTotaleRiga = $riga['quantita'] * $riga['prezzo_unitario'];
            $ivaRiga = $prezzoTotaleRiga * ($riga['aliquota_iva'] / 100);
            $imponibile += $prezzoTotaleRiga;
            $iva += $ivaRiga;
        }

        $totale = $imponibile + $iva;

        // Riga 1: 10 * 50 = 500, IVA = 110
        // Riga 2: 5 * 100 = 500, IVA = 110
        // Totale imponibile: 1000, IVA: 220, Totale: 1220

        $this->assertEquals(1000.00, $imponibile);
        $this->assertEquals(220.00, $iva);
        $this->assertEquals(1220.00, $totale);
    }

    /**
     * Test vincolo unicità numero fattura per tenant/anno/direzione
     *
     * @return void
     */
    public function testUnicityConstraintNumeroFattura(): void
    {
        // Prova a creare una fattura con lo stesso numero/anno/direzione della fixture
        $data = [
            'tenant_id' => 1,
            'anagrafica_id' => 1,
            'tipo_documento' => 'TD01',
            'direzione' => 'emessa',
            'numero' => '1', // Già esistente nella fixture
            'data' => '2025-12-12',
            'anno' => 2025, // Stesso anno della fixture
            'divisa' => 'EUR',
            'imponibile_totale' => 100.00,
            'iva_totale' => 22.00,
            'totale_documento' => 122.00,
            'bollo_virtuale' => false,
            'cassa_previdenziale' => false,
            'esigibilita_iva' => 'I',
            'condizioni_pagamento' => 'TP02',
            'modalita_pagamento' => 'MP05',
            'stato_sdi' => 'bozza',
            'is_active' => true,
        ];

        $fattura = $this->Fatture->newEntity($data);

        // Il vincolo di unicità può essere catturato dalle buildRules o dal DB
        // In entrambi i casi, verifichiamo che la fattura duplicata non venga salvata
        try {
            $result = $this->Fatture->save($fattura);
            // Se arriviamo qui, il save ha restituito false (buildRules)
            $this->assertFalse($result, 'La fattura duplicata non dovrebbe essere salvata');
        } catch (\Cake\Database\Exception\QueryException $e) {
            // Il database ha rilevato la duplicazione - comportamento corretto
            $this->assertStringContainsString('Duplicate entry', $e->getMessage());
        }
    }

    /**
     * Test che verifica che le associazioni tra fattura e anagrafica siano corrette
     *
     * @return void
     */
    public function testAssociazioniCorrecte(): void
    {
        // Verifica che la fattura con id=1 esista e abbia le associazioni corrette
        $fattura = $this->Fatture->get(1, contain: ['FatturaRighe']);
        $this->assertNotNull($fattura);
        $this->assertEquals(1, $fattura->anagrafica_id);

        // Verifica l'anagrafica separatamente
        $anagrafica = $this->Anagrafiche->get($fattura->anagrafica_id);
        $this->assertNotNull($anagrafica);
        $this->assertEquals('Cliente Test Spa', $anagrafica->denominazione);

        // Verifica le righe
        $this->assertGreaterThanOrEqual(2, count($fattura->fattura_righe));
    }

    /**
     * Test creazione fattura con nota di credito (TD04)
     *
     * @return void
     */
    public function testCreateNotaDiCredito(): void
    {
        $data = [
            'tenant_id' => 1,
            'anagrafica_id' => 1,
            'tipo_documento' => 'TD04', // Nota di credito
            'direzione' => 'emessa',
            'numero' => 'NC-001', // Numero univoco per nota credito
            'data' => '2025-12-12',
            'anno' => 2025,
            'divisa' => 'EUR',
            'imponibile_totale' => 500.00,
            'iva_totale' => 110.00,
            'totale_documento' => 610.00,
            'bollo_virtuale' => false,
            'cassa_previdenziale' => false,
            'esigibilita_iva' => 'I',
            'condizioni_pagamento' => 'TP02',
            'modalita_pagamento' => 'MP05',
            'stato_sdi' => 'bozza',
            'is_active' => true,
        ];

        $fattura = $this->Fatture->newEntity($data);
        $result = $this->Fatture->save($fattura);

        $this->assertNotFalse($result, 'Salvataggio nota di credito fallito: ' . json_encode($fattura->getErrors()));
        $this->assertEquals('TD04', $result->tipo_documento);
    }

    /**
     * Test creazione fattura ricevuta
     *
     * @return void
     */
    public function testCreateFatturaRicevuta(): void
    {
        $data = [
            'tenant_id' => 1,
            'anagrafica_id' => 2, // Fornitore
            'tipo_documento' => 'TD01',
            'direzione' => 'ricevuta',
            'numero' => 'FT-001',
            'data' => '2025-12-12',
            'anno' => 2025,
            'divisa' => 'EUR',
            'imponibile_totale' => 800.00,
            'iva_totale' => 176.00,
            'totale_documento' => 976.00,
            'bollo_virtuale' => false,
            'cassa_previdenziale' => false,
            'esigibilita_iva' => 'I',
            'condizioni_pagamento' => 'TP02',
            'modalita_pagamento' => 'MP05',
            'stato_sdi' => 'ricevuta',
            'is_active' => true,
        ];

        $fattura = $this->Fatture->newEntity($data);
        $result = $this->Fatture->save($fattura);

        $this->assertNotFalse($result, 'Salvataggio fattura ricevuta fallito: ' . json_encode($fattura->getErrors()));
        $this->assertEquals('ricevuta', $result->direzione);
    }

    /**
     * Test isolamento multi-tenant: non deve vedere anagrafiche di altri tenant
     *
     * @return void
     */
    public function testIsolamentoMultiTenant(): void
    {
        // Imposta il contesto come utente normale del tenant 1
        TenantScopeBehavior::setTenantContext(1, 'admin');

        // Ricarica le tabelle per applicare il nuovo contesto
        $this->getTableLocator()->clear();
        $anagrafiche = $this->getTableLocator()->get('Anagrafiche');

        // Dovrebbe vedere solo le anagrafiche del tenant 1
        $results = $anagrafiche->find()->all();

        foreach ($results as $anagrafica) {
            $this->assertEquals(1, $anagrafica->tenant_id,
                'Trovata anagrafica di un altro tenant: ID=' . $anagrafica->id);
        }
    }

    /**
     * Test workflow completo: creazione di tutte le entità da zero
     *
     * @return void
     */
    public function testFullWorkflowCreazioneFattura(): void
    {
        // 1. Verifica che il tenant esista (superadmin per accedere alla tabella tenants)
        TenantScopeBehavior::setTenantContext(1, 'superadmin');
        $this->getTableLocator()->clear();
        $tenants = $this->getTableLocator()->get('Tenants');
        $tenant = $tenants->get(1);
        $this->assertNotNull($tenant);

        // Reset al contesto admin
        TenantScopeBehavior::setTenantContext(1, 'admin');
        $this->getTableLocator()->clear();
        $this->CategorieProdotti = $this->getTableLocator()->get('CategorieProdotti');
        $this->Prodotti = $this->getTableLocator()->get('Prodotti');
        $this->Anagrafiche = $this->getTableLocator()->get('Anagrafiche');
        $this->Fatture = $this->getTableLocator()->get('Fatture');

        // 2. Crea una nuova categoria prodotti
        $categoriaData = [
            'tenant_id' => 1,
            'nome' => 'Categoria Workflow Test',
            'descrizione' => 'Categoria creata durante il test workflow',
            'is_active' => true,
        ];
        $categoria = $this->CategorieProdotti->newEntity($categoriaData);
        $categoria = $this->CategorieProdotti->saveOrFail($categoria);

        // 3. Crea un nuovo prodotto nella categoria
        $prodottoData = [
            'tenant_id' => 1,
            'categoria_id' => $categoria->id,
            'tipo' => 'servizio',
            'codice' => 'WF001',
            'nome' => 'Servizio Workflow Test',
            'descrizione' => 'Servizio creato durante il test workflow',
            'unita_misura' => 'ore',
            'prezzo_vendita' => 80.00,
            'aliquota_iva' => 22.00,
            'is_active' => true,
        ];
        $prodotto = $this->Prodotti->newEntity($prodottoData);
        $prodotto = $this->Prodotti->saveOrFail($prodotto);

        // 4. Crea una nuova anagrafica cliente
        $anagraficaData = [
            'tenant_id' => 1,
            'tipo' => 'cliente',
            'denominazione' => 'Cliente Workflow Test Srl',
            'partita_iva' => '77777777777',
            'codice_fiscale' => '77777777777',
            'regime_fiscale' => 'RF01',
            'indirizzo' => 'Via Workflow 1',
            'cap' => '00300',
            'comune' => 'Firenze',
            'provincia' => 'FI',
            'nazione' => 'IT',
            'pec' => 'workflow@pec.it',
            'codice_sdi' => 'WFTEST1',
            'split_payment' => false,
            'is_active' => true,
        ];
        $anagrafica = $this->Anagrafiche->newEntity($anagraficaData);
        $anagrafica = $this->Anagrafiche->saveOrFail($anagrafica);

        // 5. Crea la fattura con righe
        $fatturaData = [
            'tenant_id' => 1,
            'anagrafica_id' => $anagrafica->id,
            'tipo_documento' => 'TD01',
            'direzione' => 'emessa',
            'numero' => '999',
            'data' => '2025-12-12',
            'anno' => 2025,
            'divisa' => 'EUR',
            'imponibile_totale' => 800.00,
            'iva_totale' => 176.00,
            'totale_documento' => 976.00,
            'bollo_virtuale' => false,
            'cassa_previdenziale' => false,
            'esigibilita_iva' => 'I',
            'condizioni_pagamento' => 'TP02',
            'modalita_pagamento' => 'MP05',
            'stato_sdi' => 'bozza',
            'is_active' => true,
            'fattura_righe' => [
                [
                    'numero_linea' => 1,
                    'prodotto_id' => $prodotto->id,
                    'descrizione' => $prodotto->descrizione,
                    'quantita' => 10.00,
                    'unita_misura' => $prodotto->unita_misura,
                    'prezzo_unitario' => $prodotto->prezzo_vendita,
                    'prezzo_totale' => 800.00,
                    'aliquota_iva' => $prodotto->aliquota_iva,
                    'ritenuta' => false,
                    'sort_order' => 1,
                ],
            ],
        ];

        $fattura = $this->Fatture->newEntity($fatturaData, [
            'associated' => ['FatturaRighe'],
        ]);

        $result = $this->Fatture->save($fattura, [
            'associated' => ['FatturaRighe'],
        ]);

        // Verifiche finali
        $this->assertNotFalse($result, 'Workflow completo fallito: ' . json_encode($fattura->getErrors()));
        $this->assertNotNull($result->id);
        $this->assertEquals('999', $result->numero);
        $this->assertCount(1, $result->fattura_righe);

        // Verifica caricamento completo con tutte le associazioni
        $fatturaCompleta = $this->Fatture->get($result->id, contain: ['FatturaRighe']);

        // Verifica anagrafica separatamente
        $anagraficaCaricata = $this->Anagrafiche->get($fatturaCompleta->anagrafica_id);
        $this->assertNotNull($anagraficaCaricata, 'Anagrafica non caricata');
        $this->assertEquals('Cliente Workflow Test Srl', $anagraficaCaricata->denominazione);

        // Verifica prodotto della riga separatamente
        $prodottoCaricato = $this->Prodotti->get($fatturaCompleta->fattura_righe[0]->prodotto_id);
        $this->assertNotNull($prodottoCaricato, 'Prodotto non caricato');
        $this->assertEquals('Servizio Workflow Test', $prodottoCaricato->nome);
    }

    /**
     * Test validazione campi obbligatori fattura
     *
     * @return void
     */
    public function testValidazioneCampiObbligatoriFattura(): void
    {
        // Fattura senza campi obbligatori
        $data = [
            'tenant_id' => 1,
            // Mancano: anagrafica_id, tipo_documento, direzione, numero, data, anno, etc.
        ];

        $fattura = $this->Fatture->newEntity($data);

        $errors = $fattura->getErrors();

        $this->assertNotEmpty($errors, 'Dovrebbero esserci errori di validazione');
        $this->assertArrayHasKey('numero', $errors, 'Campo numero dovrebbe essere obbligatorio');
        $this->assertArrayHasKey('data', $errors, 'Campo data dovrebbe essere obbligatorio');
        $this->assertArrayHasKey('anno', $errors, 'Campo anno dovrebbe essere obbligatorio');
    }
}
