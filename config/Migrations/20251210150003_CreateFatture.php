<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateFatture extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fatture');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('anagrafica_id', 'integer', [
                'null' => false,
                'comment' => 'Cliente/Fornitore',
            ])
            ->addColumn('tipo_documento', 'string', [
                'limit' => 4,
                'default' => 'TD01',
                'null' => false,
                'comment' => 'TD01=Fattura, TD04=Nota credito, TD05=Nota debito, etc.',
            ])
            ->addColumn('direzione', 'enum', [
                'values' => ['emessa', 'ricevuta'],
                'default' => 'emessa',
                'null' => false,
            ])
            ->addColumn('numero', 'string', [
                'limit' => 20,
                'null' => false,
                'comment' => 'Numero fattura',
            ])
            ->addColumn('data', 'date', [
                'null' => false,
                'comment' => 'Data fattura',
            ])
            ->addColumn('anno', 'integer', [
                'null' => false,
                'comment' => 'Anno di riferimento',
            ])
            ->addColumn('divisa', 'string', [
                'limit' => 3,
                'default' => 'EUR',
                'null' => false,
            ])
            ->addColumn('imponibile_totale', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('iva_totale', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('totale_documento', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('ritenuta_acconto', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => true,
                'comment' => 'Importo ritenuta d\'acconto',
            ])
            ->addColumn('tipo_ritenuta', 'string', [
                'limit' => 4,
                'null' => true,
                'comment' => 'RT01=Persone fisiche, RT02=Persone giuridiche',
            ])
            ->addColumn('aliquota_ritenuta', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('causale_pagamento_ritenuta', 'string', [
                'limit' => 2,
                'null' => true,
                'comment' => 'Causale ritenuta (A, B, C, etc.)',
            ])
            ->addColumn('bollo_virtuale', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addColumn('importo_bollo', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('cassa_previdenziale', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addColumn('tipo_cassa', 'string', [
                'limit' => 4,
                'null' => true,
                'comment' => 'TC01=INPS, TC02=INARCASSA, etc.',
            ])
            ->addColumn('aliquota_cassa', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('importo_cassa', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('imponibile_cassa', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('aliquota_iva_cassa', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('sconto_maggiorazione_tipo', 'string', [
                'limit' => 2,
                'null' => true,
                'comment' => 'SC=Sconto, MG=Maggiorazione',
            ])
            ->addColumn('sconto_maggiorazione_percentuale', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('sconto_maggiorazione_importo', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('causale', 'text', [
                'null' => true,
            ])
            ->addColumn('esigibilita_iva', 'char', [
                'limit' => 1,
                'default' => 'I',
                'null' => false,
                'comment' => 'I=Immediata, D=Differita, S=Split payment',
            ])
            ->addColumn('condizioni_pagamento', 'string', [
                'limit' => 4,
                'default' => 'TP02',
                'null' => false,
                'comment' => 'TP01=Rate, TP02=Completo, TP03=Anticipo',
            ])
            ->addColumn('modalita_pagamento', 'string', [
                'limit' => 4,
                'default' => 'MP05',
                'null' => false,
                'comment' => 'MP01=Contanti, MP05=Bonifico, etc.',
            ])
            ->addColumn('data_scadenza_pagamento', 'date', [
                'null' => true,
            ])
            ->addColumn('iban', 'string', [
                'limit' => 34,
                'null' => true,
            ])
            ->addColumn('note', 'text', [
                'null' => true,
            ])
            ->addColumn('nome_file', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Nome file XML senza estensione',
            ])
            ->addColumn('xml_content', 'blob', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::BLOB_LONG,
                'null' => true,
                'comment' => 'Contenuto XML firmato',
            ])
            ->addColumn('xml_generato_at', 'datetime', [
                'null' => true,
            ])
            ->addColumn('stato_sdi', 'string', [
                'limit' => 30,
                'default' => 'bozza',
                'null' => false,
                'comment' => 'bozza, da_inviare, inviata, consegnata, accettata, rifiutata, errore',
            ])
            ->addColumn('sdi_identificativo', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Identificativo SDI',
            ])
            ->addColumn('sdi_data_ricezione', 'datetime', [
                'null' => true,
            ])
            ->addColumn('sdi_messaggio', 'text', [
                'null' => true,
                'comment' => 'Ultimo messaggio SDI',
            ])
            ->addColumn('created_by', 'integer', [
                'null' => true,
            ])
            ->addColumn('modified_by', 'integer', [
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
            ->addColumn('deleted', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addIndex(['tenant_id'])
            ->addIndex(['anagrafica_id'])
            ->addIndex(['tipo_documento'])
            ->addIndex(['direzione'])
            ->addIndex(['numero'])
            ->addIndex(['data'])
            ->addIndex(['anno'])
            ->addIndex(['stato_sdi'])
            ->addIndex(['deleted'])
            ->addIndex(['nome_file'], ['unique' => true])
            ->addIndex(['tenant_id', 'anno', 'direzione', 'numero'], [
                'unique' => true,
                'name' => 'idx_fattura_unique_numero',
            ])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('anagrafica_id', 'anagrafiche', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('created_by', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('modified_by', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
