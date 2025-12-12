<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Anagrafiche> $anagrafiche
 */
$hiddenCount = 18;
?>
<div class="anagrafiche index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Clienti') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Cliente'),
            ['action' => 'addCliente'],
            ['class' => 'button', 'escapeTitle' => false]
        ) ?>
    </div>

    <!-- Filtri -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#filtriCollapse">
            <span><i data-lucide="filter" style="width:16px;height:16px;"></i> Filtri</span>
            <i data-lucide="chevron-down" style="width:16px;height:16px;"></i>
        </div>
        <div class="collapse <?= $this->request->getQuery('q') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Denominazione, nome, email...',
                            'class' => 'form-control form-control-sm',
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
                        <?= $this->Form->control('codice_fiscale', [
                            'label' => 'C.F.',
                            'placeholder' => 'Codice Fiscale',
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
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-3">
                        <?= $this->Form->control('comune', [
                            'label' => 'Comune',
                            'placeholder' => 'Nome comune',
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
                    <div class="col-md-7 d-flex align-items-end gap-2">
                        <?= $this->Form->button('<i data-lucide="search" style="width:14px;height:14px;"></i> Filtra', [
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-sm',
                            'escapeTitle' => false,
                        ]) ?>
                        <?= $this->Html->link('<i data-lucide="x" style="width:14px;height:14px;"></i> Reset', ['action' => 'indexClienti'], [
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
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="clienti-table" title="Mostra tutte le colonne">
                <i data-lucide="columns-3" style="width:14px;height:14px;"></i>
                <span class="btn-label">Mostra tutte</span>
            </button>
            <span class="hidden-columns-indicator">+<?= $hiddenCount ?> colonne nascoste</span>
        </div>
        <div class="toolbar-right">
            <div class="table-meta">
                <span class="meta-item">
                    <i data-lucide="users" style="width:14px;height:14px;"></i>
                    <?= $this->Paginator->param('count') ?> clienti
                </span>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table data-table-id="clienti-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('denominazione') ?></th>
                    <th><?= $this->Paginator->sort('partita_iva', 'P.IVA') ?></th>
                    <th><?= $this->Paginator->sort('codice_fiscale', 'C.F.') ?></th>
                    <th><?= $this->Paginator->sort('comune') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Attivo') ?></th>

                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('tipo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('nome') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cognome') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('regime_fiscale') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('indirizzo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('numero_civico') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cap') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('provincia') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('nazione') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('telefono') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('pec') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('codice_sdi') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('riferimento_amministrazione') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('split_payment') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('created') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($anagrafiche as $anagrafica): ?>
                <tr>
                    <td><strong><?= h($anagrafica->denominazione ?: $anagrafica->nome . ' ' . $anagrafica->cognome) ?></strong></td>
                    <td><?= h($anagrafica->partita_iva) ?: '-' ?></td>
                    <td><?= h($anagrafica->codice_fiscale) ?: '-' ?></td>
                    <td><?= h($anagrafica->comune) ?> <?= $anagrafica->provincia ? '(' . h($anagrafica->provincia) . ')' : '' ?></td>
                    <td><?= h($anagrafica->email) ?: '-' ?></td>
                    <td><?= $anagrafica->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($anagrafica->id) ?></td>
                    <td class="col-hidden">
                        <?php
                        $tipoBadge = match($anagrafica->tipo) {
                            'cliente' => 'bg-primary',
                            'entrambi' => 'bg-success',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($anagrafica->tipo)) ?></span>
                    </td>
                    <td class="col-hidden"><?= h($anagrafica->nome) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->cognome) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->regime_fiscale) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->indirizzo) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->numero_civico) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->cap) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->provincia) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->nazione) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->telefono) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->pec) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->codice_sdi) ?></td>
                    <td class="col-hidden"><?= h($anagrafica->riferimento_amministrazione) ?></td>
                    <td class="col-hidden"><?= $anagrafica->split_payment ? '<span class="badge bg-warning">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="col-hidden"><?= $anagrafica->created ? $anagrafica->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $anagrafica->modified ? $anagrafica->modified->format('d/m/Y H:i') : '-' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $anagrafica->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $anagrafica->id]) ?>
                        <?= $this->Form->postLink(__('Elimina'), ['action' => 'delete', $anagrafica->id], ['method' => 'delete', 'confirm' => __('Sei sicuro di voler eliminare {0}?', $anagrafica->denominazione ?: $anagrafica->nome)]) ?>
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
