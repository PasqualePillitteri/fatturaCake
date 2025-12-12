<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Listini $listini
 */
?>
<div class="listini view content">
    <!-- Toolbar Azioni -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary btn-sm', 'escapeTitle' => false]
            ) ?>
        </div>
        <div class="d-flex gap-2">
            <?= $this->Html->link(
                '<i data-lucide="edit" style="width:16px;height:16px;"></i> ' . __('Modifica'),
                ['action' => 'edit', $listini->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                ['action' => 'delete', $listini->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare {0}?', $listini->nome),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0"><?= h($listini->nome) ?></h3>
        <div class="ms-3">
            <span class="badge bg-secondary"><?= h($listini->valuta) ?></span>
            <?= $listini->is_default ? '<span class="badge bg-primary">Default</span>' : '' ?>
            <?= $listini->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?>
        </div>
    </div>

    <div class="row">
        <!-- Dati Listino -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="list" style="width:16px;height:16px;"></i> Dati Listino
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Nome') ?></th>
                            <td><strong><?= h($listini->nome) ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Valuta') ?></th>
                            <td><span class="badge bg-secondary"><?= h($listini->valuta) ?></span></td>
                        </tr>
                        <tr>
                            <th><?= __('Data Inizio') ?></th>
                            <td><?= $listini->data_inizio ? $listini->data_inizio->format('d/m/Y') : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Data Fine') ?></th>
                            <td><?= $listini->data_fine ? $listini->data_fine->format('d/m/Y') : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Default') ?></th>
                            <td><?= $listini->is_default ? '<span class="badge bg-primary">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Stato') ?></th>
                            <td><?= $listini->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Sistema -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="settings" style="width:16px;height:16px;"></i> Informazioni Sistema
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('ID') ?></th>
                            <td><code><?= $this->Number->format($listini->id) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Tenant') ?></th>
                            <td>
                                <?php if ($listini->hasValue('tenant')): ?>
                                    <?= $this->Html->link(h($listini->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $listini->tenant->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Creato il') ?></th>
                            <td><?= $listini->created ? $listini->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Modificato il') ?></th>
                            <td><?= $listini->modified ? $listini->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Descrizione -->
    <?php if ($listini->descrizione): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="align-left" style="width:16px;height:16px;"></i> Descrizione
        </div>
        <div class="card-body">
            <?= $this->Text->autoParagraph(h($listini->descrizione)) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Prodotti Correlati -->
    <?php if (!empty($listini->prodotti)): ?>
    <div class="card">
        <div class="card-header">
            <i data-lucide="package" style="width:16px;height:16px;"></i> Prodotti Associati (<?= count($listini->prodotti) ?>)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= __('Codice') ?></th>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('Tipo') ?></th>
                            <th><?= __('Prezzo') ?></th>
                            <th><?= __('IVA') ?></th>
                            <th><?= __('Attivo') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($listini->prodotti, 0, 15) as $prodotto): ?>
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
                            <td class="text-end"><strong><?= $this->Number->currency($prodotto->prezzo_vendita, 'EUR') ?></strong></td>
                            <td class="text-center"><?= $this->Number->toPercentage($prodotto->aliquota_iva, 0) ?></td>
                            <td><?= $prodotto->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['controller' => 'Prodotti', 'action' => 'view', $prodotto->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($listini->prodotti) > 15): ?>
            <div class="card-footer text-center">
                <em class="text-muted">Mostrati 15 di <?= count($listini->prodotti) ?> prodotti</em>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
