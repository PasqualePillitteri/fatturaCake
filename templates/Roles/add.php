<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 * @var array $groupedPermissions
 */
?>
<div class="roles form content form-content">
    <div class="page-header">
        <h3><?= __('Nuovo Ruolo') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
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
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('display_name', [
                        'label' => ['text' => 'Nome Visualizzato', 'class' => 'required'],
                        'placeholder' => 'es. Responsabile Vendite',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('name', [
                        'label' => ['text' => 'Codice', 'class' => 'required'],
                        'placeholder' => 'es. sales_manager',
                        'help' => 'Identificativo univoco (solo lettere, numeri, underscore)',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('priority', [
                        'label' => 'Priorità',
                        'type' => 'number',
                        'default' => 10,
                        'help' => 'Maggiore = più privilegi (superadmin=100, admin=50, staff=20, user=10)',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('description', [
                        'label' => 'Descrizione',
                        'placeholder' => 'Descrizione opzionale del ruolo',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Permessi -->
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
                                    Seleziona tutti
                                </button>
                            </div>
                            <div class="card-body">
                                <?php foreach ($permissions as $permission): ?>
                                <div class="form-check">
                                    <?= $this->Form->checkbox("permissions._ids.{$permission->id}", [
                                        'value' => $permission->id,
                                        'id' => "perm-{$permission->id}",
                                        'class' => 'form-check-input perm-group-' . preg_replace('/[^a-z0-9]/', '', strtolower($groupName)),
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
                    <?= $this->Html->link('Sincronizza i permessi', ['action' => 'syncPermissions'], ['class' => 'alert-link']) ?>
                    dai controller.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Azioni -->
    <div class="form-card">
        <div class="form-card-body">
            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva'),
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
            this.textContent = allChecked ? 'Seleziona tutti' : 'Deseleziona tutti';
        });
    });
});
</script>
