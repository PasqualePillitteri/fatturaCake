<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\LogAttivitum> $logAttivita
 */
$hiddenCount = 4;
?>
<div class="logAttivita index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Log Attivita') ?></h3>
    </div>

    <div class="table-toolbar">
        <div class="toolbar-left">
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="log-table" title="Mostra tutte le colonne">
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
        <table data-table-id="log-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('created', 'Data') ?></th>
                    <th><?= $this->Paginator->sort('user_id', 'Utente') ?></th>
                    <th><?= $this->Paginator->sort('azione') ?></th>
                    <th><?= $this->Paginator->sort('modello') ?></th>
                    <th><?= $this->Paginator->sort('modello_id', 'ID Record') ?></th>

                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('tenant_id', 'Tenant') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('ip_address', 'IP') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('user_agent') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logAttivita as $log): ?>
                <tr>
                    <td><?= $log->created ? $log->created->format('d/m/Y H:i:s') : '-' ?></td>
                    <td><?= $log->hasValue('user') ? $this->Html->link(h($log->user->username), ['controller' => 'Users', 'action' => 'view', $log->user->id]) : '<em class="text-muted">Sistema</em>' ?></td>
                    <td>
                        <?php
                        $azioneBadge = match($log->azione) {
                            'create', 'add' => 'bg-success',
                            'update', 'edit' => 'bg-warning',
                            'delete' => 'bg-danger',
                            'view', 'read' => 'bg-info',
                            'login' => 'bg-primary',
                            'logout' => 'bg-secondary',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $azioneBadge ?>"><?= h(ucfirst($log->azione)) ?></span>
                    </td>
                    <td><code><?= h($log->modello) ?></code></td>
                    <td><?= $log->modello_id !== null ? $this->Number->format($log->modello_id) : '-' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($log->id) ?></td>
                    <td class="col-hidden"><?= $log->hasValue('tenant') ? h($log->tenant->nome) : '' ?></td>
                    <td class="col-hidden"><code><?= h($log->ip_address) ?></code></td>
                    <td class="col-hidden"><small><?= h($log->user_agent) ?></small></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $log->id]) ?>
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
