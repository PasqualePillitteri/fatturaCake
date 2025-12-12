<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\CategorieProdotti> $categorieProdotti
 */
$hiddenCount = 5;
?>
<div class="categorieProdotti index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Categorie Prodotti') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuova Categoria'),
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
        <div class="collapse <?= $this->request->getQuery('q') || $this->request->getQuery('parent_id') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Nome, descrizione...',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $this->Form->control('parent_id', [
                            'label' => 'Categoria Padre',
                            'options' => $parentCategorieProdotti ?? [],
                            'empty' => 'Tutte',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('is_active', [
                            'label' => 'Stato',
                            'options' => ['1' => 'Attive', '0' => 'Non attive'],
                            'empty' => 'Tutte',
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
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="categorie-table" title="Mostra tutte le colonne">
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
        <table data-table-id="categorie-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('parent_id', 'Categoria Padre') ?></th>
                    <th><?= $this->Paginator->sort('sort_order', 'Ordine') ?></th>
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
                <?php foreach ($categorieProdotti as $categoria): ?>
                <tr>
                    <td><strong><?= h($categoria->nome) ?></strong></td>
                    <td><?= $categoria->hasValue('parent_categorie_prodotti') ? $this->Html->link(h($categoria->parent_categorie_prodotti->nome), ['action' => 'view', $categoria->parent_categorie_prodotti->id]) : '<em class="text-muted">Root</em>' ?></td>
                    <td class="text-center"><?= $this->Number->format($categoria->sort_order) ?></td>
                    <td><?= $categoria->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($categoria->id) ?></td>
                    <td class="col-hidden"><?= $categoria->hasValue('tenant') ? h($categoria->tenant->nome) : '' ?></td>
                    <td class="col-hidden"><?= $categoria->created ? $categoria->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $categoria->modified ? $categoria->modified->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $categoria->deleted ? $categoria->deleted->format('d/m/Y H:i') : '-' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $categoria->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $categoria->id]) ?>
                        <?= $this->Form->postLink(__('Elimina'), ['action' => 'delete', $categoria->id], ['method' => 'delete', 'confirm' => __('Sei sicuro di voler eliminare {0}?', $categoria->nome)]) ?>
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
