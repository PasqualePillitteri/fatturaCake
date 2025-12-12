<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ListiniFixture
 */
class ListiniFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'listini';
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
                'nome' => 'Lorem ipsum dolor sit amet',
                'descrizione' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'valuta' => 'L',
                'data_inizio' => '2025-12-10',
                'data_fine' => '2025-12-10',
                'is_default' => 1,
                'is_active' => 1,
                'created' => '2025-12-10 16:17:20',
                'modified' => '2025-12-10 16:17:20',
                'deleted' => '2025-12-10 16:17:20',
            ],
        ];
        parent::init();
    }
}
