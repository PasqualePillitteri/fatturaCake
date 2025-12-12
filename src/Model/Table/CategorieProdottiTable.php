<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * CategorieProdotti Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 * @property \App\Model\Table\CategorieProdottiTable&\Cake\ORM\Association\BelongsTo $ParentCategorieProdotti
 * @property \App\Model\Table\CategorieProdottiTable&\Cake\ORM\Association\HasMany $ChildCategorieProdotti
 *
 * @method \App\Model\Entity\CategorieProdotti newEmptyEntity()
 * @method \App\Model\Entity\CategorieProdotti newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\CategorieProdotti> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CategorieProdotti get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\CategorieProdotti findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\CategorieProdotti patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\CategorieProdotti> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\CategorieProdotti|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\CategorieProdotti saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\CategorieProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CategorieProdotti>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CategorieProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CategorieProdotti> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CategorieProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CategorieProdotti>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\CategorieProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\CategorieProdotti> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CategorieProdottiTable extends Table
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

        $this->setTable('categorie_prodotti');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('Search.Search');
        $this->addBehavior('Tree');
        $this->addBehavior('TenantScope');
        $this->addBehavior('AuditLog');

        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ParentCategorieProdotti', [
            'className' => 'CategorieProdotti',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('ChildCategorieProdotti', [
            'className' => 'CategorieProdotti',
            'foreignKey' => 'parent_id',
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
                'fields' => ['nome', 'descrizione'],
            ])
            ->value('parent_id')
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
            ->integer('parent_id')
            ->allowEmptyString('parent_id');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('descrizione')
            ->allowEmptyString('descrizione');

        $validator
            ->integer('sort_order')
            ->notEmptyString('sort_order');

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
        $rules->add($rules->existsIn(['parent_id'], 'ParentCategorieProdotti'), ['errorField' => 'parent_id']);

        return $rules;
    }
}
