<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateListini extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('listini');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('descrizione', 'text', [
                'null' => true,
            ])
            ->addColumn('valuta', 'string', [
                'limit' => 3,
                'default' => 'EUR',
                'null' => false,
            ])
            ->addColumn('data_inizio', 'date', [
                'null' => true,
            ])
            ->addColumn('data_fine', 'date', [
                'null' => true,
            ])
            ->addColumn('is_default', 'boolean', [
                'default' => false,
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
            ->addIndex(['is_default'])
            ->addIndex(['is_active'])
            ->addIndex(['deleted'])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
