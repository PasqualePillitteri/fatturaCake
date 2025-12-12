<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AbbonamentiFixture
 */
class AbbonamentiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'abbonamenti';

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
                'piano_id' => 1,
                'tipo' => 'annuale',
                'data_inizio' => '2025-01-01',
                'data_fine' => '2025-12-31',
                'importo' => 290.00,
                'stato' => 'attivo',
                'note' => null,
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 2,
                'tenant_id' => 2,
                'piano_id' => 2,
                'tipo' => 'annuale',
                'data_inizio' => '2025-01-01',
                'data_fine' => '2025-12-31',
                'importo' => 590.00,
                'stato' => 'attivo',
                'note' => null,
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
