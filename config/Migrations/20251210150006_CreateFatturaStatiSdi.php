<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateFatturaStatiSdi extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fattura_stati_sdi');
        $table
            ->addColumn('fattura_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('stato', 'string', [
                'limit' => 30,
                'null' => false,
            ])
            ->addColumn('identificativo_sdi', 'string', [
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('data_ora_ricezione', 'datetime', [
                'null' => true,
            ])
            ->addColumn('messaggio', 'text', [
                'null' => true,
            ])
            ->addColumn('file_notifica', 'blob', [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::BLOB_LONG,
                'null' => true,
            ])
            ->addColumn('nome_file_notifica', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addIndex(['fattura_id'])
            ->addIndex(['stato'])
            ->addIndex(['identificativo_sdi'])
            ->addForeignKey('fattura_id', 'fatture', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
