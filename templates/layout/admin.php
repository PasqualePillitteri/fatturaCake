<?php
/**
 * Admin Layout - FatturaCake Enterprise
 *
 * @var \App\View\AppView $this
 * @var string $content
 */

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

$appName = Configure::read('App.name', 'FatturaCake');

// Get current user role
$userRole = $currentUser->role ?? 'user';
$isSuperAdmin = ($userRole === 'superadmin');

// Load permissions for current role (cached)
$rolePermissions = [];
if (!$isSuperAdmin) {
    $cacheKey = 'menu_permissions_' . $userRole;
    $rolePermissions = Cache::read($cacheKey, 'default');

    if ($rolePermissions === null) {
        $rolesTable = TableRegistry::getTableLocator()->get('Roles');
        $role = $rolesTable->find()
            ->where(['name' => $userRole])
            ->contain(['Permissions'])
            ->first();

        $rolePermissions = [];
        if ($role && $role->permissions) {
            foreach ($role->permissions as $perm) {
                $key = $perm->controller . '::' . $perm->action;
                $rolePermissions[$key] = true;
                // Also add wildcard check
                $rolePermissions[$perm->controller . '::*'] = true;
            }
        }
        Cache::write($cacheKey, $rolePermissions, 'default');
    }
}

// Helper function to check permission
$canAccess = function($controller, $action = 'index') use ($isSuperAdmin, $rolePermissions) {
    if ($isSuperAdmin) {
        return true;
    }
    $key = $controller . '::' . $action;
    $wildcard = $controller . '::*';
    return isset($rolePermissions[$key]) || isset($rolePermissions[$wildcard]);
};

// Menu items per la sidebar con permessi
$menuItems = [
    ['label' => 'DASHBOARD', 'header' => true, 'section' => 'dashboard'],
    ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'url' => '/', 'controller' => 'Dashboard', 'action' => 'index', 'section' => 'dashboard'],

    ['label' => 'FATTURAZIONE ATTIVA', 'header' => true, 'section' => 'attiva'],
    ['label' => 'Fatture Emesse', 'icon' => 'file-output', 'url' => '/fatture-attive', 'controller' => 'Fatture', 'action' => 'indexAttive', 'section' => 'attiva'],
    ['label' => 'Clienti', 'icon' => 'user-check', 'url' => '/clienti', 'controller' => 'Anagrafiche', 'action' => 'indexClienti', 'section' => 'attiva'],

    ['label' => 'FATTURAZIONE PASSIVA', 'header' => true, 'section' => 'passiva'],
    ['label' => 'Fatture Ricevute', 'icon' => 'file-input', 'url' => '/fatture-passive', 'controller' => 'Fatture', 'action' => 'indexPassive', 'section' => 'passiva'],
    ['label' => 'Fornitori', 'icon' => 'truck', 'url' => '/fornitori', 'controller' => 'Anagrafiche', 'action' => 'indexFornitori', 'section' => 'passiva'],

    ['label' => 'ARCHIVIO', 'header' => true, 'section' => 'archivio'],
    ['label' => 'Tutte le Fatture', 'icon' => 'files', 'url' => '/fatture', 'controller' => 'Fatture', 'action' => 'index', 'section' => 'archivio'],
    ['label' => 'Tutte le Anagrafiche', 'icon' => 'contact', 'url' => '/anagrafiche', 'controller' => 'Anagrafiche', 'action' => 'index', 'section' => 'archivio'],

    ['label' => 'IMPORT / EXPORT', 'header' => true, 'section' => 'importexport'],
    ['label' => 'Import da Excel', 'icon' => 'file-spreadsheet', 'url' => '/import/fatture', 'controller' => 'Import', 'action' => 'fatture', 'section' => 'importexport'],
    ['label' => 'Import da XML/ZIP', 'icon' => 'file-code', 'url' => '/import/fatture-xml', 'controller' => 'Import', 'action' => 'fattureXml', 'section' => 'importexport'],
    ['label' => 'Export Fatture', 'icon' => 'download', 'url' => '/export', 'controller' => 'Export', 'action' => 'index', 'section' => 'importexport'],

    ['label' => 'CATALOGO', 'header' => true, 'section' => 'catalogo'],
    ['label' => 'Prodotti/Servizi', 'icon' => 'package', 'url' => '/prodotti', 'controller' => 'Prodotti', 'action' => 'index', 'section' => 'catalogo'],
    ['label' => 'Categorie', 'icon' => 'folder-tree', 'url' => '/categorie-prodotti', 'controller' => 'CategorieProdotti', 'action' => 'index', 'section' => 'catalogo'],
    ['label' => 'Listini', 'icon' => 'tags', 'url' => '/listini', 'controller' => 'Listini', 'action' => 'index', 'section' => 'catalogo'],

    ['label' => 'CONFIGURAZIONE', 'header' => true, 'section' => 'config'],
    ['label' => 'Config. SDI', 'icon' => 'settings', 'url' => '/configurazioni-sdi', 'controller' => 'ConfigurazioniSdi', 'action' => 'index', 'section' => 'config'],

    ['label' => 'AMMINISTRAZIONE', 'header' => true, 'section' => 'admin', 'roles' => ['superadmin', 'admin']],
    ['label' => 'Gestione Utenti', 'icon' => 'users', 'url' => '/users', 'controller' => 'Users', 'action' => 'index', 'section' => 'admin'],
    ['label' => 'Ruoli & Permessi', 'icon' => 'shield-check', 'url' => '/roles/matrix', 'controller' => 'Roles', 'action' => 'matrix', 'section' => 'admin', 'roles' => ['superadmin']],
    ['label' => 'Tenant', 'icon' => 'building', 'url' => '/tenants', 'controller' => 'Tenants', 'action' => 'index', 'section' => 'admin', 'roles' => ['superadmin']],
    ['label' => 'Piani', 'icon' => 'credit-card', 'url' => '/piani', 'controller' => 'Piani', 'action' => 'index', 'section' => 'admin', 'roles' => ['superadmin']],
    ['label' => 'Abbonamenti', 'icon' => 'calendar-check', 'url' => '/abbonamenti', 'controller' => 'Abbonamenti', 'action' => 'index', 'section' => 'admin', 'roles' => ['superadmin']],
    ['label' => 'Log Attivita', 'icon' => 'activity', 'url' => '/log-attivita', 'controller' => 'LogAttivita', 'action' => 'index', 'section' => 'admin'],
];

// Filter menu items based on permissions
$visibleSections = [];
$filteredMenuItems = [];

foreach ($menuItems as $item) {
    if (!empty($item['header'])) {
        // Header - check if specific roles are required
        if (!empty($item['roles']) && !in_array($userRole, $item['roles'])) {
            continue;
        }
        $filteredMenuItems[] = $item;
    } else {
        // Menu item - check role restriction first
        if (!empty($item['roles']) && !in_array($userRole, $item['roles'])) {
            continue;
        }

        // Check permission
        $controller = $item['controller'] ?? '';
        $action = $item['action'] ?? 'index';

        if ($canAccess($controller, $action)) {
            $filteredMenuItems[] = $item;
            if (!empty($item['section'])) {
                $visibleSections[$item['section']] = true;
            }
        }
    }
}

// Remove headers without visible items
$menuItems = [];
foreach ($filteredMenuItems as $item) {
    if (!empty($item['header'])) {
        $section = $item['section'] ?? '';
        if (empty($section) || !empty($visibleSections[$section])) {
            $menuItems[] = $item;
        }
    } else {
        $menuItems[] = $item;
    }
}

$currentUrl = $this->request->getRequestTarget();
?>
<!DOCTYPE html>
<html lang="it" data-bs-theme="light" data-color="sky">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?> - <?= h($appName) ?></title>
    <?= $this->Html->meta('icon') ?>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?= $this->Html->css('admin') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

    <script>
        (function() {
            const getSystemTheme = () => matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = localStorage.getItem('theme') || 'system';
            const color = localStorage.getItem('color') || 'sky';
            const html = document.documentElement;
            html.setAttribute('data-bs-theme', theme === 'system' ? getSystemTheme() : theme);
            html.setAttribute('data-color', color);
        })();
    </script>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header -->
    <header id="main-header" class="d-flex align-items-center justify-content-between px-3 border-bottom bg-body position-sticky top-0" style="height:64px; z-index:1030;">
        <div class="d-flex align-items-center">
            <!-- Mobile toggle -->
            <button class="btn btn-outline-secondary d-lg-none me-2" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                <i data-lucide="menu"></i>
            </button>

            <!-- Desktop sidebar toggle -->
            <button id="sidebar-toggle-btn" class="btn btn-outline-secondary d-none d-lg-block me-3" title="Toggle Sidebar">
                <i data-lucide="panel-left"></i>
            </button>

            <!-- Logo/Brand -->
            <div class="d-flex align-items-center">
                <i data-lucide="file-text" class="text-primary me-2" style="width:28px;height:28px;"></i>
                <h1 class="h5 mb-0 fw-bold"><?= h($appName) ?></h1>
            </div>

            <!-- Breadcrumb (desktop) -->
            <nav class="d-none d-md-block ms-4" aria-label="breadcrumb">
                <?= $this->Breadcrumbs->render(
                    ['class' => 'breadcrumb mb-0'],
                    ['separator' => '<i data-lucide="chevron-right" style="width:14px;height:14px;"></i>']
                ) ?>
            </nav>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Date/Time -->
            <div class="d-none d-sm-flex align-items-center text-secondary small">
                <i data-lucide="calendar-days" class="me-1" style="width:16px;height:16px;"></i>
                <span id="header-date"></span>
                <span class="mx-2">|</span>
                <i data-lucide="clock" class="me-1" style="width:16px;height:16px;"></i>
                <span id="header-time"></span>
            </div>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i data-lucide="user" style="width:18px;height:18px;"></i>
                    </div>
                    <i data-lucide="chevron-down" style="width:14px;height:14px;"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" style="width:320px;">
                    <div class="fw-semibold mb-3">Impostazioni Vista</div>

                    <!-- Theme selector -->
                    <div class="mb-3">
                        <label class="form-label small mb-1">Tema</label>
                        <div id="theme-selector" class="btn-group w-100">
                            <button data-theme="light" class="btn btn-sm btn-outline-secondary">
                                <i data-lucide="sun" style="width:14px;height:14px;"></i> Chiaro
                            </button>
                            <button data-theme="dark" class="btn btn-sm btn-outline-secondary">
                                <i data-lucide="moon" style="width:14px;height:14px;"></i> Scuro
                            </button>
                            <button data-theme="system" class="btn btn-sm btn-outline-secondary">
                                <i data-lucide="laptop" style="width:14px;height:14px;"></i> Sistema
                            </button>
                        </div>
                    </div>

                    <!-- Color selector -->
                    <div class="mb-3">
                        <label class="form-label small mb-1">Colore Primario</label>
                        <div id="color-selector" class="d-flex gap-2 flex-wrap">
                            <button data-color="slate" class="color-swatch" style="background:#475569;" title="Slate"></button>
                            <button data-color="red" class="color-swatch" style="background:#dc2626;" title="Rosso"></button>
                            <button data-color="orange" class="color-swatch" style="background:#ea580c;" title="Arancione"></button>
                            <button data-color="amber" class="color-swatch" style="background:#d97706;" title="Ambra"></button>
                            <button data-color="lime" class="color-swatch" style="background:#65a30d;" title="Lime"></button>
                            <button data-color="green" class="color-swatch" style="background:#16a34a;" title="Verde"></button>
                            <button data-color="teal" class="color-swatch" style="background:#0d9488;" title="Teal"></button>
                            <button data-color="sky" class="color-swatch" style="background:#0ea5e9;" title="Sky"></button>
                            <button data-color="indigo" class="color-swatch" style="background:#4f46e5;" title="Indaco"></button>
                            <button data-color="fuchsia" class="color-swatch" style="background:#c026d3;" title="Fucsia"></button>
                        </div>
                    </div>

                    <hr>
                    <a href="/users/logout" class="btn btn-outline-danger w-100">
                        <i data-lucide="log-out" class="me-2" style="width:16px;height:16px;"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="d-flex flex-grow-1">
        <!-- Sidebar -->
        <aside id="sidebarOffcanvas" class="offcanvas-lg offcanvas-start bg-body border-end" tabindex="-1">
            <div class="offcanvas-header d-lg-none border-bottom">
                <h5 class="offcanvas-title"><?= h($appName) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column p-0">
                <!-- Search -->
                <div class="p-2 border-bottom">
                    <div class="position-relative">
                        <i data-lucide="search" class="position-absolute top-50 translate-middle-y text-secondary" style="left:12px;width:14px;height:14px;"></i>
                        <input type="search" id="sidebar-search" class="form-control form-control-sm ps-4" placeholder="Filtra menu...">
                    </div>
                </div>

                <!-- Menu -->
                <nav id="sidebar-nav" class="flex-grow-1 overflow-auto p-2">
                    <?php foreach ($menuItems as $item): ?>
                        <?php if (!empty($item['header'])): ?>
                            <div class="menu-header"><?= h($item['label']) ?></div>
                        <?php else: ?>
                            <?php
                            $url = $item['url'] ?? '#';
                            // Match esatto o URL che inizia con $url/ o $url?
                            $isActive = ($currentUrl === $url ||
                                strpos($currentUrl, $url . '/') === 0 ||
                                strpos($currentUrl, $url . '?') === 0);
                            $activeClass = $isActive ? ' active' : '';
                            ?>
                            <a href="<?= h($url) ?>" class="menu-link<?= $activeClass ?>">
                                <?php if (!empty($item['icon'])): ?>
                                    <i data-lucide="<?= h($item['icon']) ?>" style="width:16px;height:16px;"></i>
                                <?php endif; ?>
                                <span><?= h($item['label']) ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div id="no-results-message" class="text-center small text-secondary py-3 d-none">Nessun risultato</div>
                </nav>

                <!-- Footer -->
                <div class="border-top p-2">
                    <div class="text-center small text-secondary">
                        FatturaCake v1.0
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-grow-1 d-flex flex-column">
            <div class="p-3 p-lg-4">
                <!-- Mobile breadcrumb -->
                <nav class="d-md-none mb-3" aria-label="breadcrumb">
                    <?= $this->Breadcrumbs->render(['class' => 'breadcrumb mb-0 small']) ?>
                </nav>

                <!-- Flash messages -->
                <?= $this->Flash->render() ?>

                <!-- Page content -->
                <?= $this->fetch('content') ?>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>

    <?= $this->Html->script('admin') ?>
    <?= $this->fetch('script') ?>
</body>
</html>
