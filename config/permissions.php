<?php
/**
 * RBAC Permissions Configuration
 *
 * Defines role-based access control rules for the application.
 * Rules are evaluated in order - first match wins.
 *
 * Available keys:
 * - role: string or array of roles
 * - prefix: route prefix (Admin, Api, etc.)
 * - plugin: plugin name
 * - controller: controller name or array
 * - action: action name, array, or '*' for all
 * - allowed: bool or callback for custom logic
 */

return [
    'CakeDC/Auth.permissions' => [
        // ============================================
        // SUPERADMIN - Full access to everything
        // ============================================
        [
            'role' => 'superadmin',
            'prefix' => '*',
            'plugin' => '*',
            'controller' => '*',
            'action' => '*',
        ],

        // ============================================
        // RBAC MANAGEMENT - Only superadmin
        // ============================================
        [
            'role' => 'superadmin',
            'controller' => ['Roles', 'Permissions'],
            'action' => '*',
        ],

        // ============================================
        // ADMIN - Full access within their tenant
        // (excludes Roles/Permissions management)
        // ============================================
        [
            'role' => 'admin',
            'controller' => ['Fatture', 'FatturaRighe', 'FatturaAllegati', 'Anagrafiche', 'Prodotti',
                            'CategorieProdotti', 'Listini', 'ListiniProdotti', 'Users', 'Tenants',
                            'Dashboard', 'Pages', 'LogAttivita'],
            'action' => '*',
        ],

        // ============================================
        // STAFF - Operational CRUD access
        // ============================================
        // Full CRUD on core operational entities
        [
            'role' => 'staff',
            'controller' => ['Fatture', 'Anagrafiche', 'Prodotti', 'FatturaRighe', 'FatturaAllegati'],
            'action' => '*',
        ],
        // Read-only on reference data
        [
            'role' => 'staff',
            'controller' => ['Dashboard', 'Listini', 'CategorieProdotti', 'ListiniProdotti'],
            'action' => ['index', 'view'],
        ],

        // ============================================
        // USER - Read-only access
        // ============================================
        [
            'role' => 'user',
            'controller' => ['Fatture', 'Anagrafiche', 'Prodotti'],
            'action' => ['index', 'view'],
        ],
        [
            'role' => 'user',
            'controller' => 'Dashboard',
            'action' => ['index'],
        ],

        // ============================================
        // PUBLIC ROUTES - Available to all roles
        // ============================================
        [
            'role' => '*',
            'controller' => 'Users',
            'action' => ['login', 'logout'],
        ],
        [
            'role' => '*',
            'controller' => 'Pages',
            'action' => 'display',
        ],

        // ============================================
        // PROFILE - All authenticated users can manage their profile
        // ============================================
        [
            'role' => ['admin', 'staff', 'user'],
            'controller' => 'Users',
            'action' => ['profile', 'changePassword'],
        ],
    ],

    // Role field configuration
    'CakeDC/Auth.rbac' => [
        'role_field' => 'role',
        'default_role' => 'user',
    ],
];
