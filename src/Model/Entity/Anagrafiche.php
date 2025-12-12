<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Anagrafiche Entity
 *
 * @property int $id
 * @property int $tenant_id
 * @property string $tipo
 * @property string|null $denominazione
 * @property string|null $nome
 * @property string|null $cognome
 * @property string|null $codice_fiscale
 * @property string|null $partita_iva
 * @property string $regime_fiscale
 * @property string $indirizzo
 * @property string|null $numero_civico
 * @property string $cap
 * @property string $comune
 * @property string|null $provincia
 * @property string $nazione
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $pec
 * @property string|null $codice_sdi
 * @property string|null $riferimento_amministrazione
 * @property bool $split_payment
 * @property string|null $note
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Tenant $tenant
 */
class Anagrafiche extends Entity
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
        'tipo' => true,
        'denominazione' => true,
        'nome' => true,
        'cognome' => true,
        'codice_fiscale' => true,
        'partita_iva' => true,
        'regime_fiscale' => true,
        'indirizzo' => true,
        'numero_civico' => true,
        'cap' => true,
        'comune' => true,
        'provincia' => true,
        'nazione' => true,
        'telefono' => true,
        'email' => true,
        'pec' => true,
        'codice_sdi' => true,
        'riferimento_amministrazione' => true,
        'split_payment' => true,
        'note' => true,

        // Associations - protected
        'tenant' => false,
    ];
}
