<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ConfigurazioniSdi Entity
 *
 * @property int $id
 * @property int $tenant_id
 * @property string|null $aruba_username
 * @property string|null $aruba_password
 * @property string $ambiente
 * @property string|null $endpoint_upload
 * @property string|null $endpoint_stato
 * @property string|null $endpoint_notifiche
 * @property string|null $cedente_denominazione
 * @property string|null $cedente_nome
 * @property string|null $cedente_cognome
 * @property string|null $cedente_codice_fiscale
 * @property string|null $cedente_partita_iva
 * @property string $cedente_regime_fiscale
 * @property string|null $cedente_indirizzo
 * @property string|null $cedente_numero_civico
 * @property string|null $cedente_cap
 * @property string|null $cedente_comune
 * @property string|null $cedente_provincia
 * @property string $cedente_nazione
 * @property string|null $cedente_telefono
 * @property string|null $cedente_email
 * @property string|null $cedente_pec
 * @property string|null $codice_fiscale_trasmittente
 * @property string $id_paese_trasmittente
 * @property string|null $id_codice_trasmittente
 * @property int $progressivo_invio
 * @property string $formato_trasmissione
 * @property string|null $iban_predefinito
 * @property string|null $banca_predefinita
 * @property bool $usa_firma_digitale
 * @property string|null $certificato_path
 * @property string|null $certificato_password
 * @property \Cake\I18n\DateTime|null $ultima_sincronizzazione
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Tenant $tenant
 */
class ConfigurazioniSdi extends Entity
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
        'tenant_id' => false,              // Managed by TenantScopeBehavior
        'created' => false,                // Managed by TimestampBehavior
        'modified' => false,               // Managed by TimestampBehavior
        'is_active' => false,              // Admin only
        'ultima_sincronizzazione' => false, // System only
        'progressivo_invio' => false,       // System managed

        // User-editable fields
        'aruba_username' => true,
        'aruba_password' => true,
        'ambiente' => true,
        'endpoint_upload' => true,
        'endpoint_stato' => true,
        'endpoint_notifiche' => true,
        'cedente_denominazione' => true,
        'cedente_nome' => true,
        'cedente_cognome' => true,
        'cedente_codice_fiscale' => true,
        'cedente_partita_iva' => true,
        'cedente_regime_fiscale' => true,
        'cedente_indirizzo' => true,
        'cedente_numero_civico' => true,
        'cedente_cap' => true,
        'cedente_comune' => true,
        'cedente_provincia' => true,
        'cedente_nazione' => true,
        'cedente_telefono' => true,
        'cedente_email' => true,
        'cedente_pec' => true,
        'codice_fiscale_trasmittente' => true,
        'id_paese_trasmittente' => true,
        'id_codice_trasmittente' => true,
        'formato_trasmissione' => true,
        'iban_predefinito' => true,
        'banca_predefinita' => true,
        'usa_firma_digitale' => true,
        'certificato_path' => true,
        'certificato_password' => true,

        // Associations
        'tenant' => false,
    ];
}
