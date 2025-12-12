<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ConfigurazioniSdi> $configurazioniSdi
 */
$hiddenCount = 30;
?>
<div class="configurazioniSdi index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Configurazioni SDI') ?></h3>
        <?= $this->Html->link(
            '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuova Configurazione'),
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
        <div class="collapse <?= $this->request->getQuery('q') || $this->request->getQuery('ambiente') ? 'show' : '' ?>" id="filtriCollapse">
            <div class="card-body">
                <?= $this->Form->create(null, ['type' => 'get', 'valueSources' => ['query']]) ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <?= $this->Form->control('q', [
                            'label' => 'Cerca',
                            'placeholder' => 'Denominazione, P.IVA, email...',
                            'class' => 'form-control form-control-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('ambiente', [
                            'label' => 'Ambiente',
                            'options' => ['produzione' => 'Produzione', 'test' => 'Test', 'sandbox' => 'Sandbox'],
                            'empty' => 'Tutti',
                            'class' => 'form-select form-select-sm',
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $this->Form->control('usa_firma_digitale', [
                            'label' => 'Firma Digitale',
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
            <button type="button" class="btn-toolbar btn-outline-secondary" data-toggle-table="config-sdi-table" title="Mostra tutte le colonne">
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
        <table data-table-id="config-sdi-table">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('tenant_id', 'Tenant') ?></th>
                    <th><?= $this->Paginator->sort('cedente_denominazione', 'Cedente') ?></th>
                    <th><?= $this->Paginator->sort('cedente_partita_iva', 'P.IVA') ?></th>
                    <th><?= $this->Paginator->sort('ambiente') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Attivo') ?></th>

                    <th class="col-hidden"><?= $this->Paginator->sort('id') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('aruba_username') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('endpoint_upload') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('endpoint_stato') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('endpoint_notifiche') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_nome') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_cognome') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_codice_fiscale') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_regime_fiscale') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_indirizzo') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_numero_civico') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_cap') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_comune') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_provincia') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_nazione') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_telefono') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_email') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('cedente_pec') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('codice_fiscale_trasmittente') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('id_paese_trasmittente') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('id_codice_trasmittente') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('progressivo_invio') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('formato_trasmissione') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('iban_predefinito') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('banca_predefinita') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('usa_firma_digitale') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('certificato_path') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('ultima_sincronizzazione') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('created') ?></th>
                    <th class="col-hidden"><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($configurazioniSdi as $config): ?>
                <tr>
                    <td><?= $config->hasValue('tenant') ? $this->Html->link(h($config->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $config->tenant->id]) : '' ?></td>
                    <td><strong><?= h($config->cedente_denominazione) ?></strong></td>
                    <td><?= h($config->cedente_partita_iva) ?></td>
                    <td>
                        <?php
                        $ambienteBadge = match($config->ambiente) {
                            'produzione' => 'bg-success',
                            'test', 'sandbox' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $ambienteBadge ?>"><?= h(ucfirst($config->ambiente)) ?></span>
                    </td>
                    <td><?= $config->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>

                    <td class="col-hidden"><?= $this->Number->format($config->id) ?></td>
                    <td class="col-hidden"><?= h($config->aruba_username) ?></td>
                    <td class="col-hidden"><?= h($config->endpoint_upload) ?></td>
                    <td class="col-hidden"><?= h($config->endpoint_stato) ?></td>
                    <td class="col-hidden"><?= h($config->endpoint_notifiche) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_nome) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_cognome) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_codice_fiscale) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_regime_fiscale) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_indirizzo) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_numero_civico) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_cap) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_comune) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_provincia) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_nazione) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_telefono) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_email) ?></td>
                    <td class="col-hidden"><?= h($config->cedente_pec) ?></td>
                    <td class="col-hidden"><?= h($config->codice_fiscale_trasmittente) ?></td>
                    <td class="col-hidden"><?= h($config->id_paese_trasmittente) ?></td>
                    <td class="col-hidden"><?= h($config->id_codice_trasmittente) ?></td>
                    <td class="col-hidden"><?= $this->Number->format($config->progressivo_invio) ?></td>
                    <td class="col-hidden"><?= h($config->formato_trasmissione) ?></td>
                    <td class="col-hidden"><?= h($config->iban_predefinito) ?></td>
                    <td class="col-hidden"><?= h($config->banca_predefinita) ?></td>
                    <td class="col-hidden"><?= $config->usa_firma_digitale ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                    <td class="col-hidden"><?= h($config->certificato_path) ?></td>
                    <td class="col-hidden"><?= h($config->ultima_sincronizzazione) ?></td>
                    <td class="col-hidden"><?= $config->created ? $config->created->format('d/m/Y H:i') : '-' ?></td>
                    <td class="col-hidden"><?= $config->modified ? $config->modified->format('d/m/Y H:i') : '-' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $config->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $config->id]) ?>
                        <?= $this->Form->postLink(__('Elimina'), ['action' => 'delete', $config->id], ['method' => 'delete', 'confirm' => __('Sei sicuro di voler eliminare questa configurazione?')]) ?>
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
