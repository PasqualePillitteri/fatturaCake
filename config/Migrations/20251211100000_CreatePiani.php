<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreatePiani extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('piani');
        $table
            ->addColumn('nome', 'string', [
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('descrizione', 'text', [
                'null' => true,
            ])
            ->addColumn('prezzo_mensile', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => '0.00',
                'null' => false,
            ])
            ->addColumn('prezzo_annuale', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'default' => '0.00',
                'null' => false,
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => true,
                'null' => false,
            ])
            ->addColumn('sort_order', 'integer', [
                'default' => 0,
                'null' => false,
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
            ->addIndex(['is_active'])
            ->addIndex(['sort_order'])
            ->addIndex(['deleted'])
            ->create();
    }
}
