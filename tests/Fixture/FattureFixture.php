<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FattureFixture
 */
class FattureFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'fatture';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'tenant_id' => 1,
                'anagrafica_id' => 1,
                'tipo_documento' => 'TD01',
                'direzione' => 'emessa',
                'numero' => '1',
                'data' => '2025-12-10',
                'anno' => 2025,
                'divisa' => 'EUR',
                'imponibile_totale' => 1000.00,
                'iva_totale' => 220.00,
                'totale_documento' => 1220.00,
                'ritenuta_acconto' => null,
                'tipo_ritenuta' => null,
                'aliquota_ritenuta' => null,
                'causale_pagamento_ritenuta' => null,
                'bollo_virtuale' => 0,
                'importo_bollo' => null,
                'cassa_previdenziale' => 0,
                'tipo_cassa' => null,
                'aliquota_cassa' => null,
                'importo_cassa' => null,
                'imponibile_cassa' => null,
                'aliquota_iva_cassa' => null,
                'sconto_maggiorazione_tipo' => null,
                'sconto_maggiorazione_percentuale' => null,
                'sconto_maggiorazione_importo' => null,
                'causale' => 'Fattura di test',
                'esigibilita_iva' => 'I',
                'condizioni_pagamento' => 'TP02',
                'modalita_pagamento' => 'MP05',
                'data_scadenza_pagamento' => '2026-01-10',
                'iban' => 'IT60X0542811101000000123456',
                'note' => null,
                'nome_file' => null,
                'xml_content' => null,
                'xml_generato_at' => null,
                'stato_sdi' => 'bozza',
                'sdi_identificativo' => null,
                'sdi_data_ricezione' => null,
                'sdi_messaggio' => null,
                'created_by' => 1,
                'modified_by' => 1,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
            // Fattura ricevuta (passiva)
            [
                'id' => 2,
                'tenant_id' => 1,
                'anagrafica_id' => 2,
                'tipo_documento' => 'TD01',
                'direzione' => 'ricevuta',
                'numero' => 'FORN-001',
                'data' => '2025-12-05',
                'anno' => 2025,
                'divisa' => 'EUR',
                'imponibile_totale' => 500.00,
                'iva_totale' => 110.00,
                'totale_documento' => 610.00,
                'ritenuta_acconto' => null,
                'tipo_ritenuta' => null,
                'aliquota_ritenuta' => null,
                'causale_pagamento_ritenuta' => null,
                'bollo_virtuale' => 0,
                'importo_bollo' => null,
                'cassa_previdenziale' => 0,
                'tipo_cassa' => null,
                'aliquota_cassa' => null,
                'importo_cassa' => null,
                'imponibile_cassa' => null,
                'aliquota_iva_cassa' => null,
                'sconto_maggiorazione_tipo' => null,
                'sconto_maggiorazione_percentuale' => null,
                'sconto_maggiorazione_importo' => null,
                'causale' => 'Acquisto materiali',
                'esigibilita_iva' => 'I',
                'condizioni_pagamento' => 'TP02',
                'modalita_pagamento' => 'MP05',
                'data_scadenza_pagamento' => '2026-01-05',
                'iban' => null,
                'note' => null,
                'nome_file' => null,
                'xml_content' => null,
                'xml_generato_at' => null,
                'stato_sdi' => 'ricevuta',
                'sdi_identificativo' => null,
                'sdi_data_ricezione' => null,
                'sdi_messaggio' => null,
                'created_by' => 2,
                'modified_by' => 2,
                'is_active' => 1,
                'created' => '2025-12-05 10:00:00',
                'modified' => '2025-12-05 10:00:00',
                'deleted' => null,
            ],
            // Seconda fattura emessa per test filtri
            [
                'id' => 3,
                'tenant_id' => 1,
                'anagrafica_id' => 1,
                'tipo_documento' => 'TD01',
                'direzione' => 'emessa',
                'numero' => '2',
                'data' => '2025-11-15',
                'anno' => 2025,
                'divisa' => 'EUR',
                'imponibile_totale' => 2000.00,
                'iva_totale' => 440.00,
                'totale_documento' => 2440.00,
                'ritenuta_acconto' => null,
                'tipo_ritenuta' => null,
                'aliquota_ritenuta' => null,
                'causale_pagamento_ritenuta' => null,
                'bollo_virtuale' => 0,
                'importo_bollo' => null,
                'cassa_previdenziale' => 0,
                'tipo_cassa' => null,
                'aliquota_cassa' => null,
                'importo_cassa' => null,
                'imponibile_cassa' => null,
                'aliquota_iva_cassa' => null,
                'sconto_maggiorazione_tipo' => null,
                'sconto_maggiorazione_percentuale' => null,
                'sconto_maggiorazione_importo' => null,
                'causale' => 'Consulenza tecnica',
                'esigibilita_iva' => 'I',
                'condizioni_pagamento' => 'TP02',
                'modalita_pagamento' => 'MP05',
                'data_scadenza_pagamento' => '2025-12-15',
                'iban' => 'IT60X0542811101000000123456',
                'note' => null,
                'nome_file' => null,
                'xml_content' => null,
                'xml_generato_at' => null,
                'stato_sdi' => 'consegnata',
                'sdi_identificativo' => 'SDI123456789',
                'sdi_data_ricezione' => '2025-11-16 09:00:00',
                'sdi_messaggio' => null,
                'created_by' => 2,
                'modified_by' => 2,
                'is_active' => 1,
                'created' => '2025-11-15 10:00:00',
                'modified' => '2025-11-16 09:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
