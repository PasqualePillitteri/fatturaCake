<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LogAttivitum Entity
 *
 * @property int $id
 * @property int|null $tenant_id
 * @property int|null $user_id
 * @property string $azione
 * @property string|null $modello
 * @property int|null $modello_id
 * @property string|null $dati_precedenti
 * @property string|null $dati_nuovi
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Tenant $tenant
 * @property \App\Model\Entity\User $user
 */
class LogAttivitum extends Entity
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
        // SECURITY: Audit log fields should only be set by the system
        'tenant_id' => false,
        'user_id' => false,
        'azione' => true,
        'modello' => true,
        'modello_id' => true,
        'dati_precedenti' => true,
        'dati_nuovi' => true,
        'ip_address' => true,
        'user_agent' => true,
        'created' => true,
        'tenant' => false,
        'user' => false,
    ];
}
