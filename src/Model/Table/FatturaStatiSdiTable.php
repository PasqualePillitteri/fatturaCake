<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FatturaStatiSdi Model
 *
 * @property \App\Model\Table\FattureTable&\Cake\ORM\Association\BelongsTo $Fatturas
 *
 * @method \App\Model\Entity\FatturaStatiSdi newEmptyEntity()
 * @method \App\Model\Entity\FatturaStatiSdi newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FatturaStatiSdi> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FatturaStatiSdi get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FatturaStatiSdi findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FatturaStatiSdi patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FatturaStatiSdi> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FatturaStatiSdi|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FatturaStatiSdi saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaStatiSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaStatiSdi>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaStatiSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaStatiSdi> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaStatiSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaStatiSdi>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaStatiSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaStatiSdi> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FatturaStatiSdiTable extends Table
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

        $this->setTable('fattura_stati_sdi');
        $this->setDisplayField('stato');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog');

        $this->belongsTo('Fatturas', [
            'foreignKey' => 'fattura_id',
            'className' => 'Fatture',
            'joinType' => 'INNER',
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
            ->scalar('stato')
            ->maxLength('stato', 30)
            ->requirePresence('stato', 'create')
            ->notEmptyString('stato');

        $validator
            ->scalar('identificativo_sdi')
            ->maxLength('identificativo_sdi', 50)
            ->allowEmptyString('identificativo_sdi');

        $validator
            ->dateTime('data_ora_ricezione')
            ->allowEmptyDateTime('data_ora_ricezione');

        $validator
            ->scalar('messaggio')
            ->allowEmptyString('messaggio');

        $validator
            ->allowEmptyString('file_notifica');

        $validator
            ->scalar('nome_file_notifica')
            ->maxLength('nome_file_notifica', 255)
            ->allowEmptyString('nome_file_notifica');

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
        $rules->add($rules->existsIn(['fattura_id'], 'Fatturas'), ['errorField' => 'fattura_id']);

        return $rules;
    }
}
