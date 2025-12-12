<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategorieProdotti $categorieProdotti
 */
?>
<div class="categorie-prodotti view content">
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
                ['action' => 'edit', $categorieProdotti->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                ['action' => 'delete', $categorieProdotti->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare {0}?', $categorieProdotti->nome),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0">
            <i data-lucide="folder" style="width:24px;height:24px;"></i>
            <?= h($categorieProdotti->nome) ?>
        </h3>
        <div class="ms-3">
            <?= $categorieProdotti->is_active ? '<span class="badge bg-success">Attiva</span>' : '<span class="badge bg-secondary">Non attiva</span>' ?>
        </div>
    </div>

    <div class="row">
        <!-- Dati Categoria -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="folder" style="width:16px;height:16px;"></i> Dati Categoria
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Nome') ?></th>
                            <td><strong><?= h($categorieProdotti->nome) ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Categoria Padre') ?></th>
                            <td>
                                <?php if ($categorieProdotti->hasValue('parent_categorie_prodotti')): ?>
                                    <?= $this->Html->link(
                                        '<i data-lucide="folder" style="width:14px;height:14px;"></i> ' . h($categorieProdotti->parent_categorie_prodotti->nome),
                                        ['action' => 'view', $categorieProdotti->parent_categorie_prodotti->id],
                                        ['escapeTitle' => false]
                                    ) ?>
                                <?php else: ?>
                                    <em class="text-muted">Categoria principale</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Ordinamento') ?></th>
                            <td><?= $this->Number->format($categorieProdotti->sort_order) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Stato') ?></th>
                            <td><?= $categorieProdotti->is_active ? '<span class="badge bg-success">Attiva</span>' : '<span class="badge bg-secondary">Non attiva</span>' ?></td>
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
                            <td><code><?= $this->Number->format($categorieProdotti->id) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Tenant') ?></th>
                            <td>
                                <?php if ($categorieProdotti->hasValue('tenant')): ?>
                                    <?= $this->Html->link(h($categorieProdotti->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $categorieProdotti->tenant->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Creato il') ?></th>
                            <td><?= $categorieProdotti->created ? $categorieProdotti->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Modificato il') ?></th>
                            <td><?= $categorieProdotti->modified ? $categorieProdotti->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Descrizione -->
    <?php if ($categorieProdotti->descrizione): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="align-left" style="width:16px;height:16px;"></i> Descrizione
        </div>
        <div class="card-body">
            <?= $this->Text->autoParagraph(h($categorieProdotti->descrizione)) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sottocategorie -->
    <?php if (!empty($categorieProdotti->child_categorie_prodotti)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="folders" style="width:16px;height:16px;"></i> Sottocategorie
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('Ordinamento') ?></th>
                            <th><?= __('Attiva') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorieProdotti->child_categorie_prodotti as $child): ?>
                        <tr>
                            <td>
                                <i data-lucide="folder" style="width:14px;height:14px;"></i>
                                <strong><?= h($child->nome) ?></strong>
                            </td>
                            <td><?= $this->Number->format($child->sort_order) ?></td>
                            <td><?= $child->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['action' => 'view', $child->id]) ?>
                                <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $child->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Prodotti in questa categoria -->
    <?php if (!empty($categorieProdotti->prodotti)): ?>
    <div class="card">
        <div class="card-header">
            <i data-lucide="package" style="width:16px;height:16px;"></i> Prodotti in questa Categoria (<?= count($categorieProdotti->prodotti) ?>)
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
                            <th><?= __('Attivo') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($categorieProdotti->prodotti, 0, 10) as $prodotto): ?>
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
                            <td class="text-end"><?= $this->Number->currency($prodotto->prezzo_vendita, 'EUR') ?></td>
                            <td><?= $prodotto->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['controller' => 'Prodotti', 'action' => 'view', $prodotto->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($categorieProdotti->prodotti) > 10): ?>
            <div class="card-footer text-center">
                <em class="text-muted">Mostrati 10 di <?= count($categorieProdotti->prodotti) ?> prodotti</em>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
