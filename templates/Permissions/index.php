<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Permission> $permissions
 */
?>
<div class="permissions index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Gestione Permessi') ?></h3>
        <div class="btn-group">
            <?= $this->Html->link(
                '<i data-lucide="grid-3x3" style="width:16px;height:16px;"></i> ' . __('Matrice'),
                ['controller' => 'Roles', 'action' => 'matrix'],
                ['class' => 'btn btn-outline-primary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Permesso'),
                ['action' => 'add'],
                ['class' => 'button', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('group_name', 'Gruppo') ?></th>
                    <th><?= $this->Paginator->sort('display_name', 'Nome') ?></th>
                    <th><?= $this->Paginator->sort('controller', 'Controller') ?></th>
                    <th><?= $this->Paginator->sort('action', 'Azione') ?></th>
                    <th>Ruoli</th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $currentGroup = null;
                foreach ($permissions as $permission):
                    // Group separator
                    if ($permission->group_name !== $currentGroup):
                        $currentGroup = $permission->group_name;
                ?>
                <tr class="table-secondary">
                    <td colspan="6">
                        <strong>
                            <i data-lucide="folder" style="width:16px;height:16px;"></i>
                            <?= h($currentGroup ?: 'Altro') ?>
                        </strong>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td></td>
                    <td><?= h($permission->display_name) ?></td>
                    <td><code><?= h($permission->controller) ?></code></td>
                    <td><code><?= h($permission->action) ?></code></td>
                    <td>
                        <?php if (!empty($permission->roles)): ?>
                            <?php foreach ($permission->roles as $role): ?>
                                <span class="badge bg-secondary me-1"><?= h($role->display_name) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $permission->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $permission->id]) ?>
                        <?= $this->Form->postLink(
                            __('Elimina'),
                            ['action' => 'delete', $permission->id],
                            ['confirm' => __('Sei sicuro di voler eliminare questo permesso?')]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('prima')) ?>
            <?= $this->Paginator->prev('< ' . __('precedente')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('successiva') . ' >') ?>
            <?= $this->Paginator->last(__('ultima') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Pagina {{page}} di {{pages}}, {{current}} record su {{count}} totali')) ?></p>
    </div>
</div>
