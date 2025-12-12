<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Roles Model
 *
 * @property \App\Model\Table\PermissionsTable&\Cake\ORM\Association\BelongsToMany $Permissions
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\HasMany $Users
 *
 * @method \App\Model\Entity\Role newEmptyEntity()
 * @method \App\Model\Entity\Role newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Role> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Role get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Role findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Role patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Role> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Role|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Role saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesTable extends Table
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

        $this->setTable('roles');
        $this->setDisplayField('display_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');

        $this->belongsToMany('Permissions', [
            'foreignKey' => 'role_id',
            'targetForeignKey' => 'permission_id',
            'joinTable' => 'roles_permissions',
        ]);

        $this->hasMany('Users', [
            'foreignKey' => 'role_id',
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
            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Questo nome ruolo esiste già.',
            ]);

        $validator
            ->scalar('display_name')
            ->maxLength('display_name', 100)
            ->requirePresence('display_name', 'create')
            ->notEmptyString('display_name');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->boolean('is_system')
            ->notEmptyString('is_system');

        $validator
            ->integer('priority')
            ->notEmptyString('priority');

        return $validator;
    }

    /**
     * Returns a rules checker object.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name']), ['errorField' => 'name']);

        // Prevent deletion of system roles
        $rules->addDelete(function ($entity) {
            return !$entity->is_system;
        }, 'isNotSystem', [
            'errorField' => 'is_system',
            'message' => 'I ruoli di sistema non possono essere eliminati.',
        ]);

        return $rules;
    }

    /**
     * Find roles ordered by priority.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findOrdered(SelectQuery $query): SelectQuery
    {
        return $query->orderBy(['Roles.priority' => 'DESC', 'Roles.display_name' => 'ASC']);
    }

    /**
     * Find role by name.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param string $name Role name.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findByName(SelectQuery $query, string $name): SelectQuery
    {
        return $query->where(['Roles.name' => $name]);
    }

    /**
     * Get roles as list for dropdown.
     *
     * @return array<int, string>
     */
    public function getList(): array
    {
        return $this->find('list', keyField: 'id', valueField: 'display_name')
            ->find('ordered')
            ->toArray();
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
                'fields' => ['name', 'display_name', 'description'],
            ])
            ->boolean('is_system');

        return $searchManager;
    }
}
