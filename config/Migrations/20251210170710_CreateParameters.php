<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Tabella parameters per configurazioni di sistema.
 *
 * Supporta parametri globali (tenant_id = null) e per tenant specifico.
 * I parametri tenant sovrascrivono quelli globali quando presente.
 */
class CreateParameters extends BaseMigration
{
    /**
     * Up Method.
     *
     * @return void
     */
    public function up(): void
    {
        // Prima elimina la tabella esistente se presente
        if ($this->hasTable('parameters')) {
            $this->table('parameters')->drop()->save();
        }

        $table = $this->table('parameters');
        $table
            ->addColumn('tenant_id', 'integer', [
                'null' => true,
                'default' => null,
                'comment' => 'NULL = globale, ID = specifico tenant',
            ])
            ->addColumn('name', 'string', [
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('value', 'integer', [
                'null' => false,
                'default' => 0,
            ])
            ->addColumn('opt1', 'string', [
                'limit' => 256,
                'null' => true,
            ])
            ->addColumn('opt2', 'string', [
                'limit' => 256,
                'null' => true,
            ])
            ->addColumn('opt3', 'string', [
                'limit' => 256,
                'null' => true,
            ])
            ->addColumn('opt4', 'string', [
                'limit' => 256,
                'null' => true,
            ])
            ->addColumn('optext', 'text', [
                'null' => true,
            ])
            ->addColumn('descr', 'string', [
                'limit' => 512,
                'null' => true,
            ])
            ->addColumn('category', 'string', [
                'limit' => 64,
                'null' => true,
                'default' => 'general',
                'comment' => 'Categoria: general, mail, appearance, system, locale, notifications, features, booking, invoicing',
            ])
            ->addColumn('display', 'boolean', [
                'null' => false,
                'default' => true,
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => true,
            ])
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
            ])
            ->addIndex(['name', 'tenant_id'], [
                'unique' => true,
                'name' => 'idx_parameters_name_tenant',
            ])
            ->addIndex(['tenant_id'], [
                'name' => 'idx_parameters_tenant_id',
            ])
            ->addIndex(['category'], [
                'name' => 'idx_parameters_category',
            ])
            ->create();

        // Foreign key verso tenants
        $table->addForeignKey('tenant_id', 'tenants', 'id', [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ])->update();
    }

    /**
     * Down Method.
     *
     * @return void
     */
    public function down(): void
    {
        $this->table('parameters')->drop()->save();
    }
}
