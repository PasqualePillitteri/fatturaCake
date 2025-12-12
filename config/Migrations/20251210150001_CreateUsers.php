<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => true,
            ])
            ->addColumn('username', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cognome', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('telefono', 'string', [
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('avatar', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('role', 'string', [
                'limit' => 20,
                'default' => 'user',
                'null' => false,
                'comment' => 'superadmin, admin, staff, user',
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('email_verified', 'datetime', [
                'null' => true,
            ])
            ->addColumn('last_login', 'datetime', [
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['email'], ['unique' => true])
            ->addIndex(['tenant_id'])
            ->addIndex(['role'])
            ->addIndex(['is_active'])
            ->addIndex(['deleted'])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
