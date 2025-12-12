<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PianiFixture
 */
class PianiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'piani';

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
                'nome' => 'Piano Base',
                'descrizione' => 'Piano base per piccole aziende',
                'prezzo_mensile' => 29.00,
                'prezzo_annuale' => 290.00,
                'is_active' => 1,
                'sort_order' => 1,
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 2,
                'nome' => 'Piano Pro',
                'descrizione' => 'Piano professionale per medie aziende',
                'prezzo_mensile' => 59.00,
                'prezzo_annuale' => 590.00,
                'is_active' => 1,
                'sort_order' => 2,
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
