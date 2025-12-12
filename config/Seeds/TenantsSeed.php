<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * Tenants seed.
 */
class TenantsSeed extends BaseSeed
{
    public function run(): void
    {
        $now = DateTime::now();

        $data = [
            [
                'id' => 1,
                'nome' => 'Azienda Demo',
                'tipo' => null,
                'descrizione' => null,
                'codice_fiscale' => 'DMOCMP80A01H501Z',
                'partita_iva' => '12345678901',
                'indirizzo' => 'Via Roma 1',
                'citta' => 'Roma',
                'provincia' => 'RM',
                'cap' => '00100',
                'telefono' => null,
                'email' => 'info@aziendademo.it',
                'pec' => null,
                'sito_web' => null,
                'logo' => null,
                'slug' => 'azienda-demo',
                'is_active' => true,
                'created' => $now,
                'modified' => $now,
            ],
        ];

        $table = $this->table('tenants');
        $table->insert($data)->save();
    }
}
