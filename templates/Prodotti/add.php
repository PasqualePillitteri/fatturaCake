<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Prodotti $prodotti
 * @var \Cake\Collection\CollectionInterface|string[] $tenants
 * @var \Cake\Collection\CollectionInterface|string[] $categorias
 * @var \Cake\Collection\CollectionInterface|string[] $listini
 */

$tipiProdotto = ['prodotto' => 'Prodotto', 'servizio' => 'Servizio'];
$aliquoteIva = ['22' => '22%', '10' => '10%', '5' => '5%', '4' => '4%', '0' => '0%'];
?>
<div class="prodotti form content form-content">
    <div class="page-header">
        <h3><?= __('Nuovo Prodotto/Servizio') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
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
                    <?= $this->Form->control('codice', ['label' => ['text' => 'Codice', 'class' => 'required'], 'placeholder' => 'PROD001']) ?>
                </div>
                <div class="form-col">
                    <?= $this->Form->control('nome', ['label' => ['text' => 'Nome', 'class' => 'required'], 'placeholder' => 'Nome prodotto/servizio']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('tipo', ['type' => 'select', 'options' => $tipiProdotto, 'label' => 'Tipo']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('descrizione', ['type' => 'textarea', 'rows' => 2, 'label' => 'Descrizione', 'placeholder' => 'Descrizione per fattura...']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('categoria_id', ['options' => $categorias ?? [], 'empty' => '-- Seleziona categoria --', 'label' => 'Categoria']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('unita_misura', ['label' => 'Unita Misura', 'placeholder' => 'NR', 'default' => 'NR']) ?>
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
                    <?= $this->Form->control('prezzo_acquisto', ['type' => 'number', 'step' => '0.01', 'label' => 'Prezzo Acquisto', 'placeholder' => '0.00']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('prezzo_vendita', ['type' => 'number', 'step' => '0.01', 'label' => ['text' => 'Prezzo Vendita', 'class' => 'required'], 'placeholder' => '0.00']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('aliquota_iva', ['type' => 'select', 'options' => $aliquoteIva, 'label' => 'Aliquota IVA', 'default' => '22']) ?>
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
                    <?= $this->Form->control('codice_tipo', ['label' => 'Codice Tipo', 'placeholder' => 'Es: TARIC']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('codice_valore', ['label' => 'Codice Valore', 'placeholder' => 'Es: 12345678']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('natura', ['label' => 'Natura', 'placeholder' => 'Es: N2.2']) ?>
                    <span class="help-text">Codice natura per IVA a 0%</span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('riferimento_normativo', ['label' => 'Riferimento Normativo', 'placeholder' => 'Es: Art. 10 DPR 633/72']) ?>
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
                    <?= $this->Form->control('giacenza', ['type' => 'number', 'label' => 'Giacenza', 'default' => 0]) ?>
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
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo', 'default' => true]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => 'Ordine', 'default' => 0]) ?>
                </div>
            </div>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save"></i> ' . __('Salva'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
