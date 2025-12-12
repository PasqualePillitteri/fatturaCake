<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RolesPermission Entity (Pivot table)
 *
 * @property int $id
 * @property int $role_id
 * @property int $permission_id
 * @property int|null $tenant_id
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Role $role
 * @property \App\Model\Entity\Permission $permission
 * @property \App\Model\Entity\Tenant|null $tenant
 */
class RolesPermission extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        // SECURITY: RBAC pivot table - only admin can modify
        'role_id' => false,
        'permission_id' => false,
        'tenant_id' => false,
        'created' => true,
        'role' => false,
        'permission' => false,
        'tenant' => false,
    ];
}
