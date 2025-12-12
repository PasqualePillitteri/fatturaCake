<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ListiniTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ListiniTable Test Case
 */
class ListiniTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ListiniTable
     */
    protected $Listini;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Listini',
        'app.Tenants',
        'app.CategorieProdotti',
        'app.Prodotti',
        'app.ListiniProdotti',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Listini') ? [] : ['className' => ListiniTable::class];
        $this->Listini = $this->getTableLocator()->get('Listini', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Listini);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ListiniTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ListiniTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
