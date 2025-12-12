<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ListiniProdotti Entity
 *
 * @property int $id
 * @property int $listino_id
 * @property int $prodotto_id
 * @property string $prezzo
 * @property string|null $prezzo_minimo
 * @property string|null $sconto_massimo
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Listini $listino
 * @property \App\Model\Entity\Prodotti $prodotto
 */
class ListiniProdotti extends Entity
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
        'created' => false,   // Managed by TimestampBehavior
        'modified' => false,  // Managed by TimestampBehavior

        // User-editable fields (FK validated in buildRules)
        'listino_id' => true,
        'prodotto_id' => true,
        'prezzo' => true,
        'prezzo_minimo' => true,
        'sconto_massimo' => true,

        // Associations
        'listino' => true,
        'prodotto' => true,
    ];
}
