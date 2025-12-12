<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LogAttivitaFixture
 */
class LogAttivitaFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'log_attivita';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            // Log tenant 1
            [
                'id' => 1001,
                'tenant_id' => 1,
                'user_id' => 2,
                'azione' => 'create',
                'modello' => 'Fatture',
                'modello_id' => 1,
                'dati_precedenti' => null,
                'dati_nuovi' => '{"numero": "2025/001"}',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'created' => '2025-01-01 10:00:00',
            ],
            [
                'id' => 1002,
                'tenant_id' => 1,
                'user_id' => 2,
                'azione' => 'update',
                'modello' => 'Fatture',
                'modello_id' => 1,
                'dati_precedenti' => '{"stato": "bozza"}',
                'dati_nuovi' => '{"stato": "inviata"}',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'created' => '2025-01-01 11:00:00',
            ],
            // Log tenant 2
            [
                'id' => 1003,
                'tenant_id' => 2,
                'user_id' => 3,
                'azione' => 'create',
                'modello' => 'Anagrafiche',
                'modello_id' => 1,
                'dati_precedenti' => null,
                'dati_nuovi' => '{"ragione_sociale": "Cliente Test"}',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'PHPUnit',
                'created' => '2025-01-01 12:00:00',
            ],
            [
                'id' => 1004,
                'tenant_id' => 2,
                'user_id' => 3,
                'azione' => 'delete',
                'modello' => 'Prodotti',
                'modello_id' => 5,
                'dati_precedenti' => '{"nome": "Prodotto eliminato"}',
                'dati_nuovi' => null,
                'ip_address' => '192.168.1.1',
                'user_agent' => 'PHPUnit',
                'created' => '2025-01-01 13:00:00',
            ],
            // Log di sistema (senza tenant)
            [
                'id' => 1005,
                'tenant_id' => null,
                'user_id' => 1,
                'azione' => 'system',
                'modello' => 'System',
                'modello_id' => null,
                'dati_precedenti' => null,
                'dati_nuovi' => '{"evento": "backup completato"}',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Cron',
                'created' => '2025-01-01 14:00:00',
            ],
        ];
        parent::init();
    }
}
