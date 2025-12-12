<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateTenants extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('tenants');
        $table
            ->addColumn('nome', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Nome dell\'organizzazione',
            ])
            ->addColumn('tipo', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Tipo di organizzazione',
            ])
            ->addColumn('descrizione', 'text', [
                'null' => true,
            ])
            ->addColumn('codice_fiscale', 'string', [
                'limit' => 16,
                'null' => true,
            ])
            ->addColumn('partita_iva', 'string', [
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('indirizzo', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('citta', 'string', [
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('provincia', 'string', [
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('cap', 'string', [
                'limit' => 5,
                'null' => true,
            ])
            ->addColumn('telefono', 'string', [
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('email', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('pec', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('sito_web', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('logo', 'string', [
                'limit' => 255,
                'null' => true,
                'comment' => 'Path del logo',
            ])
            ->addColumn('slug', 'string', [
                'limit' => 100,
                'null' => false,
                'comment' => 'Slug univoco per URL',
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
                'comment' => 'Soft delete timestamp',
            ])
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['is_active'])
            ->addIndex(['deleted'])
            ->create();
    }
}
