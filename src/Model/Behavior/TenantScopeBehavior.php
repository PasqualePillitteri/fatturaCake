<?php
declare(strict_types=1);

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;

/**
 * TenantScope Behavior
 *
 * Automatically filters queries by tenant_id and injects tenant_id on save.
 * Ensures data isolation in multi-tenant architecture.
 */
class TenantScopeBehavior extends Behavior
{
    /**
     * Static storage for current tenant context.
     *
     * @var array<string, mixed>
     */
    protected static array $_tenantContext = [
        'tenant_id' => null,
        'role' => null,
    ];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'field' => 'tenant_id',
        'allowNullTenant' => false, // If true, allows queries without tenant filter
    ];

    /**
     * Set the tenant context (called from AppController).
     *
     * @param int|null $tenantId Tenant ID
     * @param string|null $role User role
     * @return void
     */
    public static function setTenantContext(?int $tenantId, ?string $role = null): void
    {
        self::$_tenantContext['tenant_id'] = $tenantId;
        self::$_tenantContext['role'] = $role;
    }

    /**
     * Get the current tenant context (for use in validation rules, etc.)
     *
     * @return array{tenant_id: int|null, role: string|null}
     */
    public static function getTenantContext(): array
    {
        return self::$_tenantContext;
    }

    /**
     * Get the current tenant ID.
     *
     * @return int|null
     */
    protected function getCurrentTenantId(): ?int
    {
        // First check static context (set from controller)
        if (self::$_tenantContext['tenant_id'] !== null) {
            return self::$_tenantContext['tenant_id'];
        }

        // Fallback to PHP session
        if (session_status() === PHP_SESSION_ACTIVE || session_status() === PHP_SESSION_NONE) {
            if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
                session_start();
            }
            if (isset($_SESSION['Auth']['tenant_id'])) {
                return (int)$_SESSION['Auth']['tenant_id'];
            }
        }

        return null;
    }

    /**
     * Check if the current user is a superadmin (can access all tenants).
     *
     * @return bool
     */
    protected function isSuperAdmin(): bool
    {
        // Check static context first
        if (self::$_tenantContext['role'] === 'superadmin') {
            return true;
        }

        // Fallback to PHP session
        if (session_status() === PHP_SESSION_ACTIVE) {
            return ($_SESSION['Auth']['role'] ?? null) === 'superadmin';
        }

        return false;
    }

    /**
     * Before find callback - add tenant filter to queries.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\ORM\Query\SelectQuery $query The query
     * @param \ArrayObject $options Query options
     * @param bool $primary Whether this is the primary table
     * @return void
     */
    public function beforeFind(EventInterface $event, SelectQuery $query, ArrayObject $options, bool $primary): void
    {
        // Skip tenant filtering for superadmin
        if ($this->isSuperAdmin()) {
            return;
        }

        // Skip if explicitly disabled in options
        if (!empty($options['skipTenantScope'])) {
            return;
        }

        $tenantId = $this->getCurrentTenantId();
        $field = $this->getConfig('field');
        $tableAlias = $this->table()->getAlias();

        if ($tenantId) {
            $query->where(["{$tableAlias}.{$field}" => $tenantId]);
        } elseif (!$this->getConfig('allowNullTenant')) {
            // If no tenant ID and null not allowed, return empty result
            $query->where(['1 = 0']);
        }
    }

    /**
     * Before save callback - inject and enforce tenant_id.
     *
     * SECURITY: Always forces the correct tenant_id to prevent cross-tenant data manipulation.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options Save options
     * @return bool|void Returns false to abort save if tenant mismatch detected on existing entity
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // Superadmin can save to any tenant
        if ($this->isSuperAdmin()) {
            return;
        }

        $field = $this->getConfig('field');
        $tenantId = $this->getCurrentTenantId();

        if (!$tenantId) {
            // No tenant context - abort save for security
            return false;
        }

        if ($entity->isNew()) {
            // SECURITY: Always force tenant_id on new entities, regardless of what was passed
            $entity->set($field, $tenantId);
        } else {
            // SECURITY: For existing entities, verify they belong to the current tenant
            $originalTenantId = $entity->getOriginal($field) ?: $entity->get($field);

            if ($originalTenantId && (int)$originalTenantId !== $tenantId) {
                // Attempting to modify entity from different tenant - abort
                $event->stopPropagation();
                return false;
            }

            // Prevent changing tenant_id on existing entities
            if ($entity->isDirty($field) && (int)$entity->get($field) !== $tenantId) {
                // Restore original tenant_id
                $entity->set($field, $originalTenantId);
            }
        }
    }

    /**
     * Before delete callback - verify tenant ownership.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options Delete options
     * @return void
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        // Superadmin can delete any record
        if ($this->isSuperAdmin()) {
            return;
        }

        $field = $this->getConfig('field');
        $tenantId = $this->getCurrentTenantId();

        // Verify the entity belongs to the current tenant
        if ($entity->get($field) && $entity->get($field) !== $tenantId) {
            $event->stopPropagation();
            $event->setResult(false);
        }
    }

    /**
     * Custom finder for tenant-scoped queries.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query
     * @param int $tenantId The tenant ID to filter by
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findByTenant(SelectQuery $query, int $tenantId): SelectQuery
    {
        $field = $this->getConfig('field');
        $tableAlias = $this->table()->getAlias();

        return $query->where(["{$tableAlias}.{$field}" => $tenantId]);
    }
}
