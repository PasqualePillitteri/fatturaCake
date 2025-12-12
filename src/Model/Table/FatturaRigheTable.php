<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Behavior\TenantScopeBehavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FatturaRighe Model
 *
 * @property \App\Model\Table\FattureTable&\Cake\ORM\Association\BelongsTo $Fatture
 * @property \App\Model\Table\ProdottiTable&\Cake\ORM\Association\BelongsTo $Prodotti
 *
 * @method \App\Model\Entity\FatturaRighe newEmptyEntity()
 * @method \App\Model\Entity\FatturaRighe newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FatturaRighe> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FatturaRighe get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FatturaRighe findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FatturaRighe patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FatturaRighe> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FatturaRighe|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FatturaRighe saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaRighe>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaRighe>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaRighe>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaRighe> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaRighe>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaRighe>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaRighe>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaRighe> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FatturaRigheTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('fattura_righe');
        $this->setDisplayField('descrizione');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog');

        $this->belongsTo('Fatture', [
            'foreignKey' => 'fattura_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Prodotti', [
            'foreignKey' => 'prodotto_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('fattura_id')
            ->notEmptyString('fattura_id');

        $validator
            ->integer('prodotto_id')
            ->allowEmptyString('prodotto_id');

        $validator
            ->integer('numero_linea')
            ->requirePresence('numero_linea', 'create')
            ->notEmptyString('numero_linea');

        $validator
            ->scalar('tipo_cessione_prestazione')
            ->maxLength('tipo_cessione_prestazione', 2)
            ->allowEmptyString('tipo_cessione_prestazione');

        $validator
            ->scalar('codice_tipo')
            ->maxLength('codice_tipo', 35)
            ->allowEmptyString('codice_tipo');

        $validator
            ->scalar('codice_valore')
            ->maxLength('codice_valore', 35)
            ->allowEmptyString('codice_valore');

        $validator
            ->scalar('descrizione')
            ->maxLength('descrizione', 1000)
            ->requirePresence('descrizione', 'create')
            ->notEmptyString('descrizione');

        $validator
            ->decimal('quantita')
            ->allowEmptyString('quantita');

        $validator
            ->scalar('unita_misura')
            ->maxLength('unita_misura', 10)
            ->allowEmptyString('unita_misura');

        $validator
            ->date('data_inizio_periodo')
            ->allowEmptyDate('data_inizio_periodo');

        $validator
            ->date('data_fine_periodo')
            ->allowEmptyDate('data_fine_periodo');

        $validator
            ->decimal('prezzo_unitario')
            ->requirePresence('prezzo_unitario', 'create')
            ->notEmptyString('prezzo_unitario');

        $validator
            ->scalar('sconto_maggiorazione_tipo')
            ->maxLength('sconto_maggiorazione_tipo', 2)
            ->allowEmptyString('sconto_maggiorazione_tipo');

        $validator
            ->decimal('sconto_maggiorazione_percentuale')
            ->allowEmptyString('sconto_maggiorazione_percentuale');

        $validator
            ->decimal('sconto_maggiorazione_importo')
            ->allowEmptyString('sconto_maggiorazione_importo');

        $validator
            ->decimal('prezzo_totale')
            ->requirePresence('prezzo_totale', 'create')
            ->notEmptyString('prezzo_totale');

        $validator
            ->decimal('aliquota_iva')
            ->notEmptyString('aliquota_iva');

        $validator
            ->scalar('natura')
            ->maxLength('natura', 4)
            ->allowEmptyString('natura');

        $validator
            ->scalar('riferimento_normativo')
            ->maxLength('riferimento_normativo', 100)
            ->allowEmptyString('riferimento_normativo');

        $validator
            ->boolean('ritenuta')
            ->notEmptyString('ritenuta');

        $validator
            ->scalar('altri_dati_tipo')
            ->maxLength('altri_dati_tipo', 10)
            ->allowEmptyString('altri_dati_tipo');

        $validator
            ->scalar('altri_dati_testo')
            ->maxLength('altri_dati_testo', 60)
            ->allowEmptyString('altri_dati_testo');

        $validator
            ->decimal('altri_dati_numero')
            ->allowEmptyString('altri_dati_numero');

        $validator
            ->date('altri_dati_data')
            ->allowEmptyDate('altri_dati_data');

        $validator
            ->integer('sort_order')
            ->notEmptyString('sort_order');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['fattura_id', 'numero_linea']), ['errorField' => 'fattura_id']);
        $rules->add($rules->existsIn(['fattura_id'], 'Fatture'), ['errorField' => 'fattura_id']);

        // SECURITY: Validate that prodotto_id belongs to the current tenant (prevents IDOR)
        $rules->add(function ($entity, $options) {
            if (empty($entity->prodotto_id)) {
                return true; // prodotto_id is optional
            }

            $context = TenantScopeBehavior::getTenantContext();
            $tenantId = $context['tenant_id'];

            // Superadmin can reference any prodotto
            if ($context['role'] === 'superadmin') {
                return $this->Prodotti->exists(['id' => $entity->prodotto_id]);
            }

            if (!$tenantId) {
                return false;
            }

            // Check that prodotto exists AND belongs to the current tenant
            return $this->Prodotti->exists([
                'id' => $entity->prodotto_id,
                'tenant_id' => $tenantId,
            ]);
        }, 'validTenantProdotto', [
            'errorField' => 'prodotto_id',
            'message' => __('Prodotto non valido o non appartenente al tenant corrente.'),
        ]);

        return $rules;
    }
}
