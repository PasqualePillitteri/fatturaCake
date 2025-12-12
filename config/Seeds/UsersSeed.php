<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * Users seed.
 * Password: pilliTest
 */
class UsersSeed extends BaseSeed
{
    public function run(): void
    {
        $now = DateTime::now();
        $passwordHash = password_hash('7%p8Z926To', PASSWORD_DEFAULT);

        $data = [
            [
                'id' => 1,
                'tenant_id' => null,
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'password' => $passwordHash,
                'nome' => 'Super',
                'cognome' => 'Admin',
                'telefono' => null,
                'avatar' => null,
                'role' => 'superadmin',
                'is_active' => true,
                'email_verified' => $now,
                'last_login' => null,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 2,
                'tenant_id' => 1,
                'username' => 'admin',
                'email' => 'admin@example.com',
                'password' => $passwordHash,
                'nome' => 'Admin',
                'cognome' => 'User',
                'telefono' => null,
                'avatar' => null,
                'role' => 'admin',
                'is_active' => true,
                'email_verified' => $now,
                'last_login' => null,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 3,
                'tenant_id' => 1,
                'username' => 'staff',
                'email' => 'staff@example.com',
                'password' => $passwordHash,
                'nome' => 'Staff',
                'cognome' => 'User',
                'telefono' => null,
                'avatar' => null,
                'role' => 'staff',
                'is_active' => true,
                'email_verified' => $now,
                'last_login' => null,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 4,
                'tenant_id' => 1,
                'username' => 'user',
                'email' => 'user@example.com',
                'password' => $passwordHash,
                'nome' => 'Normal',
                'cognome' => 'User',
                'telefono' => null,
                'avatar' => null,
                'role' => 'user',
                'is_active' => true,
                'email_verified' => $now,
                'last_login' => null,
                'created' => $now,
                'modified' => $now,
            ],
        ];

        $table = $this->table('users');
        $table->insert($data)->save();
    }
}
