<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AnagraficheFixture
 */
class AnagraficheFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'anagrafiche';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'tenant_id' => 1,
                'tipo' => 'cliente',
                'denominazione' => 'Cliente Test Spa',
                'nome' => null,
                'cognome' => null,
                'codice_fiscale' => '12345678901',
                'partita_iva' => '12345678901',
                'regime_fiscale' => 'RF01',
                'indirizzo' => 'Via Roma 100',
                'numero_civico' => '100',
                'cap' => '00100',
                'comune' => 'Roma',
                'provincia' => 'RM',
                'nazione' => 'IT',
                'telefono' => '0612345678',
                'email' => 'cliente@test.it',
                'pec' => 'cliente@pec.it',
                'codice_sdi' => '0000000',
                'riferimento_amministrazione' => null,
                'split_payment' => 0,
                'note' => null,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 2,
                'tenant_id' => 1,
                'tipo' => 'fornitore',
                'denominazione' => 'Fornitore Test Srl',
                'nome' => null,
                'cognome' => null,
                'codice_fiscale' => '98765432109',
                'partita_iva' => '98765432109',
                'regime_fiscale' => 'RF01',
                'indirizzo' => 'Via Milano 50',
                'numero_civico' => '50',
                'cap' => '20100',
                'comune' => 'Milano',
                'provincia' => 'MI',
                'nazione' => 'IT',
                'telefono' => '0298765432',
                'email' => 'fornitore@test.it',
                'pec' => 'fornitore@pec.it',
                'codice_sdi' => 'ABCDEFG',
                'riferimento_amministrazione' => null,
                'split_payment' => 0,
                'note' => null,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 3,
                'tenant_id' => 2,
                'tipo' => 'cliente',
                'denominazione' => 'Cliente Altro Tenant',
                'nome' => null,
                'cognome' => null,
                'codice_fiscale' => '11111111111',
                'partita_iva' => '11111111111',
                'regime_fiscale' => 'RF01',
                'indirizzo' => 'Via Napoli 10',
                'numero_civico' => '10',
                'cap' => '80100',
                'comune' => 'Napoli',
                'provincia' => 'NA',
                'nazione' => 'IT',
                'telefono' => '0811234567',
                'email' => 'altro@test.it',
                'pec' => 'altro@pec.it',
                'codice_sdi' => '1234567',
                'riferimento_amministrazione' => null,
                'split_payment' => 0,
                'note' => null,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
