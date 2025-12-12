<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * Abbonamenti seed.
 */
class AbbonamentiSeed extends BaseSeed
{
    public function run(): void
    {
        $now = DateTime::now();

        // Truncate table before inserting
        $table = $this->table('abbonamenti');
        $table->truncate();

        $data = [
            [
                'id' => 1,
                'tenant_id' => 1,
                'piano_id' => 3, // Pro
                'tipo' => 'annuale',
                'data_inizio' => '2025-01-01',
                'data_fine' => '2025-12-31',
                'importo' => '299.00',
                'stato' => 'attivo',
                'note' => 'Abbonamento annuale Pro - Azienda Demo',
                'created' => $now,
                'modified' => $now,
                'deleted' => null,
            ],
        ];

        $table = $this->table('abbonamenti');
        $table->insert($data)->save();
    }
}
