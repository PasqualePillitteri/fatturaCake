<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FatturaAllegati Entity
 *
 * @property int $id
 * @property int $fattura_id
 * @property string $nome_attachment
 * @property string|null $algoritmo_compressione
 * @property string|null $formato_attachment
 * @property string|null $descrizione_attachment
 * @property string|resource|null $attachment
 * @property string|null $file_path
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Fatture $fattura
 */
class FatturaAllegati extends Entity
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
        'fattura_id' => false, // FK to parent - managed by association
        'created' => false,    // Managed by TimestampBehavior
        'modified' => false,   // Managed by TimestampBehavior

        // User-editable fields
        'nome_attachment' => true,
        'algoritmo_compressione' => true,
        'formato_attachment' => true,
        'descrizione_attachment' => true,
        'attachment' => true,
        'file_path' => true,

        // Associations
        'fattura' => false,
    ];
}
