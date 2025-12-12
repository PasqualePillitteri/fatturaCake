<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Behavior;

use App\Model\Behavior\AuditLogBehavior;
use App\Model\Behavior\TenantScopeBehavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Behavior\AuditLogBehavior Test Case
 */
class AuditLogBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.LogAttivita',
        'app.Tenants',
        'app.Users',
        'app.Prodotti',
        'app.CategorieProdotti',
    ];

    /**
     * Test table with AuditLog behavior
     *
     * @var \Cake\ORM\Table
     */
    protected Table $TestTable;

    /**
     * LogAttivita table
     *
     * @var \App\Model\Table\LogAttivitaTable
     */
    protected $LogAttivita;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set tenant context first (required for TenantScope behavior)
        // Use 'admin' role so tenant_id is automatically injected
        TenantScopeBehavior::setTenantContext(1, 'admin');

        // Get Prodotti table which should have AuditLog behavior
        $this->TestTable = TableRegistry::getTableLocator()->get('Prodotti');

        // Get LogAttivita table
        $this->LogAttivita = TableRegistry::getTableLocator()->get('LogAttivita');

        // Set user context for audit logging
        // Use user_id=2 which belongs to tenant_id=1 (admin_tenant1 from fixture)
        AuditLogBehavior::setUserContext([
            'user_id' => 2,
            'tenant_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit Test',
        ]);
    }

    /**
     * Helper to count logs without tenant filtering
     *
     * @return int
     */
    protected function countAllLogs(): int
    {
        $originalContext = TenantScopeBehavior::getTenantContext();
        TenantScopeBehavior::setTenantContext(null, 'superadmin');
        $count = $this->LogAttivita->find()->count();
        TenantScopeBehavior::setTenantContext($originalContext['tenant_id'], $originalContext['role']);
        return $count;
    }

    /**
     * Helper to find log by criteria without tenant filtering
     *
     * @param array $conditions Query conditions
     * @return \App\Model\Entity\LogAttivitum|null
     */
    protected function findLog(array $conditions)
    {
        $originalContext = TenantScopeBehavior::getTenantContext();
        TenantScopeBehavior::setTenantContext(null, 'superadmin');
        $log = $this->LogAttivita->find()
            ->where($conditions)
            ->orderBy(['id' => 'DESC'])
            ->first();
        TenantScopeBehavior::setTenantContext($originalContext['tenant_id'], $originalContext['role']);
        return $log;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TestTable);
        unset($this->LogAttivita);

        AuditLogBehavior::setUserContext([
            'user_id' => null,
            'tenant_id' => null,
            'ip_address' => null,
            'user_agent' => null,
        ]);
        TenantScopeBehavior::setTenantContext(null, null);
        TableRegistry::getTableLocator()->clear();

        parent::tearDown();
    }

    /**
     * Get valid product data for testing
     *
     * @param array $overrides Data to override defaults
     * @return array
     */
    protected function getValidProductData(array $overrides = []): array
    {
        return array_merge([
            'tipo' => 'servizio',
            'codice' => 'TEST' . uniqid(),
            'nome' => 'Test Product',
            'prezzo_vendita' => 100.00,
            'prezzo_ivato' => false,
            'aliquota_iva' => 22.00,
            'soggetto_ritenuta' => false,
            'gestione_magazzino' => false,
            'giacenza' => 0,
            'sort_order' => 1,
        ], $overrides);
    }

    /**
     * Test setUserContext and getUserContext
     *
     * @return void
     */
    public function testSetAndGetUserContext(): void
    {
        AuditLogBehavior::setUserContext([
            'user_id' => 5,
            'tenant_id' => 3,
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Test Agent',
        ]);

        $context = AuditLogBehavior::getUserContext();

        $this->assertEquals(5, $context['user_id']);
        $this->assertEquals(3, $context['tenant_id']);
        $this->assertEquals('192.168.1.1', $context['ip_address']);
        $this->assertEquals('Test Agent', $context['user_agent']);
    }

    /**
     * Test that create operation is logged
     *
     * @return void
     */
    public function testCreateOperationIsLogged(): void
    {
        $initialCount = $this->countAllLogs();

        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'TEST001',
            'nome' => 'Test Product',
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Entity should be saved: ' . json_encode($entity->getErrors()));

        $newCount = $this->countAllLogs();
        $this->assertEquals($initialCount + 1, $newCount, 'A log entry should be created');

        $log = $this->findLog(['modello' => 'Prodotti', 'azione' => 'create', 'modello_id' => $result->id]);

        $this->assertNotNull($log, 'Log entry should exist');
        $this->assertEquals('create', $log->azione);
        $this->assertEquals('Prodotti', $log->modello);
        $this->assertEquals($result->id, $log->modello_id);
        $this->assertEquals(2, $log->user_id);
        $this->assertEquals(1, $log->tenant_id);
        $this->assertEquals('127.0.0.1', $log->ip_address);
        $this->assertNotNull($log->dati_nuovi);

        $newData = json_decode($log->dati_nuovi, true);
        $this->assertEquals('TEST001', $newData['codice']);
        $this->assertEquals('Test Product', $newData['nome']);
    }

    /**
     * Test that update operation is logged
     *
     * @return void
     */
    public function testUpdateOperationIsLogged(): void
    {
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'UPDATE001',
            'nome' => 'Original Name',
            'prezzo_vendita' => 50.00,
        ]));
        $saved = $this->TestTable->save($entity);
        $this->assertNotFalse($saved, 'Initial save failed');

        $initialCount = $this->countAllLogs();

        $reloaded = $this->TestTable->get($saved->id);
        $reloaded->nome = 'Updated Name';
        $reloaded->prezzo_vendita = 75.00;

        $result = $this->TestTable->save($reloaded);
        $this->assertNotFalse($result, 'Update save failed');

        $newCount = $this->countAllLogs();
        $this->assertEquals($initialCount + 1, $newCount, 'An update log entry should be created');

        $log = $this->findLog(['modello' => 'Prodotti', 'azione' => 'update', 'modello_id' => $reloaded->id]);

        $this->assertNotNull($log);
        $this->assertEquals('update', $log->azione);

        $newData = json_decode($log->dati_nuovi, true);
        $this->assertArrayHasKey('nome', $newData);
        $this->assertEquals('Updated Name', $newData['nome']);
    }

    /**
     * Test that delete operation is logged
     *
     * @return void
     */
    public function testDeleteOperationIsLogged(): void
    {
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'DELETE001',
            'nome' => 'To Be Deleted',
            'prezzo_vendita' => 25.00,
        ]));
        $saved = $this->TestTable->save($entity);
        $this->assertNotFalse($saved, 'Initial save failed');
        $entityId = $saved->id;

        $initialCount = $this->countAllLogs();

        $result = $this->TestTable->delete($saved, ['purge' => true]);
        $this->assertTrue($result);

        $newCount = $this->countAllLogs();
        $this->assertEquals($initialCount + 1, $newCount, 'A delete log entry should be created');

        $log = $this->findLog(['modello' => 'Prodotti', 'azione' => 'delete', 'modello_id' => $entityId]);

        $this->assertNotNull($log);
        $this->assertEquals('delete', $log->azione);
        $this->assertNotNull($log->dati_precedenti);
        $this->assertNull($log->dati_nuovi);

        $previousData = json_decode($log->dati_precedenti, true);
        $this->assertEquals('DELETE001', $previousData['codice']);
        $this->assertEquals('To Be Deleted', $previousData['nome']);
    }

    /**
     * Test that excluded fields are not logged
     *
     * @return void
     */
    public function testExcludedFieldsNotLogged(): void
    {
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'EXCLUDE001',
            'nome' => 'Test Exclude',
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Save failed');

        $log = $this->findLog(['modello' => 'Prodotti', 'azione' => 'create', 'modello_id' => $result->id]);

        $this->assertNotNull($log, 'Log should exist');
        $newData = json_decode($log->dati_nuovi, true);

        $this->assertArrayNotHasKey('created', $newData);
        $this->assertArrayNotHasKey('modified', $newData);
        $this->assertArrayNotHasKey('password', $newData);
    }

    /**
     * Test that no log is created when nothing changes on update
     *
     * @return void
     */
    public function testNoLogWhenNoChanges(): void
    {
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'NOCHANGE001',
            'nome' => 'No Changes',
        ]));
        $saved = $this->TestTable->save($entity);
        $this->assertNotFalse($saved, 'Initial save failed');

        $reloaded = $this->TestTable->get($saved->id);

        $initialCount = $this->countAllLogs();

        $result = $this->TestTable->save($reloaded);
        $this->assertNotFalse($result);

        $newCount = $this->countAllLogs();
        $this->assertEquals($initialCount, $newCount, 'No log should be created when nothing changes');
    }

    /**
     * Test user context is captured in log
     *
     * @return void
     */
    public function testUserContextCapturedInLog(): void
    {
        AuditLogBehavior::setUserContext([
            'user_id' => 2,
            'tenant_id' => 1,
            'ip_address' => '10.0.0.1',
            'user_agent' => 'Custom Agent/2.0',
        ]);

        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'CONTEXT001',
            'nome' => 'Context Test',
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Save failed');

        $log = $this->findLog(['modello' => 'Prodotti', 'modello_id' => $result->id]);

        $this->assertNotNull($log, 'Log should exist');
        $this->assertEquals(2, $log->user_id);
        $this->assertEquals(1, $log->tenant_id);
        $this->assertEquals('10.0.0.1', $log->ip_address);
        $this->assertEquals('Custom Agent/2.0', $log->user_agent);
    }

    /**
     * Test behavior can be configured to disable create logging
     *
     * @return void
     */
    public function testDisableCreateLogging(): void
    {
        $this->TestTable->behaviors()->get('AuditLog')->setConfig('logCreate', false);

        $initialCount = $this->countAllLogs();

        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'NOCREATELOG',
            'nome' => 'No Create Log',
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Save failed');

        $newCount = $this->countAllLogs();
        $this->assertEquals($initialCount, $newCount, 'No create log should be generated');

        $this->TestTable->behaviors()->get('AuditLog')->setConfig('logCreate', true);
    }

    /**
     * Test behavior can be configured to disable update logging
     *
     * @return void
     */
    public function testDisableUpdateLogging(): void
    {
        // Create entity first with logging enabled
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'NOUPDATE',
            'nome' => 'Original',
        ]));
        $saved = $this->TestTable->save($entity);
        $this->assertNotFalse($saved, 'Initial save failed');

        // Disable update logging AFTER create
        $this->TestTable->behaviors()->get('AuditLog')->setConfig('logUpdate', false);

        $reloaded = $this->TestTable->get($saved->id);
        $reloaded->nome = 'Updated';
        $this->TestTable->save($reloaded);

        // Verify no update log was created for this entity
        $updateLog = $this->findLog([
            'modello' => 'Prodotti',
            'azione' => 'update',
            'modello_id' => $saved->id,
        ]);

        $this->assertNull($updateLog, 'No update log should be generated when logUpdate is disabled');

        $this->TestTable->behaviors()->get('AuditLog')->setConfig('logUpdate', true);
    }

    /**
     * Test behavior can be configured to disable delete logging
     *
     * @return void
     */
    public function testDisableDeleteLogging(): void
    {
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'NODELETE',
            'nome' => 'To Delete',
        ]));
        $saved = $this->TestTable->save($entity);
        $this->assertNotFalse($saved, 'Initial save failed');

        $this->TestTable->behaviors()->get('AuditLog')->setConfig('logDelete', false);

        $initialCount = $this->countAllLogs();

        $this->TestTable->delete($saved, ['purge' => true]);

        $newCount = $this->countAllLogs();
        $this->assertEquals($initialCount, $newCount, 'No delete log should be generated');

        $this->TestTable->behaviors()->get('AuditLog')->setConfig('logDelete', true);
    }

    /**
     * Test that associated data is filtered from logs
     *
     * @return void
     */
    public function testAssociatedDataFiltered(): void
    {
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'ASSOC001',
            'nome' => 'Assoc Test',
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Save failed');

        $log = $this->findLog(['modello' => 'Prodotti', 'modello_id' => $result->id]);

        $this->assertNotNull($log, 'Log should exist');
        $newData = json_decode($log->dati_nuovi, true);

        foreach ($newData as $key => $value) {
            $this->assertFalse(is_array($value), "Field '$key' should not be an array");
            $this->assertFalse(is_object($value), "Field '$key' should not be an object");
        }
    }

    /**
     * Test custom exclude fields configuration
     *
     * @return void
     */
    public function testCustomExcludeFields(): void
    {
        $this->TestTable->behaviors()->get('AuditLog')->setConfig('excludeFields', [
            'created',
            'modified',
            'password',
            'prezzo_vendita',
        ]);

        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'CUSTOM001',
            'nome' => 'Custom Exclude',
            'prezzo_vendita' => 999.99,
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Save failed');

        $log = $this->findLog(['modello' => 'Prodotti', 'modello_id' => $result->id]);

        $this->assertNotNull($log, 'Log should exist');
        $newData = json_decode($log->dati_nuovi, true);

        $this->assertArrayNotHasKey('prezzo_vendita', $newData);
        $this->assertArrayHasKey('codice', $newData);

        $this->TestTable->behaviors()->get('AuditLog')->setConfig('excludeFields', [
            'created',
            'modified',
            'password',
        ]);
    }

    /**
     * Test partial user context (some fields null)
     *
     * @return void
     */
    public function testPartialUserContext(): void
    {
        AuditLogBehavior::setUserContext([
            'user_id' => null,
            'tenant_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => null,
        ]);

        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'PARTIAL001',
            'nome' => 'Partial Context',
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Save failed');

        $log = $this->findLog(['modello' => 'Prodotti', 'modello_id' => $result->id]);

        $this->assertNotNull($log, 'Log should exist');
        $this->assertNull($log->user_id);
        $this->assertEquals(1, $log->tenant_id);
        $this->assertEquals('127.0.0.1', $log->ip_address);
        $this->assertNull($log->user_agent);
    }

    /**
     * Test JSON encoding with special characters
     *
     * @return void
     */
    public function testJsonEncodingWithSpecialCharacters(): void
    {
        $entity = $this->TestTable->newEntity($this->getValidProductData([
            'codice' => 'SPECIAL001',
            'nome' => 'Prodotto con caratteri speciali: "àèìòù" <test> & more',
        ]));

        $result = $this->TestTable->save($entity);
        $this->assertNotFalse($result, 'Save failed');

        $log = $this->findLog(['modello' => 'Prodotti', 'modello_id' => $result->id]);

        $this->assertNotNull($log, 'Log should exist');
        $newData = json_decode($log->dati_nuovi, true);

        $this->assertStringContainsString('àèìòù', $newData['nome']);
    }
}
