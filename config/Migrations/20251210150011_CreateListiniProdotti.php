<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateListiniProdotti extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('listini_prodotti');
        $table
            ->addColumn('listino_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('prodotto_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('prezzo', 'decimal', [
                'precision' => 15,
                'scale' => 8,
                'null' => false,
            ])
            ->addColumn('prezzo_minimo', 'decimal', [
                'precision' => 15,
                'scale' => 8,
                'null' => true,
            ])
            ->addColumn('sconto_massimo', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addIndex(['listino_id'])
            ->addIndex(['prodotto_id'])
            ->addIndex(['listino_id', 'prodotto_id'], ['unique' => true])
            ->addForeignKey('listino_id', 'listini', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('prodotto_id', 'prodotti', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
