<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Fatture> $fatture
 */
?>
<div class="fatture index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Fatture Passive (Ricevute)') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuova Fattura Passiva'),
            ['action' => 'addPassiva'],
            ['class' => 'button', 'escapeTitle' => false]
        ) ?>
    </div>

    <!-- Filtri -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#filtriCollapse">
            <span><i data-lucide="filter" style="width:16px;height:16px;"></i> Filtri</span>
            <i data-lucide="chevron-down" style="width:16px;height:16px;"></i>
        </div>
        <div class="collapse <?= $this->request->getQuery('q') || $this->request->getQuery('tipo_documento') || $this->request->getQuery('stato_sdi') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Numero, fornitore, causale...',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('tipo_documento', [
                            'label' => 'Tipo',
                            'options' => ['TD01' => 'Fattura', 'TD04' => 'Nota credito', 'TD05' => 'Nota debito', 'TD06' => 'Parcella'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('stato_sdi', [
                            'label' => 'Stato SDI',
                            'options' => ['bozza' => 'Bozza', 'generata' => 'Generata', 'inviata' => 'Inviata', 'consegnata' => 'Consegnata', 'accettata' => 'Accettata', 'rifiutata' => 'Rifiutata', 'scartata' => 'Scartata'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('anno', [
                            'label' => 'Anno',
                            'options' => array_combine(range(date('Y'), date('Y') - 5), range(date('Y'), date('Y') - 5)),
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-2">
                        <?= $this->Form->control('data_from', [
                            'label' => 'Data da',
                            'type' => 'date',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('data_to', [
                            'label' => 'Data a',
                            'type' => 'date',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-8 d-flex align-items-end gap-2">
                        <?= $this->Form->button('<i data-lucide="search" style="width:14px;height:14px;"></i> Filtra', [
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-sm',
                            'escapeTitle' => false,
                        ]) ?>
                        <?= $this->Html->link('<i data-lucide="x" style="width:14px;height:14px;"></i> Reset', ['action' => 'indexPassive'], [
                            'class' => 'btn btn-outline-secondary btn-sm',
                            'escapeTitle' => false,
                        ]) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="table-toolbar">
        <div class="toolbar-left">
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="fatture-passive-table" title="Mostra tutte le colonne">
                <i data-lucide="columns-3" style="width:14px;height:14px;"></i>
                <span class="btn-label">Mostra tutte</span>
            </button>
            <span class="hidden-columns-indicator">
                <i data-lucide="eye-off" style="width:12px;height:12px;"></i>
                +36 colonne nascoste
            </span>
        </div>
        <div class="toolbar-right">
            <div class="table-meta">
                <span class="meta-item">
                    <i data-lucide="file-input" style="width:14px;height:14px;"></i>
                    <?= $this->Paginator->param('count') ?> fatture ricevute
                </span>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table data-table-id="fatture-passive-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('tipo_documento', 'Tipo') ?></th>
                    <th><?= $this->Paginator->sort('anagrafica_id', 'Fornitore') ?></th>
                    <th><?= $this->Paginator->sort('numero') ?></th>
                    <th><?= $this->Paginator->sort('data') ?></th>
                    <th><?= $this->Paginator->sort('totale_documento', 'Totale') ?></th>
                    <th><?= $this->Paginator->sort('stato_sdi', 'Stato SDI') ?></th>

                    <!-- Colonne nascoste -->
                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('anno') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('divisa') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('imponibile_totale', 'Imponibile') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('iva_totale', 'IVA') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('ritenuta_acconto', 'Ritenuta') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('bollo_virtuale', 'Bollo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('esigibilita_iva') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('condizioni_pagamento') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modalita_pagamento') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('data_scadenza_pagamento', 'Scadenza') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('iban') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('nome_file') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('sdi_identificativo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('sdi_data_ricezione') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('is_active', 'Attivo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('created') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modified') ?></th>

                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fatture as $fattura): ?>
                <tr>
                    <td>
                        <span class="badge bg-secondary"><?= h($fattura->tipo_documento) ?></span>
                    </td>
                    <td>
                        <?= $fattura->hasValue('anagrafiche') ? $this->Html->link(
                            h($fattura->anagrafiche->denominazione ?: $fattura->anagrafiche->nome . ' ' . $fattura->anagrafiche->cognome),
                            ['controller' => 'Anagrafiche', 'action' => 'view', $fattura->anagrafiche->id]
                        ) : '<em class="text-muted">-</em>' ?>
                    </td>
                    <td><strong><?= h($fattura->numero) ?></strong></td>
                    <td><?= $fattura->data ? $fattura->data->format('d/m/Y') : '-' ?></td>
                    <td class="text-end"><strong><?= $this->Number->currency($fattura->totale_documento, 'EUR') ?></strong></td>
                    <td>
                        <?php
                        $statoBadge = match($fattura->stato_sdi) {
                            'bozza' => 'bg-secondary',
                            'generata' => 'bg-info',
                            'inviata' => 'bg-primary',
                            'consegnata', 'accettata' => 'bg-success',
                            'rifiutata', 'errore', 'scartata' => 'bg-danger',
                            'mancata_consegna' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $statoBadge ?>"><?= h(ucfirst($fattura->stato_sdi ?: 'bozza')) ?></span>
                    </td>

                    <!-- Colonne nascoste -->
                    <td class="col-hidden"><?= $this->Number->format($fattura->id) ?></td>
                    <td class="col-hidden"><?= h($fattura->anno) ?></td>
                    <td class="col-hidden"><?= h($fattura->divisa) ?></td>
                    <td class="col-hidden text-end"><?= $this->Number->currency($fattura->imponibile_totale, 'EUR') ?></td>
                    <td class="col-hidden text-end"><?= $this->Number->currency($fattura->iva_totale, 'EUR') ?></td>
                    <td class="col-hidden text-end"><?= $fattura->ritenuta_acconto !== null ? $this->Number->currency($fattura->ritenuta_acconto, 'EUR') : '-' ?></td>
                    <td class="col-hidden"><?= $fattura->bollo_virtuale ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="col-hidden"><?= h($fattura->esigibilita_iva) ?></td>
                    <td class="col-hidden"><?= h($fattura->condizioni_pagamento) ?></td>
                    <td class="col-hidden"><?= h($fattura->modalita_pagamento) ?></td>
                    <td class="col-hidden"><?= $fattura->data_scadenza_pagamento ? $fattura->data_scadenza_pagamento->format('d/m/Y') : '-' ?></td>
                    <td class="col-hidden"><?= h($fattura->iban) ?></td>
                    <td class="col-hidden"><?= h($fattura->nome_file) ?></td>
                    <td class="col-hidden"><?= h($fattura->sdi_identificativo) ?></td>
                    <td class="col-hidden"><?= h($fattura->sdi_data_ricezione) ?></td>
                    <td class="col-hidden"><?= $fattura->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="col-hidden"><?= $fattura->created ? $fattura->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $fattura->modified ? $fattura->modified->format('d/m/Y H:i') : '-' ?></td>

                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $fattura->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $fattura->id]) ?>
                        <?= $this->Form->postLink(
                            __('Elimina'),
                            ['action' => 'delete', $fattura->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Sei sicuro di voler eliminare la fattura #{0}?', $fattura->numero),
                            ]
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
