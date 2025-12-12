<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FatturaStatiSdiTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FatturaStatiSdiTable Test Case
 */
class FatturaStatiSdiTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FatturaStatiSdiTable
     */
    protected $FatturaStatiSdi;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.FatturaStatiSdi',
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
        $config = $this->getTableLocator()->exists('FatturaStatiSdi') ? [] : ['className' => FatturaStatiSdiTable::class];
        $this->FatturaStatiSdi = $this->getTableLocator()->get('FatturaStatiSdi', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->FatturaStatiSdi);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\FatturaStatiSdiTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\FatturaStatiSdiTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
