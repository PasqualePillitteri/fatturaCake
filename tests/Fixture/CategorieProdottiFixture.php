<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CategorieProdottiFixture
 */
class CategorieProdottiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'categorie_prodotti';
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
                'parent_id' => null,
                'nome' => 'Servizi IT',
                'descrizione' => 'Servizi di consulenza e sviluppo IT',
                'sort_order' => 1,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 2,
                'tenant_id' => 1,
                'parent_id' => 1,
                'nome' => 'Consulenza',
                'descrizione' => 'Servizi di consulenza',
                'sort_order' => 1,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 3,
                'tenant_id' => 1,
                'parent_id' => 1,
                'nome' => 'Sviluppo Software',
                'descrizione' => 'Sviluppo software personalizzato',
                'sort_order' => 2,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
