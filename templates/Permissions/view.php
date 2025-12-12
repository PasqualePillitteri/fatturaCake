<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permission $permission
 */
?>
<div class="permissions view content">
    <div class="page-header">
        <h3><?= h($permission->display_name ?: "{$permission->controller}:{$permission->action}") ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="edit" style="width:16px;height:16px;"></i> ' . __('Modifica'),
                ['action' => 'edit', $permission->id],
                ['class' => 'btn btn-primary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="key" style="width:18px;height:18px;"></i>
            Dettagli Permesso
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nome visualizzato:</th>
                            <td><?= h($permission->display_name) ?: '<em class="text-muted">Non impostato</em>' ?></td>
                        </tr>
                        <tr>
                            <th>Controller:</th>
                            <td><code><?= h($permission->controller) ?></code></td>
                        </tr>
                        <tr>
                            <th>Azione:</th>
                            <td><code><?= h($permission->action) ?></code></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Gruppo:</th>
                            <td><?= h($permission->group_name) ?: '<em class="text-muted">Non impostato</em>' ?></td>
                        </tr>
                        <tr>
                            <th>Prefix:</th>
                            <td><?= h($permission->prefix) ?: '<em class="text-muted">Nessuno</em>' ?></td>
                        </tr>
                        <tr>
                            <th>Plugin:</th>
                            <td><?= h($permission->plugin) ?: '<em class="text-muted">Nessuno</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php if ($permission->description): ?>
            <div class="row">
                <div class="col-12">
                    <hr>
                    <strong>Descrizione:</strong>
                    <p class="mb-0"><?= h($permission->description) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ruoli con questo permesso -->
    <div class="card">
        <div class="card-header">
            <i data-lucide="users" style="width:18px;height:18px;"></i>
            Ruoli con questo permesso (<?= count($permission->roles ?? []) ?>)
        </div>
        <div class="card-body">
            <?php if (!empty($permission->roles)): ?>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($permission->roles as $role): ?>
                        <?= $this->Html->link(
                            '<i data-lucide="shield" style="width:14px;height:14px;"></i> ' . h($role->display_name),
                            ['controller' => 'Roles', 'action' => 'view', $role->id],
                            ['class' => 'btn btn-outline-secondary btn-sm', 'escapeTitle' => false]
                        ) ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">Nessun ruolo ha questo permesso assegnato.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
