<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Abbonamento Entity
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $piano_id
 * @property string $tipo
 * @property \Cake\I18n\Date $data_inizio
 * @property \Cake\I18n\Date|null $data_fine
 * @property string $importo
 * @property string $stato
 * @property string|null $note
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $deleted
 *
 * @property \App\Model\Entity\Tenant $tenant
 * @property \App\Model\Entity\Piano $piano
 */
class Abbonamento extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'tenant_id' => false, // SECURITY: protected from mass assignment
        'piano_id' => true,
        'tipo' => true,
        'data_inizio' => true,
        'data_fine' => true,
        'importo' => true,
        'stato' => true,
        'note' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'tenant' => true,
        'piano' => true,
    ];

    /**
     * Verifica se l'abbonamento è attivo
     *
     * @return bool
     */
    public function isAttivo(): bool
    {
        return $this->stato === 'attivo';
    }

    /**
     * Verifica se l'abbonamento è scaduto
     *
     * @return bool
     */
    public function isScaduto(): bool
    {
        if ($this->data_fine === null) {
            return false;
        }
        return $this->data_fine->isPast();
    }
}
