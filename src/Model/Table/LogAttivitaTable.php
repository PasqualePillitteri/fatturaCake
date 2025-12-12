<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * LogAttivita Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\LogAttivitum newEmptyEntity()
 * @method \App\Model\Entity\LogAttivitum newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\LogAttivitum> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\LogAttivitum get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\LogAttivitum findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\LogAttivitum patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\LogAttivitum> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\LogAttivitum|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\LogAttivitum saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\LogAttivitum>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LogAttivitum>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LogAttivitum>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LogAttivitum> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LogAttivitum>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LogAttivitum>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\LogAttivitum>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\LogAttivitum> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LogAttivitaTable extends Table
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

        $this->setTable('log_attivita');
        $this->setDisplayField('azione');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('TenantScope');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->integer('tenant_id')
            ->allowEmptyString('tenant_id');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->scalar('azione')
            ->maxLength('azione', 50)
            ->requirePresence('azione', 'create')
            ->notEmptyString('azione');

        $validator
            ->scalar('modello')
            ->maxLength('modello', 100)
            ->allowEmptyString('modello');

        $validator
            ->integer('modello_id')
            ->allowEmptyString('modello_id');

        $validator
            ->scalar('dati_precedenti')
            ->maxLength('dati_precedenti', 4294967295)
            ->allowEmptyString('dati_precedenti');

        $validator
            ->scalar('dati_nuovi')
            ->maxLength('dati_nuovi', 4294967295)
            ->allowEmptyString('dati_nuovi');

        $validator
            ->scalar('ip_address')
            ->maxLength('ip_address', 45)
            ->allowEmptyString('ip_address');

        $validator
            ->scalar('user_agent')
            ->maxLength('user_agent', 255)
            ->allowEmptyString('user_agent');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }
}
