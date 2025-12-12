<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Behavior\TenantScopeBehavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ListiniProdotti Model
 *
 * @property \App\Model\Table\ListiniTable&\Cake\ORM\Association\BelongsTo $Listinos
 * @property \App\Model\Table\ProdottiTable&\Cake\ORM\Association\BelongsTo $Prodottos
 *
 * @method \App\Model\Entity\ListiniProdotti newEmptyEntity()
 * @method \App\Model\Entity\ListiniProdotti newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ListiniProdotti> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ListiniProdotti get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ListiniProdotti findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ListiniProdotti patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ListiniProdotti> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ListiniProdotti|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ListiniProdotti saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ListiniProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ListiniProdotti>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ListiniProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ListiniProdotti> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ListiniProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ListiniProdotti>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ListiniProdotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ListiniProdotti> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ListiniProdottiTable extends Table
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

        $this->setTable('listini_prodotti');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('AuditLog');

        $this->belongsTo('Listini', [
            'foreignKey' => 'listino_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Prodotti', [
            'foreignKey' => 'prodotto_id',
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
            ->integer('listino_id')
            ->notEmptyString('listino_id');

        $validator
            ->integer('prodotto_id')
            ->notEmptyString('prodotto_id');

        $validator
            ->decimal('prezzo')
            ->requirePresence('prezzo', 'create')
            ->notEmptyString('prezzo');

        $validator
            ->decimal('prezzo_minimo')
            ->allowEmptyString('prezzo_minimo');

        $validator
            ->decimal('sconto_massimo')
            ->allowEmptyString('sconto_massimo');

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
        $rules->add($rules->isUnique(['listino_id', 'prodotto_id']), ['errorField' => 'listino_id']);

        // SECURITY: Validate that listino_id belongs to the current tenant (prevents IDOR)
        $rules->add(function ($entity, $options) {
            if (empty($entity->listino_id)) {
                return false; // listino_id is required
            }

            $context = TenantScopeBehavior::getTenantContext();
            $tenantId = $context['tenant_id'];

            // Superadmin can reference any listino
            if ($context['role'] === 'superadmin') {
                return $this->Listini->exists(['id' => $entity->listino_id]);
            }

            if (!$tenantId) {
                return false;
            }

            return $this->Listini->exists([
                'id' => $entity->listino_id,
                'tenant_id' => $tenantId,
            ]);
        }, 'validTenantListino', [
            'errorField' => 'listino_id',
            'message' => __('Listino non valido o non appartenente al tenant corrente.'),
        ]);

        // SECURITY: Validate that prodotto_id belongs to the current tenant (prevents IDOR)
        $rules->add(function ($entity, $options) {
            if (empty($entity->prodotto_id)) {
                return false; // prodotto_id is required
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
