<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Permission Entity
 *
 * @property int $id
 * @property string $controller
 * @property string $action
 * @property string|null $prefix
 * @property string|null $plugin
 * @property string|null $display_name
 * @property string|null $description
 * @property string|null $group_name
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Role[] $roles
 */
class Permission extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'controller' => true,
        'action' => true,
        'prefix' => true,
        'plugin' => true,
        'display_name' => true,
        'description' => true,
        'group_name' => true,
        'created' => true,
        'modified' => true,
        'roles' => true,
    ];

    /**
     * Virtual field: full permission identifier.
     *
     * @return string
     */
    protected function _getIdentifier(): string
    {
        $parts = [];
        if ($this->prefix) {
            $parts[] = $this->prefix;
        }
        if ($this->plugin) {
            $parts[] = $this->plugin;
        }
        $parts[] = $this->controller;
        $parts[] = $this->action;

        return implode('.', $parts);
    }
}
