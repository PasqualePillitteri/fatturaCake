<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Tenant> $tenants
 */
$hiddenCount = 11;
?>
<div class="tenants index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Tenant') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Tenant'),
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
        <div class="collapse <?= $this->request->getQuery('q') || $this->request->getQuery('tipo') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Nome, descrizione, email...',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('tipo', [
                            'label' => 'Tipo',
                            'options' => ['azienda' => 'Azienda', 'professionista' => 'Professionista'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('partita_iva', [
                            'label' => 'P.IVA',
                            'placeholder' => 'Partita IVA',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('provincia', [
                            'label' => 'Provincia',
                            'placeholder' => 'es. MI',
                            'maxlength' => 2,
                            'class' => 'form-control form-control-sm',
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
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-12 d-flex gap-2">
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
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="tenants-table" title="Mostra tutte le colonne">
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
        <table data-table-id="tenants-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('tipo') ?></th>
                    <th><?= $this->Paginator->sort('partita_iva', 'P.IVA') ?></th>
                    <th><?= $this->Paginator->sort('citta') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Attivo') ?></th>

                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('codice_fiscale', 'C.F.') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('indirizzo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('provincia') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cap') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('telefono') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('pec') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('sito_web') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('slug') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('created') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modified') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $tenant): ?>
                <tr>
                    <td><strong><?= h($tenant->nome) ?></strong></td>
                    <td>
                        <?php
                        $tipoBadge = match($tenant->tipo) {
                            'azienda' => 'bg-primary',
                            'professionista' => 'bg-info',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($tenant->tipo)) ?></span>
                    </td>
                    <td><?= h($tenant->partita_iva) ?></td>
                    <td><?= h($tenant->citta) ?> <?= $tenant->provincia ? '(' . h($tenant->provincia) . ')' : '' ?></td>
                    <td><?= h($tenant->email) ?></td>
                    <td><?= $tenant->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($tenant->id) ?></td>
                    <td class="col-hidden"><?= h($tenant->codice_fiscale) ?></td>
                    <td class="col-hidden"><?= h($tenant->indirizzo) ?></td>
                    <td class="col-hidden"><?= h($tenant->provincia) ?></td>
                    <td class="col-hidden"><?= h($tenant->cap) ?></td>
                    <td class="col-hidden"><?= h($tenant->telefono) ?></td>
                    <td class="col-hidden"><?= h($tenant->pec) ?></td>
                    <td class="col-hidden"><?= h($tenant->sito_web) ?></td>
                    <td class="col-hidden"><?= h($tenant->slug) ?></td>
                    <td class="col-hidden"><?= $tenant->created ? $tenant->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $tenant->modified ? $tenant->modified->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $tenant->deleted ? $tenant->deleted->format('d/m/Y H:i') : '-' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $tenant->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $tenant->id]) ?>
                        <?= $this->Form->postLink(__('Elimina'), ['action' => 'delete', $tenant->id], ['method' => 'delete', 'confirm' => __('Sei sicuro di voler eliminare {0}?', $tenant->nome)]) ?>
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
