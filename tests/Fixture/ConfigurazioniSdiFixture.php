<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ConfigurazioniSdiFixture
 */
class ConfigurazioniSdiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'configurazioni_sdi';

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
                'aruba_username' => null,
                'aruba_password' => null,
                'ambiente' => 'test',
                'endpoint_upload' => null,
                'endpoint_stato' => null,
                'endpoint_notifiche' => null,
                'cedente_denominazione' => 'Tenant Uno SRL',
                'cedente_nome' => null,
                'cedente_cognome' => null,
                'cedente_codice_fiscale' => 'TNTUNOTEST001',
                'cedente_partita_iva' => '12345678901',
                'cedente_regime_fiscale' => 'RF01',
                'cedente_indirizzo' => 'Via Roma 1',
                'cedente_numero_civico' => '1',
                'cedente_cap' => '00100',
                'cedente_comune' => 'Roma',
                'cedente_provincia' => 'RM',
                'cedente_nazione' => 'IT',
                'cedente_telefono' => '0612345678',
                'cedente_email' => 'info@tenantuno.it',
                'cedente_pec' => 'pec@tenantuno.it',
                'codice_fiscale_trasmittente' => 'TNTUNOTEST001',
                'id_paese_trasmittente' => 'IT',
                'id_codice_trasmittente' => '12345678901',
                'progressivo_invio' => 1,
                'formato_trasmissione' => 'FPR12',
                'iban_predefinito' => 'IT60X0542811101000000123456',
                'banca_predefinita' => 'Banca Test',
                'usa_firma_digitale' => 0,
                'certificato_path' => null,
                'certificato_password' => null,
                'ultima_sincronizzazione' => null,
                'is_active' => 1,
                'created' => '2025-01-01 10:00:00',
                'modified' => '2025-01-01 10:00:00',
            ],
        ];
        parent::init();
    }
}
