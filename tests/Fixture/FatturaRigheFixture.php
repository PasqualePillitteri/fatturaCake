<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FatturaRigheFixture
 */
class FatturaRigheFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'fattura_righe';
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
                'fattura_id' => 1,
                'prodotto_id' => 1,
                'numero_linea' => 1,
                'tipo_cessione_prestazione' => null,
                'codice_tipo' => null,
                'codice_valore' => null,
                'descrizione' => 'Servizio di consulenza informatica',
                'quantita' => 10.00,
                'unita_misura' => 'ore',
                'data_inizio_periodo' => null,
                'data_fine_periodo' => null,
                'prezzo_unitario' => 50.00,
                'sconto_maggiorazione_tipo' => null,
                'sconto_maggiorazione_percentuale' => null,
                'sconto_maggiorazione_importo' => null,
                'prezzo_totale' => 500.00,
                'aliquota_iva' => 22.00,
                'natura' => null,
                'riferimento_normativo' => null,
                'ritenuta' => 0,
                'altri_dati_tipo' => null,
                'altri_dati_testo' => null,
                'altri_dati_numero' => null,
                'altri_dati_data' => null,
                'sort_order' => 1,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
            ],
            [
                'id' => 2,
                'fattura_id' => 1,
                'prodotto_id' => null,
                'numero_linea' => 2,
                'tipo_cessione_prestazione' => null,
                'codice_tipo' => null,
                'codice_valore' => null,
                'descrizione' => 'Sviluppo software personalizzato',
                'quantita' => 5.00,
                'unita_misura' => 'ore',
                'data_inizio_periodo' => null,
                'data_fine_periodo' => null,
                'prezzo_unitario' => 100.00,
                'sconto_maggiorazione_tipo' => null,
                'sconto_maggiorazione_percentuale' => null,
                'sconto_maggiorazione_importo' => null,
                'prezzo_totale' => 500.00,
                'aliquota_iva' => 22.00,
                'natura' => null,
                'riferimento_normativo' => null,
                'ritenuta' => 0,
                'altri_dati_tipo' => null,
                'altri_dati_testo' => null,
                'altri_dati_numero' => null,
                'altri_dati_data' => null,
                'sort_order' => 2,
                'created' => '2025-12-10 10:00:00',
                'modified' => '2025-12-10 10:00:00',
            ],
            // Riga per fattura ricevuta (id=2)
            [
                'id' => 3,
                'fattura_id' => 2,
                'prodotto_id' => null,
                'numero_linea' => 1,
                'tipo_cessione_prestazione' => null,
                'codice_tipo' => null,
                'codice_valore' => null,
                'descrizione' => 'Materiale di consumo',
                'quantita' => 50.00,
                'unita_misura' => 'PZ',
                'data_inizio_periodo' => null,
                'data_fine_periodo' => null,
                'prezzo_unitario' => 10.00,
                'sconto_maggiorazione_tipo' => null,
                'sconto_maggiorazione_percentuale' => null,
                'sconto_maggiorazione_importo' => null,
                'prezzo_totale' => 500.00,
                'aliquota_iva' => 22.00,
                'natura' => null,
                'riferimento_normativo' => null,
                'ritenuta' => 0,
                'altri_dati_tipo' => null,
                'altri_dati_testo' => null,
                'altri_dati_numero' => null,
                'altri_dati_data' => null,
                'sort_order' => 1,
                'created' => '2025-12-05 10:00:00',
                'modified' => '2025-12-05 10:00:00',
            ],
            // Righe per fattura emessa 3
            [
                'id' => 4,
                'fattura_id' => 3,
                'prodotto_id' => null,
                'numero_linea' => 1,
                'tipo_cessione_prestazione' => null,
                'codice_tipo' => null,
                'codice_valore' => null,
                'descrizione' => 'Consulenza professionale',
                'quantita' => 20.00,
                'unita_misura' => 'ore',
                'data_inizio_periodo' => null,
                'data_fine_periodo' => null,
                'prezzo_unitario' => 100.00,
                'sconto_maggiorazione_tipo' => null,
                'sconto_maggiorazione_percentuale' => null,
                'sconto_maggiorazione_importo' => null,
                'prezzo_totale' => 2000.00,
                'aliquota_iva' => 22.00,
                'natura' => null,
                'riferimento_normativo' => null,
                'ritenuta' => 0,
                'altri_dati_tipo' => null,
                'altri_dati_testo' => null,
                'altri_dati_numero' => null,
                'altri_dati_data' => null,
                'sort_order' => 1,
                'created' => '2025-11-15 10:00:00',
                'modified' => '2025-11-15 10:00:00',
            ],
        ];
        parent::init();
    }
}
