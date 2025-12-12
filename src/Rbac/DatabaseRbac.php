<?php
declare(strict_types=1);

namespace App\Rbac;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Database RBAC Provider
 *
 * Reads permissions from database instead of config file.
 * Provides caching for performance.
 */
class DatabaseRbac
{
    /**
     * Cache config name.
     */
    protected const CACHE_CONFIG = 'default';

    /**
     * Cache key prefix.
     */
    protected const CACHE_PREFIX = 'rbac_permissions_';

    /**
     * Roles table.
     *
     * @var \App\Model\Table\RolesTable
     */
    protected $Roles;

    /**
     * Permissions table.
     *
     * @var \App\Model\Table\PermissionsTable
     */
    protected $Permissions;

    /**
     * Cached permissions array.
     *
     * @var array|null
     */
    protected ?array $permissionsCache = null;

    /**
     * Configuration options.
     *
     * @var array
     */
    protected array $config = [
        'role_field' => 'role',
        'default_role' => 'user',
        'cache_permissions' => true,
        'fallback_to_config' => true,
    ];

    /**
     * Constructor.
     *
     * @param array $config Configuration options.
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        $this->Roles = TableRegistry::getTableLocator()->get('Roles');
        $this->Permissions = TableRegistry::getTableLocator()->get('Permissions');
    }

    /**
     * Check if a user has permission to access a controller/action.
     *
     * @param array|object $user User data or identity.
     * @param \Psr\Http\Message\ServerRequestInterface $request Current request.
     * @return bool
     */
    public function checkPermission($user, ServerRequestInterface $request): bool
    {
        $role = $this->getUserRole($user);
        $params = $request->getAttribute('params', []);

        $controller = $params['controller'] ?? '';
        $action = $params['action'] ?? '';
        $prefix = $params['prefix'] ?? null;
        $plugin = $params['plugin'] ?? null;

        // Superadmin bypasses all checks
        if ($role === 'superadmin') {
            return true;
        }

        // Check database permissions
        $permissions = $this->getPermissionsForRole($role);

        foreach ($permissions as $permission) {
            if ($this->matchesPermission($permission, $controller, $action, $prefix, $plugin)) {
                return true;
            }
        }

        // Fallback to config file if enabled
        if ($this->config['fallback_to_config']) {
            return $this->checkConfigPermission($role, $controller, $action, $prefix, $plugin);
        }

        return false;
    }

    /**
     * Get user's role.
     *
     * @param array|object $user User data.
     * @return string
     */
    protected function getUserRole($user): string
    {
        $roleField = $this->config['role_field'];

        if (is_array($user)) {
            return $user[$roleField] ?? $this->config['default_role'];
        }

        if (is_object($user) && method_exists($user, 'get')) {
            return $user->get($roleField) ?? $this->config['default_role'];
        }

        if (is_object($user) && isset($user->{$roleField})) {
            return $user->{$roleField} ?? $this->config['default_role'];
        }

        return $this->config['default_role'];
    }

    /**
     * Get permissions for a role from database.
     *
     * @param string $role Role name.
     * @param int|null $tenantId Optional tenant ID for tenant-specific permissions.
     * @return array
     */
    public function getPermissionsForRole(string $role, ?int $tenantId = null): array
    {
        $cacheKey = self::CACHE_PREFIX . $role . '_' . ($tenantId ?? 'global');

        if ($this->config['cache_permissions']) {
            $cached = Cache::read($cacheKey, self::CACHE_CONFIG);
            if ($cached !== null) {
                return $cached;
            }
        }

        // Find role by name
        $roleEntity = $this->Roles->find()
            ->where(['name' => $role])
            ->contain(['Permissions'])
            ->first();

        if (!$roleEntity) {
            return [];
        }

        $permissions = [];
        foreach ($roleEntity->permissions as $permission) {
            $permissions[] = [
                'controller' => $permission->controller,
                'action' => $permission->action,
                'prefix' => $permission->prefix,
                'plugin' => $permission->plugin,
            ];
        }

        if ($this->config['cache_permissions']) {
            Cache::write($cacheKey, $permissions, self::CACHE_CONFIG);
        }

        return $permissions;
    }

    /**
     * Check if permission matches the current request.
     *
     * @param array $permission Permission data.
     * @param string $controller Controller name.
     * @param string $action Action name.
     * @param string|null $prefix Route prefix.
     * @param string|null $plugin Plugin name.
     * @return bool
     */
    protected function matchesPermission(
        array $permission,
        string $controller,
        string $action,
        ?string $prefix,
        ?string $plugin
    ): bool {
        // Check controller (supports wildcard *)
        if ($permission['controller'] !== '*' && $permission['controller'] !== $controller) {
            return false;
        }

        // Check action (supports wildcard *)
        if ($permission['action'] !== '*' && $permission['action'] !== $action) {
            return false;
        }

        // Check prefix
        if ($permission['prefix'] !== null && $permission['prefix'] !== '*' && $permission['prefix'] !== $prefix) {
            return false;
        }

        // Check plugin
        if ($permission['plugin'] !== null && $permission['plugin'] !== '*' && $permission['plugin'] !== $plugin) {
            return false;
        }

        return true;
    }

    /**
     * Fallback: check permission from config file.
     *
     * @param string $role Role name.
     * @param string $controller Controller name.
     * @param string $action Action name.
     * @param string|null $prefix Route prefix.
     * @param string|null $plugin Plugin name.
     * @return bool
     */
    protected function checkConfigPermission(
        string $role,
        string $controller,
        string $action,
        ?string $prefix,
        ?string $plugin
    ): bool {
        $permissions = Configure::read('CakeDC/Auth.permissions', []);

        foreach ($permissions as $rule) {
            // Check role
            $ruleRole = $rule['role'] ?? '*';
            if ($ruleRole !== '*') {
                $allowedRoles = (array) $ruleRole;
                if (!in_array($role, $allowedRoles)) {
                    continue;
                }
            }

            // Check controller
            $ruleController = $rule['controller'] ?? '*';
            if ($ruleController !== '*') {
                $allowedControllers = (array) $ruleController;
                if (!in_array($controller, $allowedControllers)) {
                    continue;
                }
            }

            // Check action
            $ruleAction = $rule['action'] ?? '*';
            if ($ruleAction !== '*') {
                $allowedActions = (array) $ruleAction;
                if (!in_array($action, $allowedActions)) {
                    continue;
                }
            }

            // Check prefix
            if (isset($rule['prefix']) && $rule['prefix'] !== '*' && $rule['prefix'] !== $prefix) {
                continue;
            }

            // Check plugin
            if (isset($rule['plugin']) && $rule['plugin'] !== '*' && $rule['plugin'] !== $plugin) {
                continue;
            }

            // Match found
            return true;
        }

        return false;
    }

    /**
     * Clear permission cache for a role or all roles.
     *
     * @param string|null $role Role name (null for all).
     * @return void
     */
    public function clearCache(?string $role = null): void
    {
        if ($role) {
            Cache::delete(self::CACHE_PREFIX . $role . '_global', self::CACHE_CONFIG);
        } else {
            // Clear all RBAC cache entries
            Cache::clear(self::CACHE_CONFIG);
        }
    }

    /**
     * Get all permissions grouped by role for UI display.
     *
     * @return array
     */
    public function getAllPermissionsMatrix(): array
    {
        $roles = $this->Roles->find('ordered')->contain(['Permissions'])->toArray();
        $allPermissions = $this->Permissions->find('grouped')->toArray();

        $matrix = [];
        foreach ($allPermissions as $permission) {
            $matrix[$permission->id] = [
                'permission' => $permission,
                'roles' => [],
            ];
        }

        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                if (isset($matrix[$permission->id])) {
                    $matrix[$permission->id]['roles'][$role->id] = true;
                }
            }
        }

        return [
            'roles' => $roles,
            'permissions' => $allPermissions,
            'matrix' => $matrix,
        ];
    }
}
