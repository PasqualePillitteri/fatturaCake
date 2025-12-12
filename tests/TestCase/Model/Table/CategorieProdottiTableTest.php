<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CategorieProdottiTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CategorieProdottiTable Test Case
 */
class CategorieProdottiTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CategorieProdottiTable
     */
    protected $CategorieProdotti;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.CategorieProdotti',
        'app.Tenants',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CategorieProdotti') ? [] : ['className' => CategorieProdottiTable::class];
        $this->CategorieProdotti = $this->getTableLocator()->get('CategorieProdotti', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CategorieProdotti);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\CategorieProdottiTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\CategorieProdottiTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
