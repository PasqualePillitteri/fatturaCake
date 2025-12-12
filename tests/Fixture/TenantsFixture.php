<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TenantsFixture
 */
class TenantsFixture extends TestFixture
{
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
                'nome' => 'Tenant Uno',
                'tipo' => 'azienda',
                'descrizione' => 'Primo tenant di test',
                'codice_fiscale' => 'TNTUNOTEST001',
                'partita_iva' => '12345678901',
                'indirizzo' => 'Via Roma 1',
                'citta' => 'Roma',
                'provincia' => 'RM',
                'cap' => '00100',
                'telefono' => '0612345678',
                'email' => 'info@tenantuno.it',
                'pec' => 'pec@tenantuno.it',
                'sito_web' => 'https://tenantuno.it',
                'logo' => null,
                'slug' => 'tenant-uno',
                'is_active' => 1,
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 2,
                'nome' => 'Tenant Due',
                'tipo' => 'azienda',
                'descrizione' => 'Secondo tenant di test',
                'codice_fiscale' => 'TNTDUETEST002',
                'partita_iva' => '98765432109',
                'indirizzo' => 'Via Milano 2',
                'citta' => 'Milano',
                'provincia' => 'MI',
                'cap' => '20100',
                'telefono' => '0298765432',
                'email' => 'info@tenantdue.it',
                'pec' => 'pec@tenantdue.it',
                'sito_web' => 'https://tenantdue.it',
                'logo' => null,
                'slug' => 'tenant-due',
                'is_active' => 1,
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
