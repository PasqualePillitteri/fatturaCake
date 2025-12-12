<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Listini> $listini
 */
$hiddenCount = 5;
?>
<div class="listini index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Listini') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Listino'),
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
        <div class="collapse <?= $this->request->getQuery('q') || $this->request->getQuery('valuta') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Nome, descrizione...',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('valuta', [
                            'label' => 'Valuta',
                            'options' => ['EUR' => 'EUR', 'USD' => 'USD', 'GBP' => 'GBP'],
                            'empty' => 'Tutte',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('is_default', [
                            'label' => 'Default',
                            'options' => ['1' => 'Si', '0' => 'No'],
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
                    <div class="col-md-3 d-flex align-items-end gap-2">
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
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="listini-table" title="Mostra tutte le colonne">
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
        <table data-table-id="listini-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('valuta') ?></th>
                    <th><?= $this->Paginator->sort('data_inizio', 'Inizio') ?></th>
                    <th><?= $this->Paginator->sort('data_fine', 'Fine') ?></th>
                    <th><?= $this->Paginator->sort('is_default', 'Default') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Attivo') ?></th>

                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('tenant_id', 'Tenant') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('created') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modified') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listini as $listino): ?>
                <tr>
                    <td><strong><?= h($listino->nome) ?></strong></td>
                    <td><span class="badge bg-secondary"><?= h($listino->valuta) ?></span></td>
                    <td><?= $listino->data_inizio ? $listino->data_inizio->format('d/m/Y') : '-' ?></td>
                    <td><?= $listino->data_fine ? $listino->data_fine->format('d/m/Y') : '-' ?></td>
                    <td><?= $listino->is_default ? '<span class="badge bg-primary">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td><?= $listino->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($listino->id) ?></td>
                    <td class="col-hidden"><?= $listino->hasValue('tenant') ? h($listino->tenant->nome) : '' ?></td>
                    <td class="col-hidden"><?= $listino->created ? $listino->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $listino->modified ? $listino->modified->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $listino->deleted ? $listino->deleted->format('d/m/Y H:i') : '-' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $listino->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $listino->id]) ?>
                        <?= $this->Form->postLink(__('Elimina'), ['action' => 'delete', $listino->id], ['method' => 'delete', 'confirm' => __('Sei sicuro di voler eliminare {0}?', $listino->nome)]) ?>
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
