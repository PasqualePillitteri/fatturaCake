<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Listini $listini
 * @var \Cake\Collection\CollectionInterface|string[] $tenants
 * @var \Cake\Collection\CollectionInterface|string[] $prodotti
 */

$valute = ['EUR' => 'Euro (EUR)', 'USD' => 'Dollaro USA (USD)', 'GBP' => 'Sterlina (GBP)', 'CHF' => 'Franco Svizzero (CHF)'];
?>
<div class="listini form content form-content">
    <div class="page-header">
        <h3><?= __('Nuovo Listino') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($listini) ?>

    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="tags" style="width:18px;height:18px;"></i>
            Dati Listino
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('nome', ['label' => ['text' => 'Nome', 'class' => 'required'], 'placeholder' => 'Es: Listino Base 2025']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('valuta', ['type' => 'select', 'options' => $valute, 'label' => 'Valuta', 'default' => 'EUR']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('descrizione', ['type' => 'textarea', 'rows' => 2, 'label' => 'Descrizione']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('data_inizio', ['type' => 'date', 'label' => 'Data Inizio Validita']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('data_fine', ['type' => 'date', 'label' => 'Data Fine Validita']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_default', ['type' => 'checkbox', 'label' => 'Listino predefinito']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo', 'default' => true]) ?>
                </div>
            </div>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
