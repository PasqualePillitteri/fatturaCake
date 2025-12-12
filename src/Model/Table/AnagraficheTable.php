<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Anagrafiche Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 *
 * @method \App\Model\Entity\Anagrafiche newEmptyEntity()
 * @method \App\Model\Entity\Anagrafiche newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Anagrafiche> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Anagrafiche get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Anagrafiche findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Anagrafiche patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Anagrafiche> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Anagrafiche|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Anagrafiche saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Anagrafiche>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Anagrafiche>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Anagrafiche>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Anagrafiche> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Anagrafiche>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Anagrafiche>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Anagrafiche>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Anagrafiche> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AnagraficheTable extends Table
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

        $this->setTable('anagrafiche');
        $this->setDisplayField('tipo');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('Search.Search');
        $this->addBehavior('TenantScope');
        $this->addBehavior('AuditLog');

        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Fatture', [
            'foreignKey' => 'anagrafica_id',
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
                'fields' => ['denominazione', 'nome', 'cognome', 'email', 'pec'],
            ])
            ->value('tipo')
            ->like('partita_iva', [
                'before' => true,
                'after' => true,
            ])
            ->like('codice_fiscale', [
                'before' => true,
                'after' => true,
            ])
            ->like('comune')
            ->value('provincia')
            ->boolean('is_active');

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
            ->scalar('tipo')
            ->notEmptyString('tipo');

        $validator
            ->scalar('denominazione')
            ->maxLength('denominazione', 255)
            ->allowEmptyString('denominazione');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 100)
            ->allowEmptyString('nome');

        $validator
            ->scalar('cognome')
            ->maxLength('cognome', 100)
            ->allowEmptyString('cognome');

        $validator
            ->scalar('codice_fiscale')
            ->maxLength('codice_fiscale', 16)
            ->allowEmptyString('codice_fiscale');

        $validator
            ->scalar('partita_iva')
            ->maxLength('partita_iva', 11)
            ->allowEmptyString('partita_iva');

        $validator
            ->scalar('regime_fiscale')
            ->maxLength('regime_fiscale', 4)
            ->notEmptyString('regime_fiscale');

        $validator
            ->scalar('indirizzo')
            ->maxLength('indirizzo', 255)
            ->requirePresence('indirizzo', 'create')
            ->notEmptyString('indirizzo');

        $validator
            ->scalar('numero_civico')
            ->maxLength('numero_civico', 10)
            ->allowEmptyString('numero_civico');

        $validator
            ->scalar('cap')
            ->maxLength('cap', 5)
            ->requirePresence('cap', 'create')
            ->notEmptyString('cap');

        $validator
            ->scalar('comune')
            ->maxLength('comune', 100)
            ->requirePresence('comune', 'create')
            ->notEmptyString('comune');

        $validator
            ->scalar('provincia')
            ->maxLength('provincia', 2)
            ->allowEmptyString('provincia');

        $validator
            ->scalar('nazione')
            ->maxLength('nazione', 2)
            ->notEmptyString('nazione');

        $validator
            ->scalar('telefono')
            ->maxLength('telefono', 20)
            ->allowEmptyString('telefono');

        $validator
            ->email('email')
            ->allowEmptyString('email');

        $validator
            ->scalar('pec')
            ->maxLength('pec', 255)
            ->allowEmptyString('pec');

        $validator
            ->scalar('codice_sdi')
            ->maxLength('codice_sdi', 7)
            ->allowEmptyString('codice_sdi');

        $validator
            ->scalar('riferimento_amministrazione')
            ->maxLength('riferimento_amministrazione', 20)
            ->allowEmptyString('riferimento_amministrazione');

        $validator
            ->boolean('split_payment')
            ->notEmptyString('split_payment');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

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
        $rules->add($rules->existsIn(['tenant_id'], 'Tenants'), ['errorField' => 'tenant_id']);

        return $rules;
    }
}
