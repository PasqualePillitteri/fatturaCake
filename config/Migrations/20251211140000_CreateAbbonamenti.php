<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateAbbonamenti extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('abbonamenti');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('piano_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('tipo', 'string', [
                'limit' => 20,
                'default' => 'mensile',
                'null' => false,
                'comment' => 'mensile, annuale',
            ])
            ->addColumn('data_inizio', 'date', [
                'null' => false,
            ])
            ->addColumn('data_fine', 'date', [
                'null' => true,
            ])
            ->addColumn('importo', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => '0.00',
                'null' => false,
            ])
            ->addColumn('stato', 'string', [
                'limit' => 20,
                'default' => 'attivo',
                'null' => false,
                'comment' => 'attivo, scaduto, cancellato, sospeso',
            ])
            ->addColumn('note', 'text', [
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'null' => true,
            ])
            ->addColumn('deleted', 'datetime', [
                'null' => true,
            ])
            ->addIndex(['tenant_id'])
            ->addIndex(['piano_id'])
            ->addIndex(['stato'])
            ->addIndex(['data_inizio'])
            ->addIndex(['data_fine'])
            ->addIndex(['deleted'])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->addForeignKey('piano_id', 'piani', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}
