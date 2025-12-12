<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Behavior\TenantScopeBehavior;
use App\Model\Table\LogAttivitaTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LogAttivitaTable Test Case
 */
class LogAttivitaTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LogAttivitaTable
     */
    protected $LogAttivita;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.LogAttivita',
        'app.Tenants',
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

        // Clear any session data that might interfere with TenantScope
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }

        // Reset tenant context
        TenantScopeBehavior::setTenantContext(null, null);

        $config = $this->getTableLocator()->exists('LogAttivita') ? [] : ['className' => LogAttivitaTable::class];
        $this->LogAttivita = $this->getTableLocator()->get('LogAttivita', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->LogAttivita);
        // Reset tenant context after each test
        TenantScopeBehavior::setTenantContext(null, null);

        // Clear session
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }

        parent::tearDown();
    }

    /**
     * Test that admin of tenant 1 sees only tenant 1 logs
     *
     * @return void
     */
    public function testAdminTenant1SeesOnlyOwnLogs(): void
    {
        // Set context as admin of tenant 1
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $logs = $this->LogAttivita->find()->all();

        $this->assertCount(2, $logs, 'Admin tenant 1 should see exactly 2 logs');

        foreach ($logs as $log) {
            $this->assertEquals(1, $log->tenant_id, 'All logs should belong to tenant 1');
        }
    }

    /**
     * Test that admin of tenant 2 sees only tenant 2 logs
     *
     * @return void
     */
    public function testAdminTenant2SeesOnlyOwnLogs(): void
    {
        // Set context as admin of tenant 2
        TenantScopeBehavior::setTenantContext(2, 'admin');

        $logs = $this->LogAttivita->find()->all();

        $this->assertCount(2, $logs, 'Admin tenant 2 should see exactly 2 logs');

        foreach ($logs as $log) {
            $this->assertEquals(2, $log->tenant_id, 'All logs should belong to tenant 2');
        }
    }

    /**
     * Test that superadmin sees all logs (no tenant filter)
     *
     * @return void
     */
    public function testSuperadminSeesAllLogs(): void
    {
        // Set context as superadmin
        TenantScopeBehavior::setTenantContext(null, 'superadmin');

        $logs = $this->LogAttivita->find()->all();

        // Should see all 5 logs (2 tenant1 + 2 tenant2 + 1 system)
        $this->assertCount(5, $logs, 'Superadmin should see all 5 logs');
    }

    /**
     * Test that staff sees only their tenant logs
     *
     * @return void
     */
    public function testStaffSeesOnlyOwnTenantLogs(): void
    {
        // Set context as staff of tenant 1
        TenantScopeBehavior::setTenantContext(1, 'staff');

        $logs = $this->LogAttivita->find()->all();

        $this->assertCount(2, $logs, 'Staff tenant 1 should see exactly 2 logs');

        foreach ($logs as $log) {
            $this->assertEquals(1, $log->tenant_id, 'All logs should belong to tenant 1');
        }
    }

    /**
     * Test that user without tenant sees no logs
     *
     * @return void
     */
    public function testUserWithoutTenantSeesNoLogs(): void
    {
        // Set context without tenant (non-superadmin)
        TenantScopeBehavior::setTenantContext(null, 'user');

        $logs = $this->LogAttivita->find()->all();

        $this->assertCount(0, $logs, 'User without tenant should see no logs');
    }

    /**
     * Test that admin cannot see logs with null tenant_id
     *
     * @return void
     */
    public function testAdminCannotSeeSystemLogs(): void
    {
        // Set context as admin of tenant 1
        TenantScopeBehavior::setTenantContext(1, 'admin');

        // Try to find log with id 1005 (system log with null tenant)
        $systemLog = $this->LogAttivita->find()
            ->where(['LogAttivita.id' => 1005])
            ->first();

        $this->assertNull($systemLog, 'Admin should not see system logs (null tenant_id)');
    }

    /**
     * Test that tenant 1 admin cannot access tenant 2 logs by ID
     *
     * @return void
     */
    public function testAdminCannotAccessOtherTenantLogById(): void
    {
        // Set context as admin of tenant 1
        TenantScopeBehavior::setTenantContext(1, 'admin');

        // Try to find log with id 1003 (belongs to tenant 2)
        $otherTenantLog = $this->LogAttivita->find()
            ->where(['LogAttivita.id' => 1003])
            ->first();

        $this->assertNull($otherTenantLog, 'Admin should not access logs from other tenants');
    }

    /**
     * Test validation requires azione field
     *
     * @return void
     */
    public function testValidationRequiresAzione(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $log = $this->LogAttivita->newEntity([
            'modello' => 'Test',
        ]);

        $this->assertNotEmpty($log->getErrors(), 'Should have validation errors');
        $this->assertArrayHasKey('azione', $log->getErrors(), 'Should require azione field');
    }

    /**
     * Test that new log gets tenant_id automatically injected
     *
     * @return void
     */
    public function testNewLogGetsTenantIdInjected(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $log = $this->LogAttivita->newEntity([
            'azione' => 'test_create',
            'modello' => 'TestModel',
            'modello_id' => 999,
        ]);

        $result = $this->LogAttivita->save($log);

        $this->assertNotFalse($result, 'Log should be saved');
        $this->assertEquals(1, $result->tenant_id, 'tenant_id should be automatically set to 1');
    }

    /**
     * Test that tenant_id cannot be manipulated on save
     *
     * @return void
     */
    public function testTenantIdCannotBeManipulatedOnSave(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        // Try to save with different tenant_id
        $log = $this->LogAttivita->newEntity([
            'tenant_id' => 2, // Trying to inject different tenant
            'azione' => 'test_injection',
            'modello' => 'TestModel',
        ]);

        $result = $this->LogAttivita->save($log);

        $this->assertNotFalse($result, 'Log should be saved');
        $this->assertEquals(1, $result->tenant_id, 'tenant_id should be forced to current tenant (1), not injected value (2)');
    }

    /**
     * Test that admin cannot delete logs from other tenants
     *
     * @return void
     */
    public function testAdminCannotDeleteOtherTenantLogs(): void
    {
        // First, get the log as superadmin to have the entity
        TenantScopeBehavior::setTenantContext(null, 'superadmin');
        $otherTenantLog = $this->LogAttivita->get(1003); // Belongs to tenant 2

        // Now switch to tenant 1 admin and try to delete
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $result = $this->LogAttivita->delete($otherTenantLog);

        $this->assertFalse($result, 'Admin should not be able to delete logs from other tenants');

        // Verify log still exists
        TenantScopeBehavior::setTenantContext(null, 'superadmin');
        $stillExists = $this->LogAttivita->exists(['id' => 1003]);
        $this->assertTrue($stillExists, 'Log should still exist after failed delete attempt');
    }

    /**
     * Test that admin can delete their own tenant logs
     *
     * @return void
     */
    public function testAdminCanDeleteOwnTenantLogs(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $log = $this->LogAttivita->get(1001); // Belongs to tenant 1
        $result = $this->LogAttivita->delete($log);

        $this->assertTrue($result, 'Admin should be able to delete own tenant logs');

        // Verify log is deleted
        $exists = $this->LogAttivita->exists(['id' => 1001]);
        $this->assertFalse($exists, 'Log should be deleted');
    }

    /**
     * Test that superadmin can delete any log
     *
     * @return void
     */
    public function testSuperadminCanDeleteAnyLog(): void
    {
        TenantScopeBehavior::setTenantContext(null, 'superadmin');

        // Delete log from tenant 1
        $log1 = $this->LogAttivita->get(1001);
        $result1 = $this->LogAttivita->delete($log1);
        $this->assertTrue($result1, 'Superadmin should delete tenant 1 log');

        // Delete log from tenant 2
        $log2 = $this->LogAttivita->get(1003);
        $result2 = $this->LogAttivita->delete($log2);
        $this->assertTrue($result2, 'Superadmin should delete tenant 2 log');

        // Delete system log (null tenant)
        $log3 = $this->LogAttivita->get(1005);
        $result3 = $this->LogAttivita->delete($log3);
        $this->assertTrue($result3, 'Superadmin should delete system log');
    }

    /**
     * Test tenant filter works with ordering
     *
     * @return void
     */
    public function testTenantFilterWithOrdering(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $logs = $this->LogAttivita->find()
            ->orderBy(['LogAttivita.created' => 'DESC'])
            ->all();

        $this->assertCount(2, $logs, 'Should return only 2 logs for tenant 1');

        // Verify ordering works
        $firstLog = $logs->first();
        $this->assertEquals('update', $firstLog->azione, 'Most recent log should be update');
    }

    /**
     * Test tenant filter works with limit
     *
     * @return void
     */
    public function testTenantFilterWithLimit(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $logs = $this->LogAttivita->find()
            ->limit(1)
            ->all();

        $this->assertCount(1, $logs, 'Limit should work correctly');
        $this->assertEquals(1, $logs->first()->tenant_id, 'Log should belong to tenant 1');
    }

    /**
     * Test tenant filter with conditions
     *
     * @return void
     */
    public function testTenantFilterWithConditions(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $logs = $this->LogAttivita->find()
            ->where(['azione' => 'create'])
            ->all();

        $this->assertCount(1, $logs, 'Should find 1 create log for tenant 1');
        $this->assertEquals(1, $logs->first()->tenant_id);
        $this->assertEquals('create', $logs->first()->azione);
    }

    /**
     * Test count respects tenant filter
     *
     * @return void
     */
    public function testCountRespectsFilter(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');
        $count1 = $this->LogAttivita->find()->count();
        $this->assertEquals(2, $count1, 'Tenant 1 should have 2 logs');

        TenantScopeBehavior::setTenantContext(2, 'admin');
        $count2 = $this->LogAttivita->find()->count();
        $this->assertEquals(2, $count2, 'Tenant 2 should have 2 logs');

        TenantScopeBehavior::setTenantContext(null, 'superadmin');
        $countAll = $this->LogAttivita->find()->count();
        $this->assertEquals(5, $countAll, 'Superadmin should count all 5 logs');
    }

    /**
     * Test tenant filter with contain (associations)
     *
     * @return void
     */
    public function testTenantFilterWithContain(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $logs = $this->LogAttivita->find()
            ->contain(['Users', 'Tenants'])
            ->all();

        $this->assertCount(2, $logs, 'Should return 2 logs with associations');

        foreach ($logs as $log) {
            $this->assertEquals(1, $log->tenant_id);
        }
    }

    /**
     * Test switching tenant context
     *
     * @return void
     */
    public function testSwitchingTenantContext(): void
    {
        // Start as tenant 1
        TenantScopeBehavior::setTenantContext(1, 'admin');
        $logs1 = $this->LogAttivita->find()->all()->toArray();
        $this->assertCount(2, $logs1);

        // Switch to tenant 2
        TenantScopeBehavior::setTenantContext(2, 'admin');
        $logs2 = $this->LogAttivita->find()->all()->toArray();
        $this->assertCount(2, $logs2);

        // Verify they're different logs
        $ids1 = array_column($logs1, 'id');
        $ids2 = array_column($logs2, 'id');
        $this->assertEmpty(array_intersect($ids1, $ids2), 'Logs from different tenants should not overlap');
    }

    /**
     * Test non-existent tenant returns no logs
     *
     * @return void
     */
    public function testNonExistentTenantReturnsNoLogs(): void
    {
        TenantScopeBehavior::setTenantContext(999, 'admin');

        $logs = $this->LogAttivita->find()->all();

        $this->assertCount(0, $logs, 'Non-existent tenant should see no logs');
    }

    /**
     * Test exists() respects tenant filter
     *
     * @return void
     */
    public function testExistsRespectsFilter(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        // Log 1001 belongs to tenant 1 - should exist
        $this->assertTrue($this->LogAttivita->exists(['id' => 1001]));

        // Log 1003 belongs to tenant 2 - should not exist for tenant 1
        $this->assertFalse($this->LogAttivita->exists(['id' => 1003]));

        // Log 1005 is system log (null tenant) - should not exist for tenant 1
        $this->assertFalse($this->LogAttivita->exists(['id' => 1005]));
    }

    /**
     * Test filter by azione type for own tenant
     *
     * @return void
     */
    public function testFilterByAzioneForOwnTenant(): void
    {
        TenantScopeBehavior::setTenantContext(2, 'admin');

        // Tenant 2 has: create (Anagrafiche), delete (Prodotti)
        $createLogs = $this->LogAttivita->find()->where(['azione' => 'create'])->all();
        $this->assertCount(1, $createLogs);
        $this->assertEquals('Anagrafiche', $createLogs->first()->modello);

        $deleteLogs = $this->LogAttivita->find()->where(['azione' => 'delete'])->all();
        $this->assertCount(1, $deleteLogs);
        $this->assertEquals('Prodotti', $deleteLogs->first()->modello);
    }

    /**
     * Test that user role sees only own tenant logs
     *
     * @return void
     */
    public function testUserRoleSeesOnlyOwnTenantLogs(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'user');

        $logs = $this->LogAttivita->find()->all();

        $this->assertCount(2, $logs, 'User should see tenant 1 logs');
        foreach ($logs as $log) {
            $this->assertEquals(1, $log->tenant_id);
        }
    }

    /**
     * Test getTenantContext returns correct values
     *
     * @return void
     */
    public function testGetTenantContext(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $context = TenantScopeBehavior::getTenantContext();

        $this->assertEquals(1, $context['tenant_id']);
        $this->assertEquals('admin', $context['role']);
    }

    /**
     * Test multiple saves maintain correct tenant
     *
     * @return void
     */
    public function testMultipleSavesMaintainCorrectTenant(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        // Save first log
        $log1 = $this->LogAttivita->newEntity([
            'azione' => 'test1',
            'modello' => 'Test',
        ]);
        $result1 = $this->LogAttivita->save($log1);

        // Save second log
        $log2 = $this->LogAttivita->newEntity([
            'azione' => 'test2',
            'modello' => 'Test',
        ]);
        $result2 = $this->LogAttivita->save($log2);

        $this->assertEquals(1, $result1->tenant_id);
        $this->assertEquals(1, $result2->tenant_id);

        // Verify both are visible
        $count = $this->LogAttivita->find()->where(['modello' => 'Test'])->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test IP address and user agent are stored correctly
     *
     * @return void
     */
    public function testIpAndUserAgentStored(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $log = $this->LogAttivita->newEntity([
            'azione' => 'test',
            'modello' => 'Test',
            'ip_address' => '192.168.1.100',
            'user_agent' => 'TestBrowser/1.0',
        ]);

        $result = $this->LogAttivita->save($log);

        $this->assertEquals('192.168.1.100', $result->ip_address);
        $this->assertEquals('TestBrowser/1.0', $result->user_agent);
    }

    /**
     * Test JSON data stored correctly in dati_precedenti/dati_nuovi
     *
     * @return void
     */
    public function testJsonDataStoredCorrectly(): void
    {
        TenantScopeBehavior::setTenantContext(1, 'admin');

        $previousData = json_encode(['stato' => 'bozza', 'totale' => 100]);
        $newData = json_encode(['stato' => 'inviata', 'totale' => 100]);

        $log = $this->LogAttivita->newEntity([
            'azione' => 'update',
            'modello' => 'Fatture',
            'modello_id' => 123,
            'dati_precedenti' => $previousData,
            'dati_nuovi' => $newData,
        ]);

        $result = $this->LogAttivita->save($log);

        $this->assertEquals($previousData, $result->dati_precedenti);
        $this->assertEquals($newData, $result->dati_nuovi);
    }
}
