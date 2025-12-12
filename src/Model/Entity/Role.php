<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property bool $is_system
 * @property int $priority
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Permission[] $permissions
 * @property \App\Model\Entity\User[] $users
 */
class Role extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'display_name' => true,
        'description' => true,
        'is_system' => true,
        'priority' => true,
        'created' => true,
        'modified' => true,
        'permissions' => true,
    ];

    /**
     * System role names that cannot be deleted.
     */
    public const SYSTEM_ROLES = ['superadmin', 'admin', 'staff', 'user'];

    /**
     * Check if this role is a system role.
     *
     * @return bool
     */
    public function isSystemRole(): bool
    {
        return $this->is_system || in_array($this->name, self::SYSTEM_ROLES);
    }
}
