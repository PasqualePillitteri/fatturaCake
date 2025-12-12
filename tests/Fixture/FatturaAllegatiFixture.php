<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FatturaAllegatiFixture
 */
class FatturaAllegatiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'fattura_allegati';
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
                'fattura_id' => 1,
                'nome_attachment' => 'Lorem ipsum dolor sit amet',
                'algoritmo_compressione' => 'Lorem ip',
                'formato_attachment' => 'Lorem ip',
                'descrizione_attachment' => 'Lorem ipsum dolor sit amet',
                'attachment' => 'Lorem ipsum dolor sit amet',
                'file_path' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-12-10 16:17:11',
                'modified' => '2025-12-10 16:17:11',
            ],
        ];
        parent::init();
    }
}
