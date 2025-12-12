<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateLogAttivita extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('log_attivita');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'null' => true,
            ])
            ->addColumn('azione', 'string', [
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('modello', 'string', [
                'limit' => 100,
                'null' => true,
                'comment' => 'Nome tabella/model',
            ])
            ->addColumn('modello_id', 'integer', [
                'null' => true,
                'comment' => 'ID record',
            ])
            ->addColumn('dati_precedenti', 'json', [
                'null' => true,
            ])
            ->addColumn('dati_nuovi', 'json', [
                'null' => true,
            ])
            ->addColumn('ip_address', 'string', [
                'limit' => 45,
                'null' => true,
            ])
            ->addColumn('user_agent', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'null' => true,
            ])
            ->addIndex(['tenant_id'])
            ->addIndex(['user_id'])
            ->addIndex(['azione'])
            ->addIndex(['modello'])
            ->addIndex(['modello_id'])
            ->addIndex(['created'])
            ->addForeignKey('tenant_id', 'tenants', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
