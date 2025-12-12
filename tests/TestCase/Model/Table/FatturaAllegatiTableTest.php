<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FatturaAllegatiTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FatturaAllegatiTable Test Case
 */
class FatturaAllegatiTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FatturaAllegatiTable
     */
    protected $FatturaAllegati;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.FatturaAllegati',
        'app.Fatture',
        'app.Tenants',
        'app.Anagrafiche',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('FatturaAllegati') ? [] : ['className' => FatturaAllegatiTable::class];
        $this->FatturaAllegati = $this->getTableLocator()->get('FatturaAllegati', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->FatturaAllegati);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\FatturaAllegatiTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\FatturaAllegatiTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
