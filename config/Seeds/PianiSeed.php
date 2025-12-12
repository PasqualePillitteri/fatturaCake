<?php
declare(strict_types=1);

use Cake\I18n\DateTime;
use Migrations\BaseSeed;

/**
 * Piani seed.
 */
class PianiSeed extends BaseSeed
{
    public function run(): void
    {
        $now = DateTime::now();

        $data = [
            [
                'id' => 1,
                'nome' => 'Free',
                'descrizione' => 'Piano gratuito con funzionalità base',
                'prezzo_mensile' => '0.00',
                'prezzo_annuale' => '0.00',
                'is_active' => true,
                'sort_order' => 1,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 2,
                'nome' => 'Basic',
                'descrizione' => 'Piano base per piccole organizzazioni',
                'prezzo_mensile' => '9.99',
                'prezzo_annuale' => '99.00',
                'is_active' => true,
                'sort_order' => 2,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 3,
                'nome' => 'Pro',
                'descrizione' => 'Piano professionale con tutte le funzionalità',
                'prezzo_mensile' => '29.99',
                'prezzo_annuale' => '299.00',
                'is_active' => true,
                'sort_order' => 3,
                'created' => $now,
                'modified' => $now,
            ],
            [
                'id' => 4,
                'nome' => 'Enterprise',
                'descrizione' => 'Piano enterprise con supporto dedicato',
                'prezzo_mensile' => '99.99',
                'prezzo_annuale' => '999.00',
                'is_active' => true,
                'sort_order' => 4,
                'created' => $now,
                'modified' => $now,
            ],
        ];

        $table = $this->table('piani');
        $table->insert($data)->save();
    }
}
