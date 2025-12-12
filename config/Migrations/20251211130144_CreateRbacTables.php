<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

/**
 * CreateRbacTables migration.
 *
 * Creates tables for dynamic RBAC:
 * - roles: Role definitions
 * - permissions: Controller/action permissions
 * - roles_permissions: Pivot table for role-permission assignments
 */
class CreateRbacTables extends AbstractMigration
{
    /**
     * Up Method.
     *
     * @return void
     */
    public function up(): void
    {
        // =============================================
        // ROLES TABLE
        // =============================================
        $this->table('roles')
            ->addColumn('name', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('display_name', 'string', [
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('description', 'text', [
                'null' => true,
            ])
            ->addColumn('is_system', 'boolean', [
                'default' => false,
                'null' => false,
                'comment' => 'System roles cannot be deleted',
            ])
            ->addColumn('priority', 'integer', [
                'default' => 0,
                'null' => false,
                'comment' => 'Higher priority = more permissions (for UI ordering)',
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true,
            ])
            ->addIndex(['name'], ['unique' => true])
            ->create();

        // =============================================
        // PERMISSIONS TABLE
        // =============================================
        $this->table('permissions')
            ->addColumn('controller', 'string', [
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('action', 'string', [
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('prefix', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Route prefix (Admin, Api, etc.)',
            ])
            ->addColumn('plugin', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('display_name', 'string', [
                'limit' => 150,
                'null' => true,
            ])
            ->addColumn('description', 'text', [
                'null' => true,
            ])
            ->addColumn('group_name', 'string', [
                'limit' => 100,
                'null' => true,
                'comment' => 'For grouping in UI (e.g., Fatturazione, Anagrafica)',
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true,
            ])
            ->addIndex(['controller', 'action', 'prefix'], ['unique' => true, 'name' => 'idx_permission_unique'])
            ->addIndex(['group_name'])
            ->create();

        // =============================================
        // ROLES_PERMISSIONS PIVOT TABLE
        // =============================================
        $this->table('roles_permissions')
            ->addColumn('role_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('permission_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('tenant_id', 'integer', [
                'null' => true,
                'comment' => 'NULL = global, otherwise tenant-specific override',
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
            ])
            ->addForeignKey('role_id', 'roles', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('permission_id', 'permissions', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addIndex(['role_id', 'permission_id', 'tenant_id'], ['unique' => true, 'name' => 'idx_role_permission_tenant'])
            ->create();

        // =============================================
        // UPDATE USERS TABLE - Add role_id FK
        // =============================================
        // Note: Keep existing 'role' varchar field for backwards compatibility
        // The system will use role_id if present, fallback to role string
        $this->table('users')
            ->addColumn('role_id', 'integer', [
                'null' => true,
                'after' => 'role',
            ])
            ->addForeignKey('role_id', 'roles', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->update();
    }

    /**
     * Down Method.
     *
     * @return void
     */
    public function down(): void
    {
        // Remove FK from users first
        $this->table('users')
            ->dropForeignKey('role_id')
            ->update();

        $this->table('users')
            ->removeColumn('role_id')
            ->update();

        // Drop tables in reverse order
        $this->table('roles_permissions')->drop()->save();
        $this->table('permissions')->drop()->save();
        $this->table('roles')->drop()->save();
    }
}
