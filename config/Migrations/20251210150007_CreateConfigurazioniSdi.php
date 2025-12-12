<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateConfigurazioniSdi extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('configurazioni_sdi');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('aruba_username', 'string', [
                'limit' => 255,
                'null' => true,
                'comment' => 'Username account Aruba',
            ])
            ->addColumn('aruba_password', 'string', [
                'limit' => 255,
                'null' => true,
                'comment' => 'Password criptata',
            ])
            ->addColumn('ambiente', 'enum', [
                'values' => ['test', 'produzione'],
                'default' => 'test',
                'null' => false,
            ])
            ->addColumn('endpoint_upload', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('endpoint_stato', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('endpoint_notifiche', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cedente_denominazione', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cedente_nome', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('cedente_cognome', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('cedente_codice_fiscale', 'string', [
                'limit' => 16,
                'null' => true,
            ])
            ->addColumn('cedente_partita_iva', 'string', [
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('cedente_regime_fiscale', 'string', [
                'limit' => 4,
                'default' => 'RF01',
                'null' => false,
            ])
            ->addColumn('cedente_indirizzo', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cedente_numero_civico', 'string', [
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('cedente_cap', 'string', [
                'limit' => 5,
                'null' => true,
            ])
            ->addColumn('cedente_comune', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('cedente_provincia', 'string', [
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('cedente_nazione', 'string', [
                'limit' => 2,
                'default' => 'IT',
                'null' => false,
            ])
            ->addColumn('cedente_telefono', 'string', [
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('cedente_email', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('cedente_pec', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('codice_fiscale_trasmittente', 'string', [
                'limit' => 16,
                'null' => true,
            ])
            ->addColumn('id_paese_trasmittente', 'string', [
                'limit' => 2,
                'default' => 'IT',
                'null' => false,
            ])
            ->addColumn('id_codice_trasmittente', 'string', [
                'limit' => 28,
                'null' => true,
            ])
            ->addColumn('progressivo_invio', 'integer', [
                'default' => 0,
                'null' => false,
                'comment' => 'Ultimo progressivo utilizzato',
            ])
            ->addColumn('formato_trasmissione', 'string', [
                'limit' => 5,
                'default' => 'FPR12',
                'null' => false,
                'comment' => 'FPA12=PA, FPR12=Privati',
            ])
            ->addColumn('iban_predefinito', 'string', [
                'limit' => 34,
                'null' => true,
            ])
            ->addColumn('banca_predefinita', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('usa_firma_digitale', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addColumn('certificato_path', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('certificato_password', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('ultima_sincronizzazione', 'datetime', [
                'null' => true,
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addIndex(['tenant_id'], ['unique' => true])
            ->addIndex(['ambiente'])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
