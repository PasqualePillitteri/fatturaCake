<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'tenant_id' => null,
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'password' => '$2y$10$abcdefghijklmnopqrstuv', // hashed password
                'nome' => 'Super',
                'cognome' => 'Admin',
                'telefono' => null,
                'avatar' => null,
                'role' => 'superadmin',
                'is_active' => 1,
                'email_verified' => '2025-01-01 10:00:00',
                'last_login' => '2025-01-01 10:00:00',
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 2,
                'tenant_id' => 1,
                'username' => 'admin_tenant1',
                'email' => 'admin@tenant1.com',
                'password' => '$2y$10$abcdefghijklmnopqrstuv',
                'nome' => 'Admin',
                'cognome' => 'Tenant1',
                'telefono' => null,
                'avatar' => null,
                'role' => 'admin',
                'is_active' => 1,
                'email_verified' => '2025-01-01 10:00:00',
                'last_login' => '2025-01-01 10:00:00',
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 3,
                'tenant_id' => 2,
                'username' => 'admin_tenant2',
                'email' => 'admin@tenant2.com',
                'password' => '$2y$10$abcdefghijklmnopqrstuv',
                'nome' => 'Admin',
                'cognome' => 'Tenant2',
                'telefono' => null,
                'avatar' => null,
                'role' => 'admin',
                'is_active' => 1,
                'email_verified' => '2025-01-01 10:00:00',
                'last_login' => '2025-01-01 10:00:00',
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 4,
                'tenant_id' => 1,
                'username' => 'staff_tenant1',
                'email' => 'staff@tenant1.com',
                'password' => '$2y$10$abcdefghijklmnopqrstuv',
                'nome' => 'Staff',
                'cognome' => 'Tenant1',
                'telefono' => null,
                'avatar' => null,
                'role' => 'staff',
                'is_active' => 1,
                'email_verified' => '2025-01-01 10:00:00',
                'last_login' => '2025-01-01 10:00:00',
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
