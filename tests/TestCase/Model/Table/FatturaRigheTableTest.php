<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FatturaRigheTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FatturaRigheTable Test Case
 */
class FatturaRigheTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FatturaRigheTable
     */
    protected $FatturaRighe;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.FatturaRighe',
        'app.Fatture',
        'app.Prodotti',
        'app.Tenants',
        'app.Anagrafiche',
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
        $config = $this->getTableLocator()->exists('FatturaRighe') ? [] : ['className' => FatturaRigheTable::class];
        $this->FatturaRighe = $this->getTableLocator()->get('FatturaRighe', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->FatturaRighe);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\FatturaRigheTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\FatturaRigheTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
