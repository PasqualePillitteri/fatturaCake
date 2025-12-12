<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FatturaStatiSdi Entity
 *
 * @property int $id
 * @property int $fattura_id
 * @property string $stato
 * @property string|null $identificativo_sdi
 * @property \Cake\I18n\DateTime|null $data_ora_ricezione
 * @property string|null $messaggio
 * @property string|resource|null $file_notifica
 * @property string|null $nome_file_notifica
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Fatture $fattura
 */
class FatturaStatiSdi extends Entity
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
        // SECURITY: System/protected fields - SDI status should only be set by system
        'fattura_id' => false,         // FK to parent - managed by association
        'created' => false,            // Managed by TimestampBehavior
        'stato' => false,              // System only - SDI integration
        'identificativo_sdi' => false, // System only - SDI integration
        'data_ora_ricezione' => false, // System only - SDI integration
        'messaggio' => false,          // System only - SDI integration
        'file_notifica' => false,      // System only - SDI integration
        'nome_file_notifica' => false, // System only - SDI integration

        // Associations
        'fattura' => false,
    ];
}
