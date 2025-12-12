<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Prodotti> $prodotti
 */
$hiddenCount = 18;
?>
<div class="prodotti index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Prodotti / Servizi') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Prodotto'),
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
        <div class="collapse <?= $this->request->getQuery('q') || $this->request->getQuery('tipo') || $this->request->getQuery('categoria_id') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Codice, nome, descrizione...',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('tipo', [
                            'label' => 'Tipo',
                            'options' => ['prodotto' => 'Prodotto', 'servizio' => 'Servizio'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('categoria_id', [
                            'label' => 'Categoria',
                            'options' => $categorias ?? [],
                            'empty' => 'Tutte',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('codice', [
                            'label' => 'Codice',
                            'placeholder' => 'Codice prodotto',
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
                    <div class="col-md-2">
                        <?= $this->Form->control('prezzo_min', [
                            'label' => 'Prezzo min',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'placeholder' => '0.00',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('prezzo_max', [
                            'label' => 'Prezzo max',
                            'type' => 'number',
                            'step' => '0.01',
                            'min' => '0',
                            'placeholder' => '999.99',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('gestione_magazzino', [
                            'label' => 'Magazzino',
                            'options' => ['1' => 'Gestito', '0' => 'Non gestito'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-2">
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
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="prodotti-table" title="Mostra tutte le colonne">
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
        <table data-table-id="prodotti-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('codice') ?></th>
                    <th><?= $this->Paginator->sort('nome') ?></th>
                    <th><?= $this->Paginator->sort('tipo') ?></th>
                    <th><?= $this->Paginator->sort('categoria_id', 'Categoria') ?></th>
                    <th><?= $this->Paginator->sort('prezzo_vendita', 'Prezzo') ?></th>
                    <th><?= $this->Paginator->sort('aliquota_iva', 'IVA %') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Attivo') ?></th>

                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('tenant_id', 'Tenant') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('codice_tipo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('codice_valore') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('descrizione') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('unita_misura') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('prezzo_acquisto') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('prezzo_ivato') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('natura') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('riferimento_normativo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('soggetto_ritenuta') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('gestione_magazzino') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('giacenza') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('scorta_minima') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('sort_order') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('created') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modified') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('deleted') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prodotti as $prodotto): ?>
                <tr>
                    <td><code><?= h($prodotto->codice) ?></code></td>
                    <td><strong><?= h($prodotto->nome) ?></strong></td>
                    <td>
                        <?php
                        $tipoBadge = match($prodotto->tipo) {
                            'prodotto' => 'bg-primary',
                            'servizio' => 'bg-info',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($prodotto->tipo)) ?></span>
                    </td>
                    <td><?= $prodotto->hasValue('categoria') ? $this->Html->link(h($prodotto->categoria->nome), ['controller' => 'CategorieProdotti', 'action' => 'view', $prodotto->categoria->id]) : '-' ?></td>
                    <td class="text-end"><strong><?= $prodotto->prezzo_vendita !== null ? $this->Number->currency($prodotto->prezzo_vendita, 'EUR') : '-' ?></strong></td>
                    <td class="text-center"><?= $prodotto->aliquota_iva !== null ? $this->Number->toPercentage($prodotto->aliquota_iva, 0) : '-' ?></td>
                    <td><?= $prodotto->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($prodotto->id) ?></td>
                    <td class="col-hidden"><?= $prodotto->hasValue('tenant') ? h($prodotto->tenant->nome) : '' ?></td>
                    <td class="col-hidden"><?= h($prodotto->codice_tipo) ?></td>
                    <td class="col-hidden"><?= h($prodotto->codice_valore) ?></td>
                    <td class="col-hidden"><?= h($prodotto->descrizione) ?></td>
                    <td class="col-hidden"><?= h($prodotto->unita_misura) ?></td>
                    <td class="col-hidden text-end"><?= $prodotto->prezzo_acquisto !== null ? $this->Number->currency($prodotto->prezzo_acquisto, 'EUR') : '-' ?></td>
                    <td class="col-hidden"><?= $prodotto->prezzo_ivato ? '<span class="badge bg-info">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="col-hidden"><?= h($prodotto->natura) ?></td>
                    <td class="col-hidden"><?= h($prodotto->riferimento_normativo) ?></td>
                    <td class="col-hidden"><?= $prodotto->soggetto_ritenuta ? '<span class="badge bg-warning">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="col-hidden"><?= $prodotto->gestione_magazzino ? '<span class="badge bg-info">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="col-hidden text-end"><?= $this->Number->format($prodotto->giacenza) ?></td>
                    <td class="col-hidden text-end"><?= $prodotto->scorta_minima !== null ? $this->Number->format($prodotto->scorta_minima) : '-' ?></td>
                    <td class="col-hidden"><?= $this->Number->format($prodotto->sort_order) ?></td>
                    <td class="col-hidden"><?= $prodotto->created ? $prodotto->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $prodotto->modified ? $prodotto->modified->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $prodotto->deleted ? $prodotto->deleted->format('d/m/Y H:i') : '-' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $prodotto->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $prodotto->id]) ?>
                        <?= $this->Form->postLink(__('Elimina'), ['action' => 'delete', $prodotto->id], ['method' => 'delete', 'confirm' => __('Sei sicuro di voler eliminare {0}?', $prodotto->nome)]) ?>
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
