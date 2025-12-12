<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProdottiTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProdottiTable Test Case
 */
class ProdottiTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProdottiTable
     */
    protected $Prodotti;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Prodotti',
        'app.Tenants',
        'app.CategorieProdotti',
        'app.Listini',
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
        $config = $this->getTableLocator()->exists('Prodotti') ? [] : ['className' => ProdottiTable::class];
        $this->Prodotti = $this->getTableLocator()->get('Prodotti', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Prodotti);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ProdottiTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ProdottiTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
