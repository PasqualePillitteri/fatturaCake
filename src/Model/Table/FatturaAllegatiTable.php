<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * FatturaAllegati Model
 *
 * @property \App\Model\Table\FattureTable&\Cake\ORM\Association\BelongsTo $Fatturas
 *
 * @method \App\Model\Entity\FatturaAllegati newEmptyEntity()
 * @method \App\Model\Entity\FatturaAllegati newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\FatturaAllegati> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\FatturaAllegati get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\FatturaAllegati findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\FatturaAllegati patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\FatturaAllegati> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\FatturaAllegati|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\FatturaAllegati saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaAllegati>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaAllegati>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaAllegati>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaAllegati> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaAllegati>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaAllegati>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\FatturaAllegati>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\FatturaAllegati> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FatturaAllegatiTable extends Table
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

        $this->setTable('fattura_allegati');
        $this->setDisplayField('nome_attachment');
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
            ->scalar('nome_attachment')
            ->maxLength('nome_attachment', 60)
            ->requirePresence('nome_attachment', 'create')
            ->notEmptyFile('nome_attachment');

        $validator
            ->scalar('algoritmo_compressione')
            ->maxLength('algoritmo_compressione', 10)
            ->allowEmptyString('algoritmo_compressione');

        $validator
            ->scalar('formato_attachment')
            ->maxLength('formato_attachment', 10)
            ->allowEmptyFile('formato_attachment');

        $validator
            ->scalar('descrizione_attachment')
            ->maxLength('descrizione_attachment', 100)
            ->allowEmptyFile('descrizione_attachment');

        $validator
            ->allowEmptyFile('attachment');

        $validator
            ->scalar('file_path')
            ->maxLength('file_path', 255)
            ->allowEmptyString('file_path');

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
