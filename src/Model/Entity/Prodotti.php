<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Prodotti Entity
 *
 * @property int $id
 * @property int $tenant_id
 * @property int|null $categoria_id
 * @property string $tipo
 * @property string $codice
 * @property string|null $codice_tipo
 * @property string|null $codice_valore
 * @property string $nome
 * @property string|null $descrizione
 * @property string|null $descrizione_estesa
 * @property string|null $unita_misura
 * @property string|null $prezzo_acquisto
 * @property string|null $prezzo_vendita
 * @property bool $prezzo_ivato
 * @property string $aliquota_iva
 * @property string|null $natura
 * @property string|null $riferimento_normativo
 * @property bool $soggetto_ritenuta
 * @property bool $gestione_magazzino
 * @property string $giacenza
 * @property string|null $scorta_minima
 * @property string|null $note
 * @property int $sort_order
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Tenant $tenant
 * @property \App\Model\Entity\CategorieProdotti $categoria
 * @property \App\Model\Entity\Listini[] $listini
 */
class Prodotti extends Entity
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
        'categoria_id' => true,
        'tipo' => true,
        'codice' => true,
        'codice_tipo' => true,
        'codice_valore' => true,
        'nome' => true,
        'descrizione' => true,
        'descrizione_estesa' => true,
        'unita_misura' => true,
        'prezzo_acquisto' => true,
        'prezzo_vendita' => true,
        'prezzo_ivato' => true,
        'aliquota_iva' => true,
        'natura' => true,
        'riferimento_normativo' => true,
        'soggetto_ritenuta' => true,
        'gestione_magazzino' => true,
        'giacenza' => true,
        'scorta_minima' => true,
        'note' => true,
        'sort_order' => true,

        // Associations
        'tenant' => false,
        'categoria' => true,
        'listini' => true,
    ];
}
