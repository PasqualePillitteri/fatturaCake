<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use ArrayObject;
use Authentication\Identity;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\UsersController Test Case
 *
 * Tests for user creation with automatic tenant assignment.
 * Main focus: verify that tenant field is hidden for non-superadmin users.
 */
class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.Tenants',
        'app.Piani',
        'app.Abbonamenti',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    /**
     * Helper method to login as admin user with tenant.
     *
     * @param int $userId User ID
     * @param int|null $tenantId Tenant ID
     * @param string $role User role
     * @return void
     */
    protected function loginAsUser(int $userId = 2, ?int $tenantId = 1, string $role = 'admin'): void
    {
        $userData = new ArrayObject([
            'id' => $userId,
            'tenant_id' => $tenantId,
            'role' => $role,
            'username' => 'admin_tenant1',
            'email' => 'admin@tenant1.com',
        ]);

        $identity = new Identity($userData);

        $this->session([
            'Auth' => [
                'id' => $userId,
                'tenant_id' => $tenantId,
                'role' => $role,
                'username' => 'admin_tenant1',
                'email' => 'admin@tenant1.com',
            ],
        ]);

        $this->configRequest([
            'attributes' => [
                'identity' => $identity,
            ],
        ]);
    }

    /**
     * Helper to login as superadmin (no tenant).
     *
     * @return void
     */
    protected function loginAsSuperadmin(): void
    {
        $userData = new ArrayObject([
            'id' => 1,
            'tenant_id' => null,
            'role' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
        ]);

        $identity = new Identity($userData);

        $this->session([
            'Auth' => [
                'id' => 1,
                'tenant_id' => null,
                'role' => 'superadmin',
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
            ],
        ]);

        $this->configRequest([
            'attributes' => [
                'identity' => $identity,
            ],
        ]);
    }

    // ==================== INDEX TESTS ====================

    /**
     * Test index page accessible when authenticated.
     *
     * @return void
     */
    public function testIndexAuthenticated(): void
    {
        $this->loginAsUser();

        $this->get('/users');

        $this->assertResponseOk();
    }

    // ==================== ADD PAGE TESTS ====================

    /**
     * Test add page accessible when authenticated.
     *
     * @return void
     */
    public function testAddPageAccessible(): void
    {
        $this->loginAsUser();

        $this->get('/users/add');

        $this->assertResponseOk();
        $this->assertResponseContains('Nuovo Utente');
    }

    /**
     * Test add page does NOT show tenant field for admin user.
     * This is the main fix - admin users should not see tenant selector.
     *
     * @return void
     */
    public function testAddPageDoesNotShowTenantForAdmin(): void
    {
        $this->loginAsUser(2, 1, 'admin');

        $this->get('/users/add');

        $this->assertResponseOk();
        // Admin should NOT see tenant selection field (the name attribute in a select)
        $this->assertResponseNotContains('name="tenant_id"');
    }

    /**
     * Test add page SHOWS tenant field for superadmin.
     *
     * @return void
     */
    public function testAddPageShowsTenantForSuperadmin(): void
    {
        $this->loginAsSuperadmin();

        $this->get('/users/add');

        $this->assertResponseOk();
        // Superadmin SHOULD see tenant selection field
        $this->assertResponseContains('tenant_id');
    }

    /**
     * Test admin can create a new user with automatic tenant assignment.
     * Even if a different tenant_id is sent, it should be ignored.
     *
     * @return void
     */
    public function testAddUserWithAutomaticTenantAssignment(): void
    {
        $this->loginAsUser(2, 1, 'admin');

        $data = [
            'username' => 'newuser',
            'email' => 'newuser@test.com',
            'password' => 'password123',
            'nome' => 'New',
            'cognome' => 'User',
            'role' => 'user',
            'is_active' => true,
            // Attempt to set a different tenant (should be ignored)
            'tenant_id' => 999,
        ];

        $this->post('/users/add', $data);

        // Should redirect to users list
        $this->assertRedirectContains('/users');

        // Verify user was created with correct tenant
        $usersTable = $this->getTableLocator()->get('Users');
        if ($usersTable->hasBehavior('TenantScope')) {
            $usersTable->removeBehavior('TenantScope');
        }
        $newUser = $usersTable->find()
            ->where(['email' => 'newuser@test.com'])
            ->first();

        $this->assertNotNull($newUser, 'User should be created');
        $this->assertEquals(1, $newUser->tenant_id, 'Tenant should be auto-assigned from logged user (tenant_id=1)');
        $this->assertEquals('user', $newUser->role);
        $this->assertEquals('newuser', $newUser->username);
    }

    // ==================== EDIT PAGE TESTS ====================

    /**
     * Test edit page accessible for own tenant user.
     *
     * @return void
     */
    public function testEditPageAccessible(): void
    {
        $this->loginAsUser(2, 1, 'admin');

        // Admin tenant1 (id=2) edits staff tenant1 (id=4)
        $this->get('/users/edit/4');

        $this->assertResponseOk();
        $this->assertResponseContains('Modifica Utente');
    }

    /**
     * Test edit page does NOT show tenant field for admin user.
     *
     * @return void
     */
    public function testEditPageDoesNotShowTenantForAdmin(): void
    {
        $this->loginAsUser(2, 1, 'admin');

        $this->get('/users/edit/4');

        $this->assertResponseOk();
        // Admin should NOT see tenant selection field
        $this->assertResponseNotContains('name="tenant_id"');
    }

    /**
     * Test admin cannot change tenant_id of existing user.
     *
     * @return void
     */
    public function testEditUserCannotChangeTenant(): void
    {
        $this->loginAsUser(2, 1, 'admin');

        $data = [
            'username' => 'staff_tenant1_updated',
            'email' => 'staff@tenant1.com',
            'nome' => 'Staff Updated',
            'cognome' => 'Tenant1',
            'role' => 'staff',
            'tenant_id' => 2, // Attempt to change tenant
            'is_active' => true,
        ];

        $this->put('/users/edit/4', $data);

        // Verify tenant was NOT changed
        $usersTable = $this->getTableLocator()->get('Users');
        if ($usersTable->hasBehavior('TenantScope')) {
            $usersTable->removeBehavior('TenantScope');
        }
        $user = $usersTable->get(4);

        $this->assertEquals(1, $user->tenant_id, 'Tenant should not be changed by non-superadmin');
    }

    // ==================== VIEW TESTS ====================

    /**
     * Test view page accessible.
     *
     * @return void
     */
    public function testViewPageAccessible(): void
    {
        $this->loginAsUser(2, 1, 'admin');

        $this->get('/users/view/4');

        $this->assertResponseOk();
    }

    // ==================== DELETE TESTS ====================

    /**
     * Test user cannot delete themselves.
     *
     * @return void
     */
    public function testCannotDeleteSelf(): void
    {
        $this->loginAsUser(2, 1, 'admin');

        $this->post('/users/delete/2');

        $this->assertRedirectContains('/users');
        $this->assertFlashElement('flash/error');

        // Verify user still exists
        $usersTable = $this->getTableLocator()->get('Users');
        if ($usersTable->hasBehavior('TenantScope')) {
            $usersTable->removeBehavior('TenantScope');
        }
        $user = $usersTable->findById(2)->first();
        $this->assertNotNull($user, 'User should still exist');
    }
}
