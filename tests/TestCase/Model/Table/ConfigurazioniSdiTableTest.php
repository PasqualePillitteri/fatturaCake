<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ConfigurazioniSdiTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ConfigurazioniSdiTable Test Case
 */
class ConfigurazioniSdiTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ConfigurazioniSdiTable
     */
    protected $ConfigurazioniSdi;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.ConfigurazioniSdi',
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
        $config = $this->getTableLocator()->exists('ConfigurazioniSdi') ? [] : ['className' => ConfigurazioniSdiTable::class];
        $this->ConfigurazioniSdi = $this->getTableLocator()->get('ConfigurazioniSdi', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ConfigurazioniSdi);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\ConfigurazioniSdiTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\ConfigurazioniSdiTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
