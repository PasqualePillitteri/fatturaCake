<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User> $users
 */
$hiddenCount = 8;
?>
<div class="users index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Utenti') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Utente'),
            ['action' => 'add'],
            ['class' => 'button', 'escapeTitle' => false]
        ) ?>
    </div>

    <!-- Filtri -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#filtriCollapse">
            <span><i data-lucide="filter" style="width:16px;height:16px;"></i> Filtri</span>
            <i data-lucide="chevron-down" style="width:16px;height:16px;"></i>
        </div>
        <div class="collapse <?= $this->request->getQuery('q') || $this->request->getQuery('role') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Username, email, nome...',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('role', [
                            'label' => 'Ruolo',
                            'options' => ['superadmin' => 'Super Admin', 'admin' => 'Admin', 'staff' => 'Staff', 'user' => 'User'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('is_active', [
                            'label' => 'Stato',
                            'options' => ['1' => 'Attivi', '0' => 'Non attivi'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <?= $this->Form->button('<i data-lucide="search" style="width:14px;height:14px;"></i> Filtra', [
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-sm',
                            'escapeTitle' => false,
                        ]) ?>
                        <?= $this->Html->link('<i data-lucide="x" style="width:14px;height:14px;"></i> Reset', ['action' => 'index'], [
                            'class' => 'btn btn-outline-secondary btn-sm',
                            'escapeTitle' => false,
                        ]) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>

    <div class="table-toolbar">
        <div class="toolbar-left">
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="users-table" title="Mostra tutte le colonne">
                <i data-lucide="columns-3" style="width:14px;height:14px;"></i>
                <span class="btn-label">Mostra tutte</span>
            </button>
            <span class="hidden-columns-indicator">+<?= $hiddenCount ?> colonne nascoste</span>
        </div>
        <div class="toolbar-right">
            <div class="table-meta">
                <span class="meta-item"><?= $this->Paginator->param('count') ?> record</span>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table data-table-id="users-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('username') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('cognome') ?></th>
                    <th><?= $this->Paginator->sort('role', 'Ruolo') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Attivo') ?></th>
                    <th><?= $this->Paginator->sort('last_login', 'Ultimo Login') ?></th>

                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('tenant_id', 'Tenant') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('telefono') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('avatar') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('email_verified') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('created') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modified') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><strong><?= h($user->username) ?></strong></td>
                    <td><?= h($user->email) ?></td>
                    <td><?= h($user->nome) ?></td>
                    <td><?= h($user->cognome) ?></td>
                    <td>
                        <?php
                        $roleBadge = match($user->role) {
                            'admin', 'superadmin' => 'bg-danger',
                            'manager' => 'bg-warning',
                            'user' => 'bg-info',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $roleBadge ?>"><?= h(ucfirst($user->role)) ?></span>
                    </td>
                    <td><?= $user->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td><?= $user->last_login ? $user->last_login->format('d/m/Y H:i') : '<em class="text-muted">Mai</em>' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($user->id) ?></td>
                    <td class="col-hidden"><?= $user->hasValue('tenant') ? h($user->tenant->nome) : '' ?></td>
                    <td class="col-hidden"><?= h($user->telefono) ?></td>
                    <td class="col-hidden"><?= h($user->avatar) ?></td>
                    <td class="col-hidden"><?= $user->email_verified ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-warning">No</span>' ?></td>
                    <td class="col-hidden"><?= $user->created ? $user->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $user->modified ? $user->modified->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $user->deleted ? $user->deleted->format('d/m/Y H:i') : '-' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $user->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $user->id]) ?>
                        <?= $this->Form->postLink(__('Elimina'), ['action' => 'delete', $user->id], ['method' => 'delete', 'confirm' => __('Sei sicuro di voler eliminare {0}?', $user->username)]) ?>
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
