<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateFatturaAllegati extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fattura_allegati');
        $table
            ->addColumn('fattura_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('nome_attachment', 'string', [
                'limit' => 60,
                'null' => false,
            ])
            ->addColumn('algoritmo_compressione', 'string', [
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('formato_attachment', 'string', [
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('descrizione_attachment', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('attachment', 'blob', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::BLOB_LONG,
                'null' => true,
                'comment' => 'Contenuto base64',
            ])
            ->addColumn('file_path', 'string', [
                'limit' => 255,
                'null' => true,
                'comment' => 'Path su disco',
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
            ->addForeignKey('fattura_id', 'fatture', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
