<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Role> $roles
 */
?>
<div class="roles index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Gestione Ruoli') ?></h3>
        <div class="btn-group">
            <?= $this->Html->link(
                '<i data-lucide="grid-3x3" style="width:16px;height:16px;"></i> ' . __('Matrice Permessi'),
                ['action' => 'matrix'],
                ['class' => 'btn btn-outline-primary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Ruolo'),
                ['action' => 'add'],
                ['class' => 'button', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('display_name', 'Nome') ?></th>
                    <th><?= $this->Paginator->sort('name', 'Codice') ?></th>
                    <th><?= $this->Paginator->sort('priority', 'Priorità') ?></th>
                    <th>Utenti</th>
                    <th><?= $this->Paginator->sort('is_system', 'Sistema') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td>
                        <strong><?= h($role->display_name) ?></strong>
                        <?php if ($role->description): ?>
                            <br><small class="text-muted"><?= h($role->description) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><code><?= h($role->name) ?></code></td>
                    <td>
                        <?php
                        $priorityBadge = match(true) {
                            $role->priority >= 100 => 'bg-danger',
                            $role->priority >= 50 => 'bg-warning',
                            $role->priority >= 10 => 'bg-info',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $priorityBadge ?>"><?= $role->priority ?></span>
                    </td>
                    <td>
                        <?php $userCount = count($role->users ?? []); ?>
                        <span class="badge bg-primary"><?= $userCount ?></span>
                    </td>
                    <td>
                        <?= $role->is_system
                            ? '<span class="badge bg-danger">Sistema</span>'
                            : '<span class="badge bg-secondary">Custom</span>'
                        ?>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $role->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $role->id]) ?>
                        <?php if (!$role->is_system): ?>
                            <?= $this->Form->postLink(
                                __('Elimina'),
                                ['action' => 'delete', $role->id],
                                ['confirm' => __('Sei sicuro di voler eliminare il ruolo {0}?', $role->display_name)]
                            ) ?>
                        <?php endif; ?>
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
