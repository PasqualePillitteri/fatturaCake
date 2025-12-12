<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 * @var array $groupedPermissions
 */

// Get current permission IDs
$currentPermissionIds = [];
if (!empty($role->permissions)) {
    foreach ($role->permissions as $permission) {
        $currentPermissionIds[$permission->id] = true;
    }
}
?>
<div class="roles form content form-content">
    <div class="page-header">
        <h3><?= __('Modifica Ruolo') ?>: <?= h($role->display_name) ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="eye" style="width:16px;height:16px;"></i> ' . __('Visualizza'),
                ['action' => 'view', $role->id],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($role) ?>

    <!-- Dati Ruolo -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="shield" style="width:18px;height:18px;"></i>
            Dati Ruolo
            <?php if ($role->is_system): ?>
                <span class="badge bg-danger ms-2">Ruolo di Sistema</span>
            <?php endif; ?>
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('display_name', [
                        'label' => ['text' => 'Nome Visualizzato', 'class' => 'required'],
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('name', [
                        'label' => ['text' => 'Codice', 'class' => 'required'],
                        'readonly' => $role->is_system,
                        'help' => $role->is_system ? 'Il codice dei ruoli di sistema non può essere modificato' : '',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('priority', [
                        'label' => 'Priorità',
                        'type' => 'number',
                        'help' => 'Maggiore = più privilegi',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('description', [
                        'label' => 'Descrizione',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Permessi -->
    <?php if ($role->name !== 'superadmin'): ?>
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="key" style="width:18px;height:18px;"></i>
            Permessi
        </div>
        <div class="form-card-body">
            <?php if (!empty($groupedPermissions)): ?>
                <div class="row">
                    <?php foreach ($groupedPermissions as $groupName => $permissions): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header py-2">
                                <strong><?= h($groupName) ?></strong>
                                <button type="button" class="btn btn-sm btn-link float-end toggle-group" data-group="<?= h($groupName) ?>">
                                    Toggle
                                </button>
                            </div>
                            <div class="card-body">
                                <?php foreach ($permissions as $permission): ?>
                                <div class="form-check">
                                    <?= $this->Form->checkbox("permissions._ids.{$permission->id}", [
                                        'value' => $permission->id,
                                        'id' => "perm-{$permission->id}",
                                        'class' => 'form-check-input perm-group-' . preg_replace('/[^a-z0-9]/', '', strtolower($groupName)),
                                        'checked' => isset($currentPermissionIds[$permission->id]),
                                        'hiddenField' => false,
                                    ]) ?>
                                    <label class="form-check-label" for="perm-<?= $permission->id ?>">
                                        <?= h($permission->display_name ?: "{$permission->controller}:{$permission->action}") ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i data-lucide="info" style="width:16px;height:16px;"></i>
                    Nessun permesso disponibile.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="form-card">
        <div class="form-card-body">
            <div class="alert alert-warning mb-0">
                <i data-lucide="infinity" style="width:16px;height:16px;"></i>
                Il ruolo <strong>superadmin</strong> ha accesso completo a tutte le funzionalità. I permessi non sono configurabili.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Azioni -->
    <div class="form-card">
        <div class="form-card-body">
            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?php if (!$role->is_system): ?>
                        <?= $this->Form->postLink(
                            '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                            ['action' => 'delete', $role->id],
                            ['confirm' => __('Sei sicuro di voler eliminare {0}?', $role->display_name), 'class' => 'btn btn-danger', 'escapeTitle' => false]
                        ) ?>
                    <?php endif; ?>
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva modifiche'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-group').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const group = this.dataset.group.toLowerCase().replace(/[^a-z0-9]/g, '');
            const checkboxes = document.querySelectorAll('.perm-group-' + group);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        });
    });
});
</script>
