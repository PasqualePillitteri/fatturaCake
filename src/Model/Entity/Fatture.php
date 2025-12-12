<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Fatture Entity
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $anagrafica_id
 * @property string $tipo_documento
 * @property string $direzione
 * @property string $numero
 * @property \Cake\I18n\Date $data
 * @property int $anno
 * @property string $divisa
 * @property string $imponibile_totale
 * @property string $iva_totale
 * @property string $totale_documento
 * @property string|null $ritenuta_acconto
 * @property string|null $tipo_ritenuta
 * @property string|null $aliquota_ritenuta
 * @property string|null $causale_pagamento_ritenuta
 * @property bool $bollo_virtuale
 * @property string|null $importo_bollo
 * @property bool $cassa_previdenziale
 * @property string|null $tipo_cassa
 * @property string|null $aliquota_cassa
 * @property string|null $importo_cassa
 * @property string|null $imponibile_cassa
 * @property string|null $aliquota_iva_cassa
 * @property string|null $sconto_maggiorazione_tipo
 * @property string|null $sconto_maggiorazione_percentuale
 * @property string|null $sconto_maggiorazione_importo
 * @property string|null $causale
 * @property string $esigibilita_iva
 * @property string $condizioni_pagamento
 * @property string $modalita_pagamento
 * @property \Cake\I18n\Date|null $data_scadenza_pagamento
 * @property string|null $iban
 * @property string|null $note
 * @property string|null $nome_file
 * @property string|resource|null $xml_content
 * @property \Cake\I18n\DateTime|null $xml_generato_at
 * @property string $stato_sdi
 * @property string|null $sdi_identificativo
 * @property \Cake\I18n\DateTime|null $sdi_data_ricezione
 * @property string|null $sdi_messaggio
 * @property int|null $created_by
 * @property int|null $modified_by
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Tenant $tenant
 * @property \App\Model\Entity\Anagrafiche $anagrafica
 */
class Fatture extends Entity
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
        'tenant_id' => false,      // Managed by TenantScopeBehavior
        'created_by' => false,     // Managed by FootprintBehavior
        'modified_by' => false,    // Managed by FootprintBehavior
        'created' => false,        // Managed by TimestampBehavior
        'modified' => false,       // Managed by TimestampBehavior
        'deleted' => false,        // Managed by TrashBehavior
        'is_active' => false,      // Admin only

        // SECURITY: SDI system fields - only modified by SDI integration
        'stato_sdi' => false,
        'sdi_identificativo' => false,
        'sdi_data_ricezione' => false,
        'sdi_messaggio' => false,

        // SECURITY: XML generation fields - only modified by XML generator
        'nome_file' => false,
        'xml_content' => false,
        'xml_generato_at' => false,

        // User-editable fields
        'anagrafica_id' => true,
        'tipo_documento' => true,
        'direzione' => true,
        'numero' => true,
        'data' => true,
        'anno' => true,
        'divisa' => true,
        'imponibile_totale' => true,
        'iva_totale' => true,
        'totale_documento' => true,
        'ritenuta_acconto' => true,
        'tipo_ritenuta' => true,
        'aliquota_ritenuta' => true,
        'causale_pagamento_ritenuta' => true,
        'bollo_virtuale' => true,
        'importo_bollo' => true,
        'cassa_previdenziale' => true,
        'tipo_cassa' => true,
        'aliquota_cassa' => true,
        'importo_cassa' => true,
        'imponibile_cassa' => true,
        'aliquota_iva_cassa' => true,
        'sconto_maggiorazione_tipo' => true,
        'sconto_maggiorazione_percentuale' => true,
        'sconto_maggiorazione_importo' => true,
        'causale' => true,
        'esigibilita_iva' => true,
        'condizioni_pagamento' => true,
        'modalita_pagamento' => true,
        'data_scadenza_pagamento' => true,
        'iban' => true,
        'note' => true,

        // Associations
        'tenant' => false,
        'anagrafica' => true,
        'fattura_righe' => true,
    ];
}
