<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseMigration;

class AddImportExportPermissions extends BaseMigration
{
    public function up(): void
    {
        $now = DateTime::now()->format('Y-m-d H:i:s');

        // Get max permission ID
        $maxId = $this->fetchRow("SELECT MAX(id) as max_id FROM permissions");
        $permissionId = ($maxId['max_id'] ?? 0) + 1;

        // Import/Export permissions
        $permissions = [
            // Import
            ['Import', 'fatture', 'Import: Pagina Import Excel'],
            ['Import', 'preview', 'Import: Anteprima Excel'],
            ['Import', 'execute', 'Import: Esegui Import Excel'],
            ['Import', 'downloadTemplate', 'Import: Scarica Template Excel'],
            ['Import', 'fattureXml', 'Import: Pagina Import XML/ZIP'],
            ['Import', 'previewXml', 'Import: Anteprima XML'],
            ['Import', 'executeXml', 'Import: Esegui Import XML'],
            // Export
            ['Export', 'index', 'Export: Pagina Export'],
            ['Export', 'excel', 'Export: Scarica Excel'],
            ['Export', 'xml', 'Export: Scarica XML'],
        ];

        $permissionData = [];
        $permissionIds = [];

        foreach ($permissions as $perm) {
            $permissionData[] = [
                'id' => $permissionId,
                'controller' => $perm[0],
                'action' => $perm[1],
                'prefix' => null,
                'plugin' => null,
                'display_name' => $perm[2],
                'description' => null,
                'group_name' => 'Import/Export',
                'created' => $now,
                'modified' => $now,
            ];
            $permissionIds[$perm[0] . '::' . $perm[1]] = $permissionId;
            $permissionId++;
        }

        $this->table('permissions')->insert($permissionData)->save();

        // Get role IDs dynamically (more robust for fresh databases)
        $adminRole = $this->fetchRow("SELECT id FROM roles WHERE name = 'admin'");
        $staffRole = $this->fetchRow("SELECT id FROM roles WHERE name = 'staff'");
        $userRole = $this->fetchRow("SELECT id FROM roles WHERE name = 'user'");

        // Skip roles_permissions if roles don't exist yet (e.g., test database)
        if (!$adminRole && !$staffRole && !$userRole) {
            return;
        }

        // Get max roles_permissions ID
        $maxRpId = $this->fetchRow("SELECT MAX(id) as max_id FROM roles_permissions");
        $rpId = ($maxRpId['max_id'] ?? 0) + 1;

        $rolesPermissions = [];

        // Admin - Full access to Import/Export
        if ($adminRole) {
            foreach ($permissionIds as $key => $permId) {
                $rolesPermissions[] = [
                    'id' => $rpId++,
                    'role_id' => $adminRole['id'],
                    'permission_id' => $permId,
                    'tenant_id' => null,
                    'created' => $now,
                ];
            }
        }

        // Staff - Full access to Import/Export
        if ($staffRole) {
            foreach ($permissionIds as $key => $permId) {
                $rolesPermissions[] = [
                    'id' => $rpId++,
                    'role_id' => $staffRole['id'],
                    'permission_id' => $permId,
                    'tenant_id' => null,
                    'created' => $now,
                ];
            }
        }

        // User - Only Export read access
        if ($userRole) {
            $userExportActions = ['Export::index', 'Export::excel', 'Export::xml'];
            foreach ($userExportActions as $key) {
                if (isset($permissionIds[$key])) {
                    $rolesPermissions[] = [
                        'id' => $rpId++,
                        'role_id' => $userRole['id'],
                        'permission_id' => $permissionIds[$key],
                        'tenant_id' => null,
                        'created' => $now,
                    ];
                }
            }
        }

        if (!empty($rolesPermissions)) {
            $this->table('roles_permissions')->insert($rolesPermissions)->save();
        }
    }

    public function down(): void
    {
        // Remove roles_permissions for Import/Export
        $this->execute("DELETE rp FROM roles_permissions rp
            INNER JOIN permissions p ON rp.permission_id = p.id
            WHERE p.controller IN ('Import', 'Export')");

        // Remove permissions
        $this->execute("DELETE FROM permissions WHERE controller IN ('Import', 'Export')");
    }
}
