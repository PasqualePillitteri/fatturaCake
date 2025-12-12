<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tenant Entity
 *
 * @property int $id
 * @property string $nome
 * @property string|null $tipo
 * @property string|null $descrizione
 * @property string|null $codice_fiscale
 * @property string|null $partita_iva
 * @property string|null $indirizzo
 * @property string|null $citta
 * @property string|null $provincia
 * @property string|null $cap
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $pec
 * @property string|null $sito_web
 * @property string|null $logo
 * @property string $slug
 * @property bool $is_active
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\ConfigurazioniSdi $configurazioni_sdi
 * @property \App\Model\Entity\Anagrafiche[] $anagrafiche
 * @property \App\Model\Entity\CategorieProdotti[] $categorie_prodotti
 * @property \App\Model\Entity\Fatture[] $fatture
 * @property \App\Model\Entity\Listini[] $listini
 * @property \App\Model\Entity\LogAttivitum[] $log_attivita
 * @property \App\Model\Entity\Prodotti[] $prodotti
 * @property \App\Model\Entity\User[] $users
 */
class Tenant extends Entity
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
        'nome' => true,
        'tipo' => true,
        'descrizione' => true,
        'codice_fiscale' => true,
        'partita_iva' => true,
        'indirizzo' => true,
        'citta' => true,
        'provincia' => true,
        'cap' => true,
        'telefono' => true,
        'email' => true,
        'pec' => true,
        'sito_web' => true,
        'logo' => true,
        'slug' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'configurazioni_sdi' => true,
        'anagrafiche' => true,
        'categorie_prodotti' => true,
        'fatture' => true,
        'listini' => true,
        'log_attivita' => true,
        'prodotti' => true,
        'users' => true,
    ];
}
