<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TenantsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TenantsTable Test Case
 */
class TenantsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TenantsTable
     */
    protected $Tenants;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Tenants',
        'app.ConfigurazioniSdi',
        'app.Anagrafiche',
        'app.CategorieProdotti',
        'app.Fatture',
        'app.Listini',
        'app.LogAttivita',
        'app.Prodotti',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Tenants') ? [] : ['className' => TenantsTable::class];
        $this->Tenants = $this->getTableLocator()->get('Tenants', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Tenants);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\TenantsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\TenantsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
