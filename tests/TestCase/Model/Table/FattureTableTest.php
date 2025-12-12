<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FattureTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FattureTable Test Case
 */
class FattureTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FattureTable
     */
    protected $Fatture;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
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
        $config = $this->getTableLocator()->exists('Fatture') ? [] : ['className' => FattureTable::class];
        $this->Fatture = $this->getTableLocator()->get('Fatture', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Fatture);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\FattureTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\FattureTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
