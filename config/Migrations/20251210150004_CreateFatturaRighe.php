<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateFatturaRighe extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fattura_righe');
        $table
            ->addColumn('fattura_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('prodotto_id', 'integer', [
                'null' => true,
                'comment' => 'Riferimento catalogo prodotti',
            ])
            ->addColumn('numero_linea', 'integer', [
                'null' => false,
            ])
            ->addColumn('tipo_cessione_prestazione', 'string', [
                'limit' => 2,
                'null' => true,
                'comment' => 'SC=Sconto, PR=Premio, AB=Abbuono, AC=Spesa accessoria',
            ])
            ->addColumn('codice_tipo', 'string', [
                'limit' => 35,
                'null' => true,
                'comment' => 'Es: TARIC, CPV, EAN, etc.',
            ])
            ->addColumn('codice_valore', 'string', [
                'limit' => 35,
                'null' => true,
            ])
            ->addColumn('descrizione', 'string', [
                'limit' => 1000,
                'null' => false,
            ])
            ->addColumn('quantita', 'decimal', [
                'precision' => 15,
                'scale' => 5,
                'null' => true,
            ])
            ->addColumn('unita_misura', 'string', [
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('data_inizio_periodo', 'date', [
                'null' => true,
            ])
            ->addColumn('data_fine_periodo', 'date', [
                'null' => true,
            ])
            ->addColumn('prezzo_unitario', 'decimal', [
                'precision' => 15,
                'scale' => 8,
                'null' => false,
                'comment' => 'Prezzo unitario con max 8 decimali',
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
            ->addColumn('prezzo_totale', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => false,
                'comment' => 'Prezzo totale linea (quantita * prezzo - sconti)',
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
                'comment' => 'Natura operazione IVA (N1-N7) se aliquota=0',
            ])
            ->addColumn('riferimento_normativo', 'string', [
                'limit' => 100,
                'null' => true,
                'comment' => 'Riferimento normativo per esenzione',
            ])
            ->addColumn('ritenuta', 'boolean', [
                'default' => false,
                'null' => false,
                'comment' => 'Soggetto a ritenuta',
            ])
            ->addColumn('altri_dati_tipo', 'string', [
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('altri_dati_testo', 'string', [
                'limit' => 60,
                'null' => true,
            ])
            ->addColumn('altri_dati_numero', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('altri_dati_data', 'date', [
                'null' => true,
            ])
            ->addColumn('sort_order', 'integer', [
                'default' => 0,
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
            ->addIndex(['fattura_id'])
            ->addIndex(['prodotto_id'])
            ->addIndex(['numero_linea'])
            ->addIndex(['aliquota_iva'])
            ->addIndex(['fattura_id', 'numero_linea'], ['unique' => true])
            ->addForeignKey('fattura_id', 'fatture', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
