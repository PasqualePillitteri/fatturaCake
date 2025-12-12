<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ListiniProdottiTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ListiniProdottiTable Test Case
 */
class ListiniProdottiTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ListiniProdottiTable
     */
    protected $ListiniProdotti;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.ListiniProdotti',
        'app.Listini',
        'app.Prodotti',
        'app.Tenants',
        'app.CategorieProdotti',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ListiniProdotti') ? [] : ['className' => ListiniProdottiTable::class];
        $this->ListiniProdotti = $this->getTableLocator()->get('ListiniProdotti', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ListiniProdotti);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ListiniProdottiTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ListiniProdottiTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
