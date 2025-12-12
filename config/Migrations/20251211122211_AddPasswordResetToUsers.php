<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddPasswordResetToUsers extends BaseMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('reset_token', 'string', [
            'default' => null,
            'limit' => 100,
            'null' => true,
        ]);
        $table->addColumn('reset_token_expires', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->addIndex(['reset_token'], [
            'name' => 'idx_users_reset_token',
            'unique' => true,
        ]);
        $table->update();
    }
}
