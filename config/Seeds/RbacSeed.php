<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * RBAC Seed.
 *
 * Creates initial roles, permissions, and role-permission assignments.
 */
class RbacSeed extends BaseSeed
{
    public function run(): void
    {
        $now = DateTime::now();

        // =============================================
        // 1. ROLES
        // =============================================
        $roles = [
            [
                'id' => 1,
                'name' => 'superadmin',
                'display_name' => 'Super Amministratore',
                'description' => 'Accesso completo a tutte le funzionalità e tutti i tenant',
                'is_system' => true,
                'priority' => 100,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'display_name' => 'Amministratore',
                'description' => 'Accesso completo alle funzionalità del proprio tenant',
                'is_system' => true,
                'priority' => 50,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 3,
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Accesso operativo: gestione fatture, anagrafiche, prodotti',
                'is_system' => true,
                'priority' => 20,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 4,
                'name' => 'user',
                'display_name' => 'Utente',
                'description' => 'Accesso in sola lettura',
                'is_system' => true,
                'priority' => 10,
                'created' => $now,
                'modified' => $now,
            ],
        ];

        $this->table('roles')->insert($roles)->save();

        // =============================================
        // 2. PERMISSIONS
        // =============================================
        $permissions = [];
        $permissionId = 1;

        // Define permissions by group
        $permissionsByGroup = [
            'Fatturazione' => [
                ['Fatture', 'index', 'Fatture: Visualizza elenco'],
                ['Fatture', 'view', 'Fatture: Visualizza dettaglio'],
                ['Fatture', 'add', 'Fatture: Crea nuova'],
                ['Fatture', 'edit', 'Fatture: Modifica'],
                ['Fatture', 'delete', 'Fatture: Elimina'],
                ['FatturaRighe', 'index', 'Righe Fattura: Elenco'],
                ['FatturaRighe', 'view', 'Righe Fattura: Dettaglio'],
                ['FatturaRighe', 'add', 'Righe Fattura: Aggiungi'],
                ['FatturaRighe', 'edit', 'Righe Fattura: Modifica'],
                ['FatturaRighe', 'delete', 'Righe Fattura: Elimina'],
                ['FatturaAllegati', 'index', 'Allegati Fattura: Elenco'],
                ['FatturaAllegati', 'view', 'Allegati Fattura: Dettaglio'],
                ['FatturaAllegati', 'add', 'Allegati Fattura: Aggiungi'],
                ['FatturaAllegati', 'edit', 'Allegati Fattura: Modifica'],
                ['FatturaAllegati', 'delete', 'Allegati Fattura: Elimina'],
            ],
            'Anagrafiche' => [
                ['Anagrafiche', 'index', 'Anagrafiche: Visualizza elenco'],
                ['Anagrafiche', 'view', 'Anagrafiche: Visualizza dettaglio'],
                ['Anagrafiche', 'add', 'Anagrafiche: Crea nuova'],
                ['Anagrafiche', 'edit', 'Anagrafiche: Modifica'],
                ['Anagrafiche', 'delete', 'Anagrafiche: Elimina'],
                ['Tenants', 'index', 'Tenant: Visualizza elenco'],
                ['Tenants', 'view', 'Tenant: Visualizza dettaglio'],
                ['Tenants', 'add', 'Tenant: Crea nuovo'],
                ['Tenants', 'edit', 'Tenant: Modifica'],
                ['Tenants', 'delete', 'Tenant: Elimina'],
            ],
            'Prodotti' => [
                ['Prodotti', 'index', 'Prodotti: Visualizza elenco'],
                ['Prodotti', 'view', 'Prodotti: Visualizza dettaglio'],
                ['Prodotti', 'add', 'Prodotti: Crea nuovo'],
                ['Prodotti', 'edit', 'Prodotti: Modifica'],
                ['Prodotti', 'delete', 'Prodotti: Elimina'],
                ['CategorieProdotti', 'index', 'Categorie: Visualizza elenco'],
                ['CategorieProdotti', 'view', 'Categorie: Visualizza dettaglio'],
                ['CategorieProdotti', 'add', 'Categorie: Crea nuova'],
                ['CategorieProdotti', 'edit', 'Categorie: Modifica'],
                ['CategorieProdotti', 'delete', 'Categorie: Elimina'],
                ['Listini', 'index', 'Listini: Visualizza elenco'],
                ['Listini', 'view', 'Listini: Visualizza dettaglio'],
                ['Listini', 'add', 'Listini: Crea nuovo'],
                ['Listini', 'edit', 'Listini: Modifica'],
                ['Listini', 'delete', 'Listini: Elimina'],
                ['ListiniProdotti', 'index', 'Listini Prodotti: Elenco'],
                ['ListiniProdotti', 'view', 'Listini Prodotti: Dettaglio'],
                ['ListiniProdotti', 'add', 'Listini Prodotti: Aggiungi'],
                ['ListiniProdotti', 'edit', 'Listini Prodotti: Modifica'],
                ['ListiniProdotti', 'delete', 'Listini Prodotti: Elimina'],
            ],
            'Utenti' => [
                ['Users', 'index', 'Utenti: Visualizza elenco'],
                ['Users', 'view', 'Utenti: Visualizza dettaglio'],
                ['Users', 'add', 'Utenti: Crea nuovo'],
                ['Users', 'edit', 'Utenti: Modifica'],
                ['Users', 'delete', 'Utenti: Elimina'],
                ['Users', 'login', 'Utenti: Login'],
                ['Users', 'logout', 'Utenti: Logout'],
                ['Users', 'profile', 'Utenti: Profilo personale'],
                ['Users', 'changePassword', 'Utenti: Cambio password'],
                ['Roles', 'index', 'Ruoli: Visualizza elenco'],
                ['Roles', 'view', 'Ruoli: Visualizza dettaglio'],
                ['Roles', 'add', 'Ruoli: Crea nuovo'],
                ['Roles', 'edit', 'Ruoli: Modifica'],
                ['Roles', 'delete', 'Ruoli: Elimina'],
                ['Roles', 'matrix', 'Ruoli: Matrice permessi'],
                ['Roles', 'saveMatrix', 'Ruoli: Salva matrice'],
                ['Roles', 'syncPermissions', 'Ruoli: Sincronizza permessi'],
                ['Permissions', 'index', 'Permessi: Visualizza elenco'],
                ['Permissions', 'view', 'Permessi: Visualizza dettaglio'],
                ['Permissions', 'add', 'Permessi: Crea nuovo'],
                ['Permissions', 'edit', 'Permessi: Modifica'],
                ['Permissions', 'delete', 'Permessi: Elimina'],
            ],
            'Sistema' => [
                ['Dashboard', 'index', 'Dashboard: Visualizza'],
                ['Pages', 'display', 'Pagine: Visualizza'],
                ['LogAttivita', 'index', 'Log Attività: Elenco'],
                ['LogAttivita', 'view', 'Log Attività: Dettaglio'],
            ],
        ];

        foreach ($permissionsByGroup as $groupName => $perms) {
            foreach ($perms as $perm) {
                $permissions[] = [
                    'id' => $permissionId++,
                    'controller' => $perm[0],
                    'action' => $perm[1],
                    'prefix' => null,
                    'plugin' => null,
                    'display_name' => $perm[2],
                    'description' => null,
                    'group_name' => $groupName,
                    'created' => $now,
                    'modified' => $now,
                ];
            }
        }

        $this->table('permissions')->insert($permissions)->save();

        // =============================================
        // 3. ROLE-PERMISSION ASSIGNMENTS
        // =============================================
        $rolesPermissions = [];
        $rpId = 1;

        // Helper to find permission ID
        $permissionMap = [];
        foreach ($permissions as $p) {
            $key = $p['controller'] . '::' . $p['action'];
            $permissionMap[$key] = $p['id'];
        }

        // Admin (role_id=2) - Full CRUD on everything except Roles/Permissions management
        $adminControllers = ['Fatture', 'FatturaRighe', 'FatturaAllegati', 'Anagrafiche', 'Prodotti',
                            'CategorieProdotti', 'Listini', 'ListiniProdotti', 'Users', 'Tenants',
                            'Dashboard', 'Pages', 'LogAttivita'];
        $adminActions = ['index', 'view', 'add', 'edit', 'delete'];

        foreach ($adminControllers as $controller) {
            foreach ($adminActions as $action) {
                $key = $controller . '::' . $action;
                if (isset($permissionMap[$key])) {
                    $rolesPermissions[] = [
                        'id' => $rpId++,
                        'role_id' => 2, // admin
                        'permission_id' => $permissionMap[$key],
                        'tenant_id' => null,
                        'created' => $now,
                    ];
                }
            }
        }
        // Admin also gets profile/password
        foreach (['profile', 'changePassword', 'login', 'logout'] as $action) {
            $key = 'Users::' . $action;
            if (isset($permissionMap[$key])) {
                $rolesPermissions[] = [
                    'id' => $rpId++,
                    'role_id' => 2,
                    'permission_id' => $permissionMap[$key],
                    'tenant_id' => null,
                    'created' => $now,
                ];
            }
        }

        // Staff (role_id=3) - Operational CRUD
        $staffFullCrud = ['Fatture', 'FatturaRighe', 'FatturaAllegati', 'Anagrafiche', 'Prodotti'];
        foreach ($staffFullCrud as $controller) {
            foreach (['index', 'view', 'add', 'edit', 'delete'] as $action) {
                $key = $controller . '::' . $action;
                if (isset($permissionMap[$key])) {
                    $rolesPermissions[] = [
                        'id' => $rpId++,
                        'role_id' => 3, // staff
                        'permission_id' => $permissionMap[$key],
                        'tenant_id' => null,
                        'created' => $now,
                    ];
                }
            }
        }
        // Staff read-only on reference data
        $staffReadOnly = ['Dashboard', 'Listini', 'CategorieProdotti', 'ListiniProdotti'];
        foreach ($staffReadOnly as $controller) {
            foreach (['index', 'view'] as $action) {
                $key = $controller . '::' . $action;
                if (isset($permissionMap[$key])) {
                    $rolesPermissions[] = [
                        'id' => $rpId++,
                        'role_id' => 3,
                        'permission_id' => $permissionMap[$key],
                        'tenant_id' => null,
                        'created' => $now,
                    ];
                }
            }
        }
        // Staff profile/auth
        foreach (['profile', 'changePassword', 'login', 'logout'] as $action) {
            $key = 'Users::' . $action;
            if (isset($permissionMap[$key])) {
                $rolesPermissions[] = [
                    'id' => $rpId++,
                    'role_id' => 3,
                    'permission_id' => $permissionMap[$key],
                    'tenant_id' => null,
                    'created' => $now,
                ];
            }
        }

        // User (role_id=4) - Read-only
        $userReadOnly = ['Fatture', 'Anagrafiche', 'Prodotti', 'Dashboard'];
        foreach ($userReadOnly as $controller) {
            foreach (['index', 'view'] as $action) {
                $key = $controller . '::' . $action;
                if (isset($permissionMap[$key])) {
                    $rolesPermissions[] = [
                        'id' => $rpId++,
                        'role_id' => 4, // user
                        'permission_id' => $permissionMap[$key],
                        'tenant_id' => null,
                        'created' => $now,
                    ];
                }
            }
        }
        // User auth
        foreach (['profile', 'changePassword', 'login', 'logout'] as $action) {
            $key = 'Users::' . $action;
            if (isset($permissionMap[$key])) {
                $rolesPermissions[] = [
                    'id' => $rpId++,
                    'role_id' => 4,
                    'permission_id' => $permissionMap[$key],
                    'tenant_id' => null,
                    'created' => $now,
                ];
            }
        }

        $this->table('roles_permissions')->insert($rolesPermissions)->save();

        // =============================================
        // 4. UPDATE USERS WITH role_id
        // =============================================
        // Map role names to IDs
        $roleNameToId = [
            'superadmin' => 1,
            'admin' => 2,
            'staff' => 3,
            'user' => 4,
        ];

        $connection = $this->getAdapter()->getConnection();
        foreach ($roleNameToId as $roleName => $roleId) {
            $connection->execute(
                "UPDATE users SET role_id = ? WHERE role = ?",
                [$roleId, $roleName]
            );
        }
    }
}
