<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * ConfigurazioniSdi Model
 *
 * @property \App\Model\Table\TenantsTable&\Cake\ORM\Association\BelongsTo $Tenants
 *
 * @method \App\Model\Entity\ConfigurazioniSdi newEmptyEntity()
 * @method \App\Model\Entity\ConfigurazioniSdi newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\ConfigurazioniSdi> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ConfigurazioniSdi get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\ConfigurazioniSdi findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\ConfigurazioniSdi patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\ConfigurazioniSdi> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\ConfigurazioniSdi|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\ConfigurazioniSdi saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\ConfigurazioniSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConfigurazioniSdi>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ConfigurazioniSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConfigurazioniSdi> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ConfigurazioniSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConfigurazioniSdi>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\ConfigurazioniSdi>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\ConfigurazioniSdi> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ConfigurazioniSdiTable extends Table
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

        $this->setTable('configurazioni_sdi');
        $this->setDisplayField('ambiente');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->addBehavior('TenantScope');
        $this->addBehavior('AuditLog');

        $this->belongsTo('Tenants', [
            'foreignKey' => 'tenant_id',
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
                'fields' => ['cedente_denominazione', 'cedente_partita_iva', 'cedente_email'],
            ])
            ->value('ambiente')
            ->boolean('is_active')
            ->boolean('usa_firma_digitale');

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
            ->notEmptyString('tenant_id')
            ->add('tenant_id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->scalar('aruba_username')
            ->maxLength('aruba_username', 255)
            ->allowEmptyString('aruba_username');

        $validator
            ->scalar('aruba_password')
            ->maxLength('aruba_password', 255)
            ->allowEmptyString('aruba_password');

        $validator
            ->scalar('ambiente')
            ->notEmptyString('ambiente');

        $validator
            ->scalar('endpoint_upload')
            ->maxLength('endpoint_upload', 255)
            ->allowEmptyString('endpoint_upload');

        $validator
            ->scalar('endpoint_stato')
            ->maxLength('endpoint_stato', 255)
            ->allowEmptyString('endpoint_stato');

        $validator
            ->scalar('endpoint_notifiche')
            ->maxLength('endpoint_notifiche', 255)
            ->allowEmptyString('endpoint_notifiche');

        $validator
            ->scalar('cedente_denominazione')
            ->maxLength('cedente_denominazione', 255)
            ->allowEmptyString('cedente_denominazione');

        $validator
            ->scalar('cedente_nome')
            ->maxLength('cedente_nome', 100)
            ->allowEmptyString('cedente_nome');

        $validator
            ->scalar('cedente_cognome')
            ->maxLength('cedente_cognome', 100)
            ->allowEmptyString('cedente_cognome');

        $validator
            ->scalar('cedente_codice_fiscale')
            ->maxLength('cedente_codice_fiscale', 16)
            ->allowEmptyString('cedente_codice_fiscale');

        $validator
            ->scalar('cedente_partita_iva')
            ->maxLength('cedente_partita_iva', 11)
            ->allowEmptyString('cedente_partita_iva');

        $validator
            ->scalar('cedente_regime_fiscale')
            ->maxLength('cedente_regime_fiscale', 4)
            ->notEmptyString('cedente_regime_fiscale');

        $validator
            ->scalar('cedente_indirizzo')
            ->maxLength('cedente_indirizzo', 255)
            ->allowEmptyString('cedente_indirizzo');

        $validator
            ->scalar('cedente_numero_civico')
            ->maxLength('cedente_numero_civico', 10)
            ->allowEmptyString('cedente_numero_civico');

        $validator
            ->scalar('cedente_cap')
            ->maxLength('cedente_cap', 5)
            ->allowEmptyString('cedente_cap');

        $validator
            ->scalar('cedente_comune')
            ->maxLength('cedente_comune', 100)
            ->allowEmptyString('cedente_comune');

        $validator
            ->scalar('cedente_provincia')
            ->maxLength('cedente_provincia', 2)
            ->allowEmptyString('cedente_provincia');

        $validator
            ->scalar('cedente_nazione')
            ->maxLength('cedente_nazione', 2)
            ->notEmptyString('cedente_nazione');

        $validator
            ->scalar('cedente_telefono')
            ->maxLength('cedente_telefono', 20)
            ->allowEmptyString('cedente_telefono');

        $validator
            ->scalar('cedente_email')
            ->maxLength('cedente_email', 255)
            ->allowEmptyString('cedente_email');

        $validator
            ->scalar('cedente_pec')
            ->maxLength('cedente_pec', 255)
            ->allowEmptyString('cedente_pec');

        $validator
            ->scalar('codice_fiscale_trasmittente')
            ->maxLength('codice_fiscale_trasmittente', 16)
            ->allowEmptyString('codice_fiscale_trasmittente');

        $validator
            ->scalar('id_paese_trasmittente')
            ->maxLength('id_paese_trasmittente', 2)
            ->notEmptyString('id_paese_trasmittente');

        $validator
            ->scalar('id_codice_trasmittente')
            ->maxLength('id_codice_trasmittente', 28)
            ->allowEmptyString('id_codice_trasmittente');

        $validator
            ->integer('progressivo_invio')
            ->notEmptyString('progressivo_invio');

        $validator
            ->scalar('formato_trasmissione')
            ->maxLength('formato_trasmissione', 5)
            ->notEmptyString('formato_trasmissione');

        $validator
            ->scalar('iban_predefinito')
            ->maxLength('iban_predefinito', 34)
            ->allowEmptyString('iban_predefinito');

        $validator
            ->scalar('banca_predefinita')
            ->maxLength('banca_predefinita', 100)
            ->allowEmptyString('banca_predefinita');

        $validator
            ->boolean('usa_firma_digitale')
            ->notEmptyString('usa_firma_digitale');

        $validator
            ->scalar('certificato_path')
            ->maxLength('certificato_path', 255)
            ->allowEmptyString('certificato_path');

        $validator
            ->scalar('certificato_password')
            ->maxLength('certificato_password', 255)
            ->allowEmptyString('certificato_password');

        $validator
            ->dateTime('ultima_sincronizzazione')
            ->allowEmptyDateTime('ultima_sincronizzazione');

        $validator
            ->boolean('is_active')
            ->notEmptyString('is_active');

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
        $rules->add($rules->isUnique(['tenant_id']), ['errorField' => 'tenant_id']);
        $rules->add($rules->existsIn(['tenant_id'], 'Tenants'), ['errorField' => 'tenant_id']);

        return $rules;
    }
}
