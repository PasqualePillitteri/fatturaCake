<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ListiniProdottiFixture
 */
class ListiniProdottiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'listini_prodotti';
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
                'listino_id' => 1,
                'prodotto_id' => 1,
                'prezzo' => 1.5,
                'prezzo_minimo' => 1.5,
                'sconto_massimo' => 1.5,
                'created' => '2025-12-10 16:17:20',
                'modified' => '2025-12-10 16:17:20',
            ],
        ];
        parent::init();
    }
}
