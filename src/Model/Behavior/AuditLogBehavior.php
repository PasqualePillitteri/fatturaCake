<?php
declare(strict_types=1);

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * AuditLog Behavior
 *
 * Automatically logs all CRUD operations to the log_attivita table.
 */
class AuditLogBehavior extends Behavior
{
    use LocatorAwareTrait;

    /**
     * Static storage for user context.
     *
     * @var array<string, mixed>
     */
    protected static array $_userContext = [
        'user_id' => null,
        'tenant_id' => null,
        'ip_address' => null,
        'user_agent' => null,
    ];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'excludeFields' => ['created', 'modified', 'password'], // Fields to exclude from logging
        'logCreate' => true,
        'logUpdate' => true,
        'logDelete' => true,
    ];

    /**
     * Store original data before save for comparison.
     *
     * @var array<string, mixed>
     */
    protected array $_originalData = [];

    /**
     * Set the user context (called from AppController).
     *
     * @param array<string, mixed> $context User context data
     * @return void
     */
    public static function setUserContext(array $context): void
    {
        self::$_userContext = array_merge(self::$_userContext, $context);
    }

    /**
     * Get the current user context.
     *
     * @return array<string, mixed>
     */
    public static function getUserContext(): array
    {
        return self::$_userContext;
    }

    /**
     * Before save callback - store original data for updates.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options Save options
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (!$entity->isNew() && $this->getConfig('logUpdate')) {
            // Store original values before modification
            $this->_originalData[$entity->get('id')] = $entity->getOriginalValues();
        }
    }

    /**
     * After save callback - log create and update operations.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options Save options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        $isNew = $entity->isNew();

        // Check if entity was just created (isNew is true during afterSave for new records)
        // We need to check if it has an id and if originalValues is empty
        $wasNew = empty($entity->getOriginalValues()) ||
                  !isset($this->_originalData[$entity->get('id')]);

        if ($wasNew && $this->getConfig('logCreate')) {
            $this->_logActivity('create', $entity, null, $this->_getLoggableData($entity));
        } elseif (!$wasNew && $this->getConfig('logUpdate')) {
            $originalData = $this->_originalData[$entity->get('id')] ?? [];
            $newData = $this->_getChangedData($entity, $originalData);

            // Only log if there are actual changes
            if (!empty($newData)) {
                $this->_logActivity('update', $entity, $this->_filterData($originalData), $newData);
            }

            // Clean up stored data
            unset($this->_originalData[$entity->get('id')]);
        }
    }

    /**
     * After delete callback - log delete operations.
     *
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \ArrayObject $options Delete options
     * @return void
     */
    public function afterDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if ($this->getConfig('logDelete')) {
            $this->_logActivity('delete', $entity, $this->_getLoggableData($entity), null);
        }
    }

    /**
     * Log an activity to the log_attivita table.
     *
     * @param string $action The action (create, update, delete)
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param array|null $previousData Previous data
     * @param array|null $newData New data
     * @return void
     */
    protected function _logActivity(
        string $action,
        EntityInterface $entity,
        ?array $previousData,
        ?array $newData
    ): void {
        try {
            $logTable = $this->fetchTable('LogAttivita');

            $context = self::getUserContext();

            $log = $logTable->newEntity([
                'azione' => $action,
                'modello' => $this->table()->getAlias(),
                'modello_id' => $entity->get('id'),
                'dati_precedenti' => $previousData ? json_encode($previousData, JSON_UNESCAPED_UNICODE) : null,
                'dati_nuovi' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => $context['ip_address'],
                'user_agent' => $context['user_agent'],
            ], ['validate' => false]);

            // Set protected fields directly (they are not mass-assignable for security)
            $log->set('tenant_id', $context['tenant_id']);
            $log->set('user_id', $context['user_id']);

            // Save without triggering audit log on itself (prevent infinite loop)
            $logTable->saveOrFail($log, ['atomic' => false]);
        } catch (\Exception $e) {
            // Log error but don't break the main operation
            \Cake\Log\Log::error('AuditLog error: ' . $e->getMessage());
        }
    }

    /**
     * Get loggable data from entity (excluding sensitive fields).
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @return array<string, mixed>
     */
    protected function _getLoggableData(EntityInterface $entity): array
    {
        $data = $entity->toArray();
        return $this->_filterData($data);
    }

    /**
     * Filter out excluded fields from data.
     *
     * @param array<string, mixed> $data The data to filter
     * @return array<string, mixed>
     */
    protected function _filterData(array $data): array
    {
        $excludeFields = $this->getConfig('excludeFields');

        foreach ($excludeFields as $field) {
            unset($data[$field]);
        }

        // Remove associated data (nested arrays/objects) for cleaner logs
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Get only the changed data between original and new.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param array<string, mixed> $originalData Original data
     * @return array<string, mixed>
     */
    protected function _getChangedData(EntityInterface $entity, array $originalData): array
    {
        $newData = $entity->toArray();
        $excludeFields = $this->getConfig('excludeFields');
        $changedData = [];

        foreach ($newData as $key => $value) {
            // Skip excluded fields
            if (in_array($key, $excludeFields)) {
                continue;
            }

            // Skip nested data
            if (is_array($value) || is_object($value)) {
                continue;
            }

            // Check if value changed
            $originalValue = $originalData[$key] ?? null;
            if ($value !== $originalValue) {
                $changedData[$key] = $value;
            }
        }

        return $changedData;
    }
}
