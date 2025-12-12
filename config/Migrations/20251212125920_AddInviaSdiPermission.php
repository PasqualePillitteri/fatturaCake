<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseMigration;

class AddInviaSdiPermission extends BaseMigration
{
    public function up(): void
    {
        $now = DateTime::now()->format('Y-m-d H:i:s');

        // Get max permission ID
        $maxId = $this->fetchRow("SELECT MAX(id) as max_id FROM permissions");
        $permissionId = ($maxId['max_id'] ?? 0) + 1;

        // Add inviaSDI permission
        $this->table('permissions')->insert([
            [
                'id' => $permissionId,
                'controller' => 'Fatture',
                'action' => 'inviaSDI',
                'prefix' => null,
                'plugin' => null,
                'display_name' => 'Fatture: Invia a SDI (simulazione)',
                'description' => 'Permette l\'invio simulato delle fatture allo SDI',
                'group_name' => 'Fatturazione',
                'created' => $now,
                'modified' => $now,
            ],
        ])->save();

        // Get role IDs dynamically (more robust for fresh databases)
        $adminRole = $this->fetchRow("SELECT id FROM roles WHERE name = 'admin'");
        $staffRole = $this->fetchRow("SELECT id FROM roles WHERE name = 'staff'");

        // Skip roles_permissions if roles don't exist yet (e.g., test database)
        if (!$adminRole && !$staffRole) {
            return;
        }

        // Get max roles_permissions ID
        $maxRpId = $this->fetchRow("SELECT MAX(id) as max_id FROM roles_permissions");
        $rpId = ($maxRpId['max_id'] ?? 0) + 1;

        $rolesPermissions = [];

        // Assign to admin and staff
        if ($adminRole) {
            $rolesPermissions[] = [
                'id' => $rpId++,
                'role_id' => $adminRole['id'],
                'permission_id' => $permissionId,
                'tenant_id' => null,
                'created' => $now,
            ];
        }

        if ($staffRole) {
            $rolesPermissions[] = [
                'id' => $rpId++,
                'role_id' => $staffRole['id'],
                'permission_id' => $permissionId,
                'tenant_id' => null,
                'created' => $now,
            ];
        }

        if (!empty($rolesPermissions)) {
            $this->table('roles_permissions')->insert($rolesPermissions)->save();
        }
    }

    public function down(): void
    {
        // Remove roles_permissions
        $this->execute("DELETE rp FROM roles_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            WHERE p.controller = 'Fatture' AND p.action = 'inviaSDI'");

        // Remove permission
        $this->execute("DELETE FROM permissions WHERE controller = 'Fatture' AND action = 'inviaSDI'");
    }
}
