<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddTreeColumnsToCategorieProdotti extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('categorie_prodotti');
        $table->addColumn('lft', 'integer', [
            'default' => null,
            'null' => true,
            'after' => 'parent_id',
        ]);
        $table->addColumn('rght', 'integer', [
            'default' => null,
            'null' => true,
            'after' => 'lft',
        ]);
        $table->addIndex(['lft', 'rght'], ['name' => 'idx_tree']);
        $table->update();
    }
}
