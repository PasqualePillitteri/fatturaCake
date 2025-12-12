<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateCategorieProdotti extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('categorie_prodotti');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('parent_id', 'integer', [
                'null' => true,
                'comment' => 'Categoria padre per gerarchia',
            ])
            ->addColumn('nome', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('descrizione', 'text', [
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
            ->addIndex(['parent_id'])
            ->addIndex(['is_active'])
            ->addIndex(['deleted'])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('parent_id', 'categorie_prodotti', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
