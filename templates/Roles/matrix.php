<?php
/**
 * @var \App\View\AppView $this
 * @var array $roles
 * @var array $permissions
 * @var array $matrix
 * @var array $groupedPermissions
 */
?>
<div class="roles matrix content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i data-lucide="grid-3x3" style="width:24px;height:24px;"></i>
            <?= __('Matrice Permessi') ?>
        </h3>
        <div class="btn-group">
            <?= $this->Form->postLink(
                '<i data-lucide="refresh-cw" style="width:16px;height:16px;"></i> ' . __('Sincronizza Controller'),
                ['action' => 'syncPermissions'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false, 'confirm' => 'Questo creerà i permessi per tutti i controller. Continuare?']
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Gestione Ruoli'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create(null, ['url' => ['action' => 'saveMatrix']]) ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 matrix-table">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="min-width: 250px;">Permesso</th>
                            <?php foreach ($roles as $role): ?>
                            <th class="text-center" style="min-width: 100px;">
                                <?= h($role->display_name) ?>
                                <?php if ($role->name === 'superadmin'): ?>
                                    <br><small class="text-muted">(tutti)</small>
                                <?php endif; ?>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupedPermissions as $groupName => $groupPerms): ?>
                        <!-- Group Header -->
                        <tr class="table-secondary">
                            <td colspan="<?= count($roles) + 1 ?>">
                                <strong>
                                    <i data-lucide="folder" style="width:16px;height:16px;"></i>
                                    <?= h($groupName) ?>
                                </strong>
                                <button type="button" class="btn btn-sm btn-link float-end select-all-group" data-group="<?= h($groupName) ?>">
                                    Seleziona tutti
                                </button>
                            </td>
                        </tr>
                        <?php foreach ($groupPerms as $permission): ?>
                        <tr>
                            <td>
                                <?= h($permission->display_name ?: "{$permission->controller}:{$permission->action}") ?>
                                <br>
                                <small class="text-muted">
                                    <code><?= h($permission->controller) ?>::<?= h($permission->action) ?></code>
                                </small>
                            </td>
                            <?php foreach ($roles as $role): ?>
                            <td class="text-center">
                                <?php if ($role->name === 'superadmin'): ?>
                                    <i data-lucide="check-circle" style="width:20px;height:20px;" class="text-success"></i>
                                <?php else: ?>
                                    <?php
                                    $isChecked = isset($matrix[$permission->id]['roles'][$role->id]);
                                    ?>
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox"
                                               name="permissions[<?= $role->id ?>][<?= $permission->id ?>]"
                                               value="1"
                                               class="form-check-input perm-check group-<?= preg_replace('/[^a-z0-9]/', '', strtolower($groupName)) ?>"
                                               <?= $isChecked ? 'checked' : '' ?>
                                               style="width: 20px; height: 20px;">
                                    </div>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="text-muted">
                        <?= count($permissions) ?> permessi | <?= count($roles) ?> ruoli
                    </span>
                </div>
                <div>
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva Modifiche'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>

<style>
.matrix-table th, .matrix-table td {
    vertical-align: middle;
}
.matrix-table .form-check {
    margin: 0;
    padding: 0;
}
.matrix-table thead {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle all in group
    document.querySelectorAll('.select-all-group').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const group = this.dataset.group.toLowerCase().replace(/[^a-z0-9]/g, '');
            const checkboxes = document.querySelectorAll('.group-' + group);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? 'Seleziona tutti' : 'Deseleziona tutti';
        });
    });
});
</script>
