<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * FatturaRighe Entity
 *
 * @property int $id
 * @property int $fattura_id
 * @property int|null $prodotto_id
 * @property int $numero_linea
 * @property string|null $tipo_cessione_prestazione
 * @property string|null $codice_tipo
 * @property string|null $codice_valore
 * @property string $descrizione
 * @property string|null $quantita
 * @property string|null $unita_misura
 * @property \Cake\I18n\Date|null $data_inizio_periodo
 * @property \Cake\I18n\Date|null $data_fine_periodo
 * @property string $prezzo_unitario
 * @property string|null $sconto_maggiorazione_tipo
 * @property string|null $sconto_maggiorazione_percentuale
 * @property string|null $sconto_maggiorazione_importo
 * @property string $prezzo_totale
 * @property string $aliquota_iva
 * @property string|null $natura
 * @property string|null $riferimento_normativo
 * @property bool $ritenuta
 * @property string|null $altri_dati_tipo
 * @property string|null $altri_dati_testo
 * @property string|null $altri_dati_numero
 * @property \Cake\I18n\Date|null $altri_dati_data
 * @property int $sort_order
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Fatture $fattura
 * @property \App\Model\Entity\Prodotto $prodotto
 */
class FatturaRighe extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        // SECURITY: System/protected fields
        'fattura_id' => false, // FK to parent - managed by association
        'created' => false,    // Managed by TimestampBehavior
        'modified' => false,   // Managed by TimestampBehavior

        // User-editable fields
        'prodotto_id' => true,
        'numero_linea' => true,
        'tipo_cessione_prestazione' => true,
        'codice_tipo' => true,
        'codice_valore' => true,
        'descrizione' => true,
        'quantita' => true,
        'unita_misura' => true,
        'data_inizio_periodo' => true,
        'data_fine_periodo' => true,
        'prezzo_unitario' => true,
        'sconto_maggiorazione_tipo' => true,
        'sconto_maggiorazione_percentuale' => true,
        'sconto_maggiorazione_importo' => true,
        'prezzo_totale' => true,
        'aliquota_iva' => true,
        'natura' => true,
        'riferimento_normativo' => true,
        'ritenuta' => true,
        'altri_dati_tipo' => true,
        'altri_dati_testo' => true,
        'altri_dati_numero' => true,
        'altri_dati_data' => true,
        'sort_order' => true,

        // Associations
        'fattura' => false,
        'prodotto' => true,
    ];
}
