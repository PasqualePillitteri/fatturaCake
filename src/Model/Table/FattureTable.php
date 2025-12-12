<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Behavior\TenantScopeBehavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Fatture Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 * @property \App\Model\Table\AnagraficheTable&\Cake\ORM\Association\BelongsTo $Anagrafiche
 *
 * @method \App\Model\Entity\Fatture newEmptyEntity()
 * @method \App\Model\Entity\Fatture newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Fatture> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Fatture get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Fatture findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Fatture patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Fatture> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Fatture|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Fatture saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Fatture>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fatture>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fatture>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fatture> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fatture>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fatture>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Fatture>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Fatture> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FattureTable extends Table
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

        $this->setTable('fatture');
        $this->setDisplayField('tipo_documento');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('Muffin/Footprint.Footprint', [
            'events' => [
                'Model.beforeSave' => [
                    'created_by' => 'new',
                    'modified_by' => 'always',
                ],
            ],
        ]);
        $this->addBehavior('Search.Search');
        $this->addBehavior('TenantScope');
        $this->addBehavior('AuditLog');

        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Anagrafiche', [
            'foreignKey' => 'anagrafica_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CreatedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
        ]);
        $this->belongsTo('ModifiedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'modified_by',
        ]);
        $this->hasMany('FatturaRighe', [
            'foreignKey' => 'fattura_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('FatturaAllegati', [
            'foreignKey' => 'fattura_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('FatturaStatiSdi', [
            'foreignKey' => 'fattura_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    /**
     * Setup search filters.
     *
     * @return \Search\Manager
     */
    public function searchManager(): Manager
    {
        $searchManager = $this->behaviors()->Search->searchManager();

        $searchManager
            ->like('q', [
                'before' => true,
                'after' => true,
                'fields' => ['numero', 'Anagrafiche.denominazione', 'causale'],
            ])
            ->value('tipo_documento')
            ->value('direzione')
            ->value('stato_sdi')
            ->value('anno')
            ->value('anagrafica_id')
            ->compare('data_from', [
                'fields' => 'data',
                'operator' => '>=',
            ])
            ->compare('data_to', [
                'fields' => 'data',
                'operator' => '<=',
            ]);

        return $searchManager;
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
            ->integer('tenant_id')
            ->notEmptyString('tenant_id');

        $validator
            ->integer('anagrafica_id')
            ->notEmptyString('anagrafica_id');

        $validator
            ->scalar('tipo_documento')
            ->maxLength('tipo_documento', 4)
            ->notEmptyString('tipo_documento');

        $validator
            ->scalar('direzione')
            ->notEmptyString('direzione');

        $validator
            ->scalar('numero')
            ->maxLength('numero', 20)
            ->requirePresence('numero', 'create')
            ->notEmptyString('numero');

        $validator
            ->date('data')
            ->requirePresence('data', 'create')
            ->notEmptyDate('data');

        $validator
            ->integer('anno')
            ->requirePresence('anno', 'create')
            ->notEmptyString('anno');

        $validator
            ->scalar('divisa')
            ->maxLength('divisa', 3)
            ->notEmptyString('divisa');

        $validator
            ->decimal('imponibile_totale')
            ->notEmptyString('imponibile_totale');

        $validator
            ->decimal('iva_totale')
            ->notEmptyString('iva_totale');

        $validator
            ->decimal('totale_documento')
            ->notEmptyString('totale_documento');

        $validator
            ->decimal('ritenuta_acconto')
            ->allowEmptyString('ritenuta_acconto');

        $validator
            ->scalar('tipo_ritenuta')
            ->maxLength('tipo_ritenuta', 4)
            ->allowEmptyString('tipo_ritenuta');

        $validator
            ->decimal('aliquota_ritenuta')
            ->allowEmptyString('aliquota_ritenuta');

        $validator
            ->scalar('causale_pagamento_ritenuta')
            ->maxLength('causale_pagamento_ritenuta', 2)
            ->allowEmptyString('causale_pagamento_ritenuta');

        $validator
            ->boolean('bollo_virtuale')
            ->notEmptyString('bollo_virtuale');

        $validator
            ->decimal('importo_bollo')
            ->allowEmptyString('importo_bollo');

        $validator
            ->boolean('cassa_previdenziale')
            ->notEmptyString('cassa_previdenziale');

        $validator
            ->scalar('tipo_cassa')
            ->maxLength('tipo_cassa', 4)
            ->allowEmptyString('tipo_cassa');

        $validator
            ->decimal('aliquota_cassa')
            ->allowEmptyString('aliquota_cassa');

        $validator
            ->decimal('importo_cassa')
            ->allowEmptyString('importo_cassa');

        $validator
            ->decimal('imponibile_cassa')
            ->allowEmptyString('imponibile_cassa');

        $validator
            ->decimal('aliquota_iva_cassa')
            ->allowEmptyString('aliquota_iva_cassa');

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
            ->scalar('causale')
            ->allowEmptyString('causale');

        $validator
            ->scalar('esigibilita_iva')
            ->maxLength('esigibilita_iva', 1)
            ->notEmptyString('esigibilita_iva');

        $validator
            ->scalar('condizioni_pagamento')
            ->maxLength('condizioni_pagamento', 4)
            ->notEmptyString('condizioni_pagamento');

        $validator
            ->scalar('modalita_pagamento')
            ->maxLength('modalita_pagamento', 4)
            ->notEmptyString('modalita_pagamento');

        $validator
            ->date('data_scadenza_pagamento')
            ->allowEmptyDate('data_scadenza_pagamento');

        $validator
            ->scalar('iban')
            ->maxLength('iban', 34)
            ->allowEmptyString('iban');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

        $validator
            ->scalar('nome_file')
            ->maxLength('nome_file', 50)
            ->allowEmptyFile('nome_file')
            ->add('nome_file', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->allowEmptyString('xml_content');

        $validator
            ->dateTime('xml_generato_at')
            ->allowEmptyDateTime('xml_generato_at');

        $validator
            ->scalar('stato_sdi')
            ->maxLength('stato_sdi', 30)
            ->notEmptyString('stato_sdi');

        $validator
            ->scalar('sdi_identificativo')
            ->maxLength('sdi_identificativo', 50)
            ->allowEmptyString('sdi_identificativo');

        $validator
            ->dateTime('sdi_data_ricezione')
            ->allowEmptyDateTime('sdi_data_ricezione');

        $validator
            ->scalar('sdi_messaggio')
            ->allowEmptyString('sdi_messaggio');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

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
        $rules->add($rules->isUnique(['tenant_id', 'anno', 'direzione', 'numero']), ['errorField' => 'tenant_id']);
        $rules->add($rules->isUnique(['nome_file'], ['allowMultipleNulls' => true]), ['errorField' => 'nome_file']);
        $rules->add($rules->existsIn(['tenant_id'], 'Tenants'), ['errorField' => 'tenant_id']);

        // SECURITY: Validate that anagrafica_id belongs to the current tenant (prevents IDOR)
        $rules->add(function ($entity, $options) {
            if (empty($entity->anagrafica_id)) {
                return true;
            }

            $context = TenantScopeBehavior::getTenantContext();
            $tenantId = $context['tenant_id'];

            // Superadmin can reference any anagrafica
            if ($context['role'] === 'superadmin') {
                return $this->Anagrafiche->exists(['id' => $entity->anagrafica_id]);
            }

            if (!$tenantId) {
                return false;
            }

            // Check that anagrafica exists AND belongs to the current tenant
            return $this->Anagrafiche->exists([
                'id' => $entity->anagrafica_id,
                'tenant_id' => $tenantId,
            ]);
        }, 'validTenantAnagrafica', [
            'errorField' => 'anagrafica_id',
            'message' => __('Anagrafica non valida o non appartenente al tenant corrente.'),
        ]);

        return $rules;
    }
}
