<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AnagraficheTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AnagraficheTable Test Case
 */
class AnagraficheTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AnagraficheTable
     */
    protected $Anagrafiche;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Anagrafiche',
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
        $config = $this->getTableLocator()->exists('Anagrafiche') ? [] : ['className' => AnagraficheTable::class];
        $this->Anagrafiche = $this->getTableLocator()->get('Anagrafiche', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Anagrafiche);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\AnagraficheTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\AnagraficheTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
