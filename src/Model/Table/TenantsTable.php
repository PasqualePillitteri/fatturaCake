<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\Date;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Tenants Model
 *
 * @property \App\Model\Table\ConfigurazioniSdiTable&\Cake\ORM\Association\HasOne $ConfigurazioniSdi
 * @property \App\Model\Table\AnagraficheTable&\Cake\ORM\Association\HasMany $Anagrafiche
 * @property \App\Model\Table\CategorieProdottiTable&\Cake\ORM\Association\HasMany $CategorieProdotti
 * @property \App\Model\Table\FattureTable&\Cake\ORM\Association\HasMany $Fatture
 * @property \App\Model\Table\ListiniTable&\Cake\ORM\Association\HasMany $Listini
 * @property \App\Model\Table\LogAttivitaTable&\Cake\ORM\Association\HasMany $LogAttivita
 * @property \App\Model\Table\ProdottiTable&\Cake\ORM\Association\HasMany $Prodotti
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Tenant newEmptyEntity()
 * @method \App\Model\Entity\Tenant newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Tenant> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Tenant get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Tenant findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Tenant patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Tenant> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Tenant|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Tenant saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Tenant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tenant>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Tenant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tenant> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Tenant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tenant>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Tenant>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Tenant> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TenantsTable extends Table
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

        $this->setTable('tenants');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
        $this->addBehavior('Search.Search');
        $this->addBehavior('AuditLog');

        $this->hasOne('ConfigurazioniSdi', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Anagrafiche', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('CategorieProdotti', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Fatture', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Listini', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('LogAttivita', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Prodotti', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Users', [
            'foreignKey' => 'tenant_id',
        ]);
        $this->hasMany('Abbonamenti', [
            'foreignKey' => 'tenant_id',
        ]);
    }

    /**
     * After save callback - create trial subscription for new tenants.
     *
     * @param \Cake\Event\EventInterface $event Event
     * @param \App\Model\Entity\Tenant $entity Entity
     * @param \ArrayObject $options Options
     * @return void
     */
    public function afterSave(EventInterface $event, $entity, $options): void
    {
        // Only for new tenants
        if (!$entity->isNew()) {
            return;
        }

        $this->createTrialSubscription($entity->id);
    }

    /**
     * Create a trial subscription for a tenant.
     *
     * @param int $tenantId Tenant ID
     * @return EntityInterface|null
     */
    public function createTrialSubscription(int $tenantId): ?EntityInterface
    {
        $abbonamentiTable = $this->fetchTable('Abbonamenti');
        $pianiTable = $this->fetchTable('Piani');

        // Check if tenant already has an active subscription
        $existingSubscription = $abbonamentiTable->find()
            ->where([
                'tenant_id' => $tenantId,
                'stato IN' => ['attivo', 'sospeso'],
            ])
            ->first();

        if ($existingSubscription) {
            return null;
        }

        // Find the first active plan (or create a default one)
        $piano = $pianiTable->find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => 'ASC'])
            ->first();

        if (!$piano) {
            // Create a default trial plan if none exists
            $piano = $pianiTable->newEntity([
                'nome' => 'Trial',
                'descrizione' => 'Piano di prova gratuito',
                'prezzo_mensile' => 0.00,
                'prezzo_annuale' => 0.00,
                'is_active' => true,
                'sort_order' => 0,
            ]);
            $pianiTable->save($piano);
        }

        // Create trial subscription (30 days)
        $today = Date::now();
        $endDate = $today->modify('+30 days');

        $abbonamento = $abbonamentiTable->newEntity([
            'tenant_id' => $tenantId,
            'piano_id' => $piano->id,
            'tipo' => 'mensile',
            'data_inizio' => $today,
            'data_fine' => $endDate,
            'importo' => 0.00,
            'stato' => 'attivo',
            'note' => 'Abbonamento trial creato automaticamente',
        ]);

        if ($abbonamentiTable->save($abbonamento)) {
            return $abbonamento;
        }

        return null;
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
                'fields' => ['nome', 'descrizione', 'email', 'pec'],
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
            ->scalar('nome')
            ->maxLength('nome', 255)
            ->requirePresence('nome', 'create')
            ->notEmptyString('nome');

        $validator
            ->scalar('tipo')
            ->maxLength('tipo', 50)
            ->allowEmptyString('tipo');

        $validator
            ->scalar('descrizione')
            ->allowEmptyString('descrizione');

        $validator
            ->scalar('codice_fiscale')
            ->maxLength('codice_fiscale', 16)
            ->allowEmptyString('codice_fiscale');

        $validator
            ->scalar('partita_iva')
            ->maxLength('partita_iva', 11)
            ->allowEmptyString('partita_iva');

        $validator
            ->scalar('indirizzo')
            ->maxLength('indirizzo', 255)
            ->allowEmptyString('indirizzo');

        $validator
            ->scalar('citta')
            ->maxLength('citta', 100)
            ->allowEmptyString('citta');

        $validator
            ->scalar('provincia')
            ->maxLength('provincia', 2)
            ->allowEmptyString('provincia');

        $validator
            ->scalar('cap')
            ->maxLength('cap', 5)
            ->allowEmptyString('cap');

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
            ->scalar('sito_web')
            ->maxLength('sito_web', 255)
            ->allowEmptyString('sito_web');

        $validator
            ->scalar('logo')
            ->maxLength('logo', 255)
            ->allowEmptyString('logo');

        $validator
            ->scalar('slug')
            ->maxLength('slug', 100)
            ->requirePresence('slug', 'create')
            ->notEmptyString('slug')
            ->add('slug', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['slug']), ['errorField' => 'slug']);

        return $rules;
    }
}
