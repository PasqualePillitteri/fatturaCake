<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Abbonamenti Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 * @property \App\Model\Table\PianiTable&\Cake\ORM\Association\BelongsTo $Piani
 *
 * @method \App\Model\Entity\Abbonamento newEmptyEntity()
 * @method \App\Model\Entity\Abbonamento newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Abbonamento> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Abbonamento get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Abbonamento findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Abbonamento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Abbonamento> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Abbonamento|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Abbonamento saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Abbonamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Abbonamento>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Abbonamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Abbonamento> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Abbonamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Abbonamento>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Abbonamento>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Abbonamento> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AbbonamentiTable extends Table
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

        $this->setTable('abbonamenti');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('Search.Search');

        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Piani', [
            'foreignKey' => 'piano_id',
            'joinType' => 'INNER',
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
                'fields' => ['Tenants.nome', 'Piani.nome', 'note'],
            ])
            ->value('tenant_id')
            ->value('piano_id')
            ->value('tipo')
            ->value('stato');

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
            ->requirePresence('tenant_id', 'create')
            ->notEmptyString('tenant_id');

        $validator
            ->integer('piano_id')
            ->requirePresence('piano_id', 'create')
            ->notEmptyString('piano_id');

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 20)
            ->inList('tipo', ['mensile', 'annuale'])
            ->notEmptyString('tipo');

        $validator
            ->date('data_inizio')
            ->requirePresence('data_inizio', 'create')
            ->notEmptyDate('data_inizio');

        $validator
            ->date('data_fine')
            ->allowEmptyDate('data_fine');

        $validator
            ->decimal('importo')
            ->notEmptyString('importo');

        $validator
            ->scalar('stato')
            ->maxLength('stato', 20)
            ->inList('stato', ['attivo', 'scaduto', 'cancellato', 'sospeso'])
            ->notEmptyString('stato');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

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
        $rules->add($rules->existsIn(['piano_id'], 'Piani'), ['errorField' => 'piano_id']);

        return $rules;
    }
}
