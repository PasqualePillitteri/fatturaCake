<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;

/**
 * Permissions Model
 *
 * @property \App\Model\Table\RolesTable&\Cake\ORM\Association\BelongsToMany $Roles
 *
 * @method \App\Model\Entity\Permission newEmptyEntity()
 * @method \App\Model\Entity\Permission newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Permission> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Permission get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Permission findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Permission patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Permission> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Permission|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Permission saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PermissionsTable extends Table
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

        $this->setTable('permissions');
        $this->setDisplayField('display_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');

        $this->belongsToMany('Roles', [
            'foreignKey' => 'permission_id',
            'targetForeignKey' => 'role_id',
            'joinTable' => 'roles_permissions',
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
            ->scalar('controller')
            ->maxLength('controller', 100)
            ->requirePresence('controller', 'create')
            ->notEmptyString('controller');

        $validator
            ->scalar('action')
            ->maxLength('action', 100)
            ->requirePresence('action', 'create')
            ->notEmptyString('action');

        $validator
            ->scalar('prefix')
            ->maxLength('prefix', 50)
            ->allowEmptyString('prefix');

        $validator
            ->scalar('plugin')
            ->maxLength('plugin', 100)
            ->allowEmptyString('plugin');

        $validator
            ->scalar('display_name')
            ->maxLength('display_name', 150)
            ->allowEmptyString('display_name');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('group_name')
            ->maxLength('group_name', 100)
            ->allowEmptyString('group_name');

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
        // Unique constraint on controller + action + prefix
        $rules->add($rules->isUnique(
            ['controller', 'action', 'prefix'],
            'Questo permesso esiste già.'
        ));

        return $rules;
    }

    /**
     * Find permissions grouped by controller.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findGrouped(SelectQuery $query): SelectQuery
    {
        return $query->orderBy([
            'Permissions.group_name' => 'ASC',
            'Permissions.controller' => 'ASC',
            'Permissions.action' => 'ASC',
        ]);
    }

    /**
     * Get permissions grouped by group_name for UI.
     *
     * @return array<string, array>
     */
    public function getGroupedPermissions(): array
    {
        $permissions = $this->find('grouped')->toArray();
        $grouped = [];

        foreach ($permissions as $permission) {
            $group = $permission->group_name ?: $permission->controller;
            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    /**
     * Sync permissions from controllers.
     * Scans controllers and creates permission entries for each action.
     *
     * @return array{created: int, existing: int}
     */
    public function syncFromControllers(): array
    {
        $controllersPath = APP . 'Controller';
        $created = 0;
        $existing = 0;

        // Standard actions
        $standardActions = ['index', 'view', 'add', 'edit', 'delete'];

        // Action descriptions
        $actionDescriptions = [
            'index' => 'Visualizza elenco',
            'view' => 'Visualizza dettaglio',
            'add' => 'Crea nuovo',
            'edit' => 'Modifica',
            'delete' => 'Elimina',
        ];

        // Scan controller files
        $files = glob($controllersPath . '/*Controller.php');
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $controllerName = str_replace('Controller', '', $filename);

            // Skip base controllers
            if (in_array($controllerName, ['App', 'Error'])) {
                continue;
            }

            // Determine group
            $groupName = $this->determineGroupName($controllerName);

            foreach ($standardActions as $action) {
                $displayName = "{$controllerName}: {$actionDescriptions[$action]}";

                $permission = $this->findOrCreate(
                    [
                        'controller' => $controllerName,
                        'action' => $action,
                        'prefix IS' => null,
                    ],
                    function ($entity) use ($displayName, $groupName) {
                        $entity->display_name = $displayName;
                        $entity->group_name = $groupName;
                    }
                );

                if ($permission->isNew()) {
                    $created++;
                } else {
                    $existing++;
                }
            }
        }

        return compact('created', 'existing');
    }

    /**
     * Determine group name for a controller.
     *
     * @param string $controllerName Controller name.
     * @return string
     */
    protected function determineGroupName(string $controllerName): string
    {
        $groups = [
            'Fatturazione' => ['Fatture', 'FatturaRighe', 'FatturaAllegati'],
            'Anagrafiche' => ['Anagrafiche', 'Tenants'],
            'Prodotti' => ['Prodotti', 'CategorieProdotti', 'Listini', 'ListiniProdotti'],
            'Utenti' => ['Users', 'Roles', 'Permissions'],
            'Sistema' => ['Dashboard', 'Pages', 'LogAttivita'],
        ];

        foreach ($groups as $groupName => $controllers) {
            if (in_array($controllerName, $controllers)) {
                return $groupName;
            }
        }

        return 'Altro';
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
                'fields' => ['controller', 'action', 'display_name', 'description', 'group_name'],
            ])
            ->value('group_name')
            ->value('controller');

        return $searchManager;
    }
}
