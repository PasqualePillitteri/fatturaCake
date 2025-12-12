<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateProdotti extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('prodotti');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('categoria_id', 'integer', [
                'null' => true,
            ])
            ->addColumn('tipo', 'enum', [
                'values' => ['prodotto', 'servizio'],
                'default' => 'servizio',
                'null' => false,
            ])
            ->addColumn('codice', 'string', [
                'limit' => 50,
                'null' => false,
                'comment' => 'Codice articolo',
            ])
            ->addColumn('codice_tipo', 'string', [
                'limit' => 35,
                'null' => true,
                'comment' => 'Codice tipo FatturaPA (es. TARIC, CPV)',
            ])
            ->addColumn('codice_valore', 'string', [
                'limit' => 35,
                'null' => true,
                'comment' => 'Valore codice FatturaPA',
            ])
            ->addColumn('nome', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('descrizione', 'string', [
                'limit' => 1000,
                'null' => true,
                'comment' => 'Descrizione per fattura',
            ])
            ->addColumn('descrizione_estesa', 'text', [
                'null' => true,
                'comment' => 'Descrizione dettagliata',
            ])
            ->addColumn('unita_misura', 'string', [
                'limit' => 10,
                'null' => true,
                'comment' => 'UM per FatturaPA',
            ])
            ->addColumn('prezzo_acquisto', 'decimal', [
                'precision' => 15,
                'scale' => 8,
                'null' => true,
            ])
            ->addColumn('prezzo_vendita', 'decimal', [
                'precision' => 15,
                'scale' => 8,
                'null' => true,
            ])
            ->addColumn('prezzo_ivato', 'boolean', [
                'default' => false,
                'null' => false,
                'comment' => 'Il prezzo include IVA',
            ])
            ->addColumn('aliquota_iva', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'default' => 22.00,
                'null' => false,
            ])
            ->addColumn('natura', 'string', [
                'limit' => 4,
                'null' => true,
                'comment' => 'Natura IVA se aliquota=0 (N1-N7)',
            ])
            ->addColumn('riferimento_normativo', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('soggetto_ritenuta', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addColumn('gestione_magazzino', 'boolean', [
                'default' => false,
                'null' => false,
            ])
            ->addColumn('giacenza', 'decimal', [
                'precision' => 15,
                'scale' => 5,
                'default' => 0,
                'null' => false,
            ])
            ->addColumn('scorta_minima', 'decimal', [
                'precision' => 15,
                'scale' => 5,
                'null' => true,
            ])
            ->addColumn('note', 'text', [
                'null' => true,
            ])
            ->addColumn('sort_order', 'integer', [
                'default' => 0,
                'null' => false,
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
            ->addIndex(['categoria_id'])
            ->addIndex(['tipo'])
            ->addIndex(['nome'])
            ->addIndex(['aliquota_iva'])
            ->addIndex(['is_active'])
            ->addIndex(['deleted'])
            ->addIndex(['tenant_id', 'codice'], ['unique' => true])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('categoria_id', 'categorie_prodotti', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->create();

        // Aggiungo la foreign key per fattura_righe.prodotto_id
        $this->table('fattura_righe')
            ->addForeignKey('prodotto_id', 'prodotti', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->update();
    }
}
