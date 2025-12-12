<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateAnagrafiche extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('anagrafiche');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('tipo', 'enum', [
                'values' => ['cliente', 'fornitore', 'entrambi'],
                'default' => 'cliente',
                'null' => false,
            ])
            ->addColumn('denominazione', 'string', [
                'limit' => 255,
                'null' => true,
                'comment' => 'Ragione sociale (persone giuridiche)',
            ])
            ->addColumn('nome', 'string', [
                'limit' => 100,
                'null' => true,
                'comment' => 'Nome (persone fisiche)',
            ])
            ->addColumn('cognome', 'string', [
                'limit' => 100,
                'null' => true,
                'comment' => 'Cognome (persone fisiche)',
            ])
            ->addColumn('codice_fiscale', 'string', [
                'limit' => 16,
                'null' => true,
            ])
            ->addColumn('partita_iva', 'string', [
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('regime_fiscale', 'string', [
                'limit' => 4,
                'default' => 'RF01',
                'null' => false,
                'comment' => 'Codice regime fiscale',
            ])
            ->addColumn('indirizzo', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('numero_civico', 'string', [
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('cap', 'string', [
                'limit' => 5,
                'null' => false,
            ])
            ->addColumn('comune', 'string', [
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('provincia', 'string', [
                'limit' => 2,
                'null' => true,
            ])
            ->addColumn('nazione', 'string', [
                'limit' => 2,
                'default' => 'IT',
                'null' => false,
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
            ->addColumn('codice_sdi', 'string', [
                'limit' => 7,
                'null' => true,
                'comment' => 'Codice destinatario SDI (7 caratteri)',
            ])
            ->addColumn('riferimento_amministrazione', 'string', [
                'limit' => 20,
                'null' => true,
            ])
            ->addColumn('split_payment', 'boolean', [
                'default' => false,
                'null' => false,
                'comment' => 'Soggetto a split payment',
            ])
            ->addColumn('note', 'text', [
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
            ->addIndex(['tipo'])
            ->addIndex(['partita_iva'])
            ->addIndex(['codice_fiscale'])
            ->addIndex(['is_active'])
            ->addIndex(['deleted'])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
