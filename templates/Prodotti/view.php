<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Prodotti $prodotti
 */
?>
<div class="prodotti view content">
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
                ['action' => 'edit', $prodotti->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                ['action' => 'delete', $prodotti->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare {0}?', $prodotti->nome),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0"><?= h($prodotti->nome) ?></h3>
        <div class="ms-3">
            <?php
            $tipoBadge = match($prodotti->tipo) {
                'prodotto' => 'bg-primary',
                'servizio' => 'bg-info',
                default => 'bg-secondary'
            };
            ?>
            <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($prodotti->tipo)) ?></span>
            <?= $prodotti->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?>
        </div>
    </div>

    <div class="row">
        <!-- Dati Principali -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="package" style="width:16px;height:16px;"></i> Dati Prodotto
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Codice') ?></th>
                            <td><code><?= h($prodotti->codice) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Nome') ?></th>
                            <td><strong><?= h($prodotti->nome) ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Tipo') ?></th>
                            <td><span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($prodotti->tipo)) ?></span></td>
                        </tr>
                        <tr>
                            <th><?= __('Categoria') ?></th>
                            <td>
                                <?php if ($prodotti->hasValue('categoria')): ?>
                                    <?= $this->Html->link(h($prodotti->categoria->nome), ['controller' => 'CategorieProdotti', 'action' => 'view', $prodotti->categoria->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Descrizione') ?></th>
                            <td><?= h($prodotti->descrizione) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Unità di Misura') ?></th>
                            <td><?= h($prodotti->unita_misura) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Prezzi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="euro" style="width:16px;height:16px;"></i> Prezzi
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Prezzo Acquisto') ?></th>
                            <td><?= $prodotti->prezzo_acquisto !== null ? $this->Number->currency($prodotti->prezzo_acquisto, 'EUR') : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Prezzo Vendita') ?></th>
                            <td><strong><?= $prodotti->prezzo_vendita !== null ? $this->Number->currency($prodotti->prezzo_vendita, 'EUR') : '<em class="text-muted">-</em>' ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Aliquota IVA') ?></th>
                            <td><?= $this->Number->toPercentage($prodotti->aliquota_iva, 0) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Prezzo IVA inclusa') ?></th>
                            <td><?= $prodotti->prezzo_ivato ? '<span class="badge bg-info">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dati Fiscali e Magazzino -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="file-text" style="width:16px;height:16px;"></i> Dati Fiscali
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Codice Tipo') ?></th>
                            <td><?= h($prodotti->codice_tipo) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Codice Valore') ?></th>
                            <td><?= h($prodotti->codice_valore) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Natura') ?></th>
                            <td><?= h($prodotti->natura) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Rif. Normativo') ?></th>
                            <td><?= h($prodotti->riferimento_normativo) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Soggetto Ritenuta') ?></th>
                            <td><?= $prodotti->soggetto_ritenuta ? '<span class="badge bg-warning">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if ($prodotti->tipo === 'prodotto'): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="warehouse" style="width:16px;height:16px;"></i> Magazzino
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Gestione Magazzino') ?></th>
                            <td><?= $prodotti->gestione_magazzino ? '<span class="badge bg-info">Attiva</span>' : '<span class="badge bg-secondary">Non attiva</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Giacenza') ?></th>
                            <td>
                                <?php
                                $giacenzaClass = '';
                                if ($prodotti->gestione_magazzino && $prodotti->scorta_minima !== null) {
                                    $giacenzaClass = $prodotti->giacenza <= $prodotti->scorta_minima ? 'text-danger fw-bold' : 'text-success';
                                }
                                ?>
                                <span class="<?= $giacenzaClass ?>"><?= $this->Number->format($prodotti->giacenza) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Scorta Minima') ?></th>
                            <td><?= $prodotti->scorta_minima !== null ? $this->Number->format($prodotti->scorta_minima) : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="settings" style="width:16px;height:16px;"></i> Informazioni Sistema
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('ID') ?></th>
                            <td><code><?= $this->Number->format($prodotti->id) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Tenant') ?></th>
                            <td>
                                <?php if ($prodotti->hasValue('tenant')): ?>
                                    <?= $this->Html->link(h($prodotti->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $prodotti->tenant->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Ordinamento') ?></th>
                            <td><?= $this->Number->format($prodotti->sort_order) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Creato il') ?></th>
                            <td><?= $prodotti->created ? $prodotti->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Modificato il') ?></th>
                            <td><?= $prodotti->modified ? $prodotti->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Descrizione Estesa -->
    <?php if ($prodotti->descrizione_estesa): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="align-left" style="width:16px;height:16px;"></i> Descrizione Estesa
        </div>
        <div class="card-body">
            <?= $this->Text->autoParagraph(h($prodotti->descrizione_estesa)) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Note -->
    <?php if ($prodotti->note): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="sticky-note" style="width:16px;height:16px;"></i> Note
        </div>
        <div class="card-body">
            <?= $this->Text->autoParagraph(h($prodotti->note)) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Listini Correlati -->
    <?php if (!empty($prodotti->listini)): ?>
    <div class="card">
        <div class="card-header">
            <i data-lucide="list" style="width:16px;height:16px;"></i> Listini Associati
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('Valuta') ?></th>
                            <th><?= __('Inizio') ?></th>
                            <th><?= __('Fine') ?></th>
                            <th><?= __('Default') ?></th>
                            <th><?= __('Attivo') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prodotti->listini as $listino): ?>
                        <tr>
                            <td><strong><?= h($listino->nome) ?></strong></td>
                            <td><span class="badge bg-secondary"><?= h($listino->valuta) ?></span></td>
                            <td><?= $listino->data_inizio ? $listino->data_inizio->format('d/m/Y') : '-' ?></td>
                            <td><?= $listino->data_fine ? $listino->data_fine->format('d/m/Y') : '-' ?></td>
                            <td><?= $listino->is_default ? '<span class="badge bg-primary">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td><?= $listino->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['controller' => 'Listini', 'action' => 'view', $listino->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
