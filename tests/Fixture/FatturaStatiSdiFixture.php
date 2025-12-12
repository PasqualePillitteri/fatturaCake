<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FatturaStatiSdiFixture
 */
class FatturaStatiSdiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'fattura_stati_sdi';
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
                'stato' => 'Lorem ipsum dolor sit amet',
                'identificativo_sdi' => 'Lorem ipsum dolor sit amet',
                'data_ora_ricezione' => '2025-12-10 16:17:12',
                'messaggio' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'file_notifica' => 'Lorem ipsum dolor sit amet',
                'nome_file_notifica' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-12-10 16:17:12',
            ],
        ];
        parent::init();
    }
}
