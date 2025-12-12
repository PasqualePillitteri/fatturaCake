<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Listini Entity
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $nome
 * @property string|null $descrizione
 * @property string $valuta
 * @property \Cake\I18n\Date|null $data_inizio
 * @property \Cake\I18n\Date|null $data_fine
 * @property bool $is_default
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Tenant $tenant
 * @property \App\Model\Entity\Prodotti[] $prodotti
 */
class Listini extends Entity
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
        'nome' => true,
        'descrizione' => true,
        'valuta' => true,
        'data_inizio' => true,
        'data_fine' => true,
        'is_default' => true,

        // Associations
        'tenant' => false,
        'prodotti' => true,
    ];
}
