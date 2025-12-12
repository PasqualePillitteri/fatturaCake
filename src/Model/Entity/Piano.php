<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Piano Entity
 *
 * @property int $id
 * @property string $nome
 * @property string|null $descrizione
 * @property string $prezzo_mensile
 * @property string $prezzo_annuale
 * @property bool $is_active
 * @property int $sort_order
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Abbonamento[] $abbonamenti
 */
class Piano extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'nome' => true,
        'descrizione' => true,
        'prezzo_mensile' => true,
        'prezzo_annuale' => true,
        'is_active' => true,
        'sort_order' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'abbonamenti' => true,
    ];
}
