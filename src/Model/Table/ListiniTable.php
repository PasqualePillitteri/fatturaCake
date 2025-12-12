<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Listini Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 * @property \App\Model\Table\ProdottiTable&\Cake\ORM\Association\BelongsToMany $Prodotti
 *
 * @method \App\Model\Entity\Listini newEmptyEntity()
 * @method \App\Model\Entity\Listini newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Listini> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Listini get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Listini findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Listini patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Listini> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Listini|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Listini saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Listini>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Listini>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Listini>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Listini> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Listini>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Listini>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Listini>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Listini> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ListiniTable extends Table
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

        $this->setTable('listini');
        $this->setDisplayField('nome');
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
        $this->belongsToMany('Prodotti', [
            'foreignKey' => 'listino_id',
            'targetForeignKey' => 'prodotto_id',
            'joinTable' => 'listini_prodotti',
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
            ->value('valuta')
            ->boolean('is_default')
            ->boolean('is_active')
            ->compare('data_inizio_from', [
                'fields' => 'data_inizio',
                'operator' => '>=',
            ])
            ->compare('data_inizio_to', [
                'fields' => 'data_inizio',
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
            ->scalar('nome')
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('descrizione')
            ->allowEmptyString('descrizione');

        $validator
            ->scalar('valuta')
            ->maxLength('valuta', 3)
            ->notEmptyString('valuta');

        $validator
            ->date('data_inizio')
            ->allowEmptyDate('data_inizio');

        $validator
            ->date('data_fine')
            ->allowEmptyDate('data_fine');

        $validator
            ->boolean('is_default')
            ->notEmptyString('is_default');

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
