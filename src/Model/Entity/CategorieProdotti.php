<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CategorieProdotti Entity
 *
 * @property int $id
 * @property int $tenant_id
 * @property int|null $parent_id
 * @property string $nome
 * @property string|null $descrizione
 * @property int $sort_order
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Tenant $tenant
 * @property \App\Model\Entity\ParentCategorieProdotti $parent_categorie_prodotti
 * @property \App\Model\Entity\ChildCategorieProdotti[] $child_categorie_prodotti
 */
class CategorieProdotti extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        // SECURITY: System/protected fields
        'tenant_id' => false,  // Managed by TenantScopeBehavior
        'created' => false,    // Managed by TimestampBehavior
        'modified' => false,   // Managed by TimestampBehavior
        'deleted' => false,    // Managed by TrashBehavior
        'is_active' => false,  // Admin only

        // User-editable fields
        'parent_id' => true,
        'nome' => true,
        'descrizione' => true,
        'sort_order' => true,

        // Associations
        'tenant' => false,
        'parent_categorie_prodotti' => true,
        'child_categorie_prodotti' => true,
    ];
}
