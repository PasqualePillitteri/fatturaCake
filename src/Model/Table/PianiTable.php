<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Piani Model
 *
 * @property \App\Model\Table\AbbonamentiTable&\Cake\ORM\Association\HasMany $Abbonamenti
 *
 * @method \App\Model\Entity\Piano newEmptyEntity()
 * @method \App\Model\Entity\Piano newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Piano> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Piano get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Piano findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Piano patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Piano> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Piano|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Piano saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Piano>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Piano>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Piano>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Piano> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Piano>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Piano>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Piano>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Piano> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PianiTable extends Table
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

        $this->setTable('piani');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('Search.Search');

        $this->hasMany('Abbonamenti', [
            'foreignKey' => 'piano_id',
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
            ->scalar('nome')
            ->maxLength('nome', 100)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('descrizione')
            ->allowEmptyString('descrizione');

        $validator
            ->decimal('prezzo_mensile')
            ->notEmptyString('prezzo_mensile');

        $validator
            ->decimal('prezzo_annuale')
            ->notEmptyString('prezzo_annuale');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

        $validator
            ->integer('sort_order')
            ->notEmptyString('sort_order');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        return $validator;
    }
}
