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
 * Prodotti Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 * @property \App\Model\Table\CategorieProdottiTable&\Cake\ORM\Association\BelongsTo $Categorias
 * @property \App\Model\Table\ListiniTable&\Cake\ORM\Association\BelongsToMany $Listini
 *
 * @method \App\Model\Entity\Prodotti newEmptyEntity()
 * @method \App\Model\Entity\Prodotti newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Prodotti> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Prodotti get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Prodotti findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Prodotti patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Prodotti> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Prodotti|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Prodotti saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Prodotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prodotti>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prodotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prodotti> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prodotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prodotti>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Prodotti>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Prodotti> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProdottiTable extends Table
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

        $this->setTable('prodotti');
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
        $this->belongsTo('Categorias', [
            'foreignKey' => 'categoria_id',
            'className' => 'CategorieProdotti',
        ]);
        $this->belongsToMany('Listini', [
            'foreignKey' => 'prodotto_id',
            'targetForeignKey' => 'listino_id',
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
                'fields' => ['codice', 'nome', 'descrizione'],
            ])
            ->value('tipo')
            ->value('categoria_id')
            ->like('codice', [
                'before' => true,
                'after' => true,
            ])
            ->compare('prezzo_min', [
                'fields' => 'prezzo_vendita',
                'operator' => '>=',
            ])
            ->compare('prezzo_max', [
                'fields' => 'prezzo_vendita',
                'operator' => '<=',
            ])
            ->boolean('is_active')
            ->boolean('gestione_magazzino');

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
            ->integer('categoria_id')
            ->allowEmptyString('categoria_id');

        $validator
            ->scalar('tipo')
            ->notEmptyString('tipo');

        $validator
            ->scalar('codice')
            ->maxLength('codice', 50)
            ->requirePresence('codice', 'create')
            ->notEmptyString('codice');

        $validator
            ->scalar('codice_tipo')
            ->maxLength('codice_tipo', 35)
            ->allowEmptyString('codice_tipo');

        $validator
            ->scalar('codice_valore')
            ->maxLength('codice_valore', 35)
            ->allowEmptyString('codice_valore');

        $validator
            ->scalar('nome')
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('descrizione')
            ->maxLength('descrizione', 1000)
            ->allowEmptyString('descrizione');

        $validator
            ->scalar('descrizione_estesa')
            ->allowEmptyString('descrizione_estesa');

        $validator
            ->scalar('unita_misura')
            ->maxLength('unita_misura', 10)
            ->allowEmptyString('unita_misura');

        $validator
            ->decimal('prezzo_acquisto')
            ->allowEmptyString('prezzo_acquisto');

        $validator
            ->decimal('prezzo_vendita')
            ->allowEmptyString('prezzo_vendita');

        $validator
            ->boolean('prezzo_ivato')
            ->notEmptyString('prezzo_ivato');

        $validator
            ->decimal('aliquota_iva')
            ->notEmptyString('aliquota_iva');

        $validator
            ->scalar('natura')
            ->maxLength('natura', 4)
            ->allowEmptyString('natura');

        $validator
            ->scalar('riferimento_normativo')
            ->maxLength('riferimento_normativo', 100)
            ->allowEmptyString('riferimento_normativo');

        $validator
            ->boolean('soggetto_ritenuta')
            ->notEmptyString('soggetto_ritenuta');

        $validator
            ->boolean('gestione_magazzino')
            ->notEmptyString('gestione_magazzino');

        $validator
            ->decimal('giacenza')
            ->notEmptyString('giacenza');

        $validator
            ->decimal('scorta_minima')
            ->allowEmptyString('scorta_minima');

        $validator
            ->scalar('note')
            ->allowEmptyString('note');

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
        $rules->add($rules->isUnique(['tenant_id', 'codice']), ['errorField' => 'tenant_id']);
        $rules->add($rules->existsIn(['tenant_id'], 'Tenants'), ['errorField' => 'tenant_id']);

        // SECURITY: Validate that categoria_id belongs to the current tenant (prevents IDOR)
        $rules->add(function ($entity, $options) {
            if (empty($entity->categoria_id)) {
                return true; // categoria_id is optional
            }

            $context = TenantScopeBehavior::getTenantContext();
            $tenantId = $context['tenant_id'];

            // Superadmin can reference any categoria
            if ($context['role'] === 'superadmin') {
                return $this->Categorias->exists(['id' => $entity->categoria_id]);
            }

            if (!$tenantId) {
                return false;
            }

            // Check that categoria exists AND belongs to the current tenant
            return $this->Categorias->exists([
                'id' => $entity->categoria_id,
                'tenant_id' => $tenantId,
            ]);
        }, 'validTenantCategoria', [
            'errorField' => 'categoria_id',
            'message' => __('Categoria non valida o non appartenente al tenant corrente.'),
        ]);

        return $rules;
    }
}
