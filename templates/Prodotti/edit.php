<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Prodotti $prodotti
 * @var string[]|\Cake\Collection\CollectionInterface $tenants
 * @var string[]|\Cake\Collection\CollectionInterface $categorias
 * @var string[]|\Cake\Collection\CollectionInterface $listini
 */

$tipiProdotto = ['prodotto' => 'Prodotto', 'servizio' => 'Servizio'];
$aliquoteIva = ['22' => '22%', '10' => '10%', '5' => '5%', '4' => '4%', '0' => '0%'];
?>
<div class="prodotti form content form-content">
    <div class="page-header">
        <h3><?= __('Modifica') ?>: <?= h($prodotti->nome) ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="eye"></i> ' . __('Visualizza'),
                ['action' => 'view', $prodotti->id],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($prodotti) ?>

    <!-- Dati Principali -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="package"></i>
            Dati Principali
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('codice', ['label' => ['text' => 'Codice', 'class' => 'required']]) ?>
                </div>
                <div class="form-col">
                    <?= $this->Form->control('nome', ['label' => ['text' => 'Nome', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('tipo', ['type' => 'select', 'options' => $tipiProdotto, 'label' => 'Tipo']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('descrizione', ['type' => 'textarea', 'rows' => 2, 'label' => 'Descrizione']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('categoria_id', ['options' => $categorias ?? [], 'empty' => '-- Seleziona categoria --', 'label' => 'Categoria']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('unita_misura', ['label' => 'Unita Misura']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Prezzi e IVA -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="euro"></i>
            Prezzi e IVA
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('prezzo_acquisto', ['type' => 'number', 'step' => '0.01', 'label' => 'Prezzo Acquisto']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('prezzo_vendita', ['type' => 'number', 'step' => '0.01', 'label' => ['text' => 'Prezzo Vendita', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('aliquota_iva', ['type' => 'select', 'options' => $aliquoteIva, 'label' => 'Aliquota IVA']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('prezzo_ivato', ['type' => 'checkbox', 'label' => 'Prezzo IVA inclusa']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('soggetto_ritenuta', ['type' => 'checkbox', 'label' => 'Soggetto a ritenuta']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Fatturazione Elettronica -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="file-text"></i>
            Dati Fatturazione Elettronica
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('codice_tipo', ['label' => 'Codice Tipo']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('codice_valore', ['label' => 'Codice Valore']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('natura', ['label' => 'Natura']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('riferimento_normativo', ['label' => 'Riferimento Normativo']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Magazzino -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="warehouse"></i>
            Gestione Magazzino
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('gestione_magazzino', ['type' => 'checkbox', 'label' => 'Gestione magazzino']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('giacenza', ['type' => 'number', 'label' => 'Giacenza']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('scorta_minima', ['type' => 'number', 'label' => 'Scorta Minima']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Note e Stato -->
    <div class="form-card">
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('note', ['type' => 'textarea', 'rows' => 2, 'label' => 'Note interne']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => 'Ordine']) ?>
                </div>
            </div>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->postLink(
                        '<i data-lucide="trash-2"></i> ' . __('Elimina'),
                        ['action' => 'delete', $prodotti->id],
                        ['confirm' => __('Sei sicuro di voler eliminare {0}?', $prodotti->nome), 'class' => 'btn btn-danger', 'escapeTitle' => false]
                    ) ?>
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save"></i> ' . __('Salva modifiche'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
