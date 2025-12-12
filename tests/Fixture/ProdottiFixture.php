<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProdottiFixture
 */
class ProdottiFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'prodotti';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'tenant_id' => 1,
                'categoria_id' => 2,
                'tipo' => 'servizio',
                'codice' => 'CONS001',
                'codice_tipo' => null,
                'codice_valore' => null,
                'nome' => 'Consulenza IT',
                'descrizione' => 'Servizio di consulenza informatica',
                'descrizione_estesa' => 'Servizio di consulenza informatica professionale per aziende',
                'unita_misura' => 'ore',
                'prezzo_acquisto' => null,
                'prezzo_vendita' => 50.00,
                'prezzo_ivato' => 0,
                'aliquota_iva' => 22.00,
                'natura' => null,
                'riferimento_normativo' => null,
                'soggetto_ritenuta' => 0,
                'gestione_magazzino' => 0,
                'giacenza' => 0.00,
                'scorta_minima' => 0.00,
                'note' => null,
                'sort_order' => 1,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
            [
                'id' => 2,
                'tenant_id' => 1,
                'categoria_id' => 3,
                'tipo' => 'servizio',
                'codice' => 'DEV001',
                'codice_tipo' => null,
                'codice_valore' => null,
                'nome' => 'Sviluppo Software',
                'descrizione' => 'Sviluppo software personalizzato',
                'descrizione_estesa' => 'Sviluppo di applicazioni web e mobile personalizzate',
                'unita_misura' => 'ore',
                'prezzo_acquisto' => null,
                'prezzo_vendita' => 100.00,
                'prezzo_ivato' => 0,
                'aliquota_iva' => 22.00,
                'natura' => null,
                'riferimento_normativo' => null,
                'soggetto_ritenuta' => 0,
                'gestione_magazzino' => 0,
                'giacenza' => 0.00,
                'scorta_minima' => 0.00,
                'note' => null,
                'sort_order' => 2,
                'is_active' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
                'deleted' => null,
            ],
        ];
        parent::init();
    }
}
