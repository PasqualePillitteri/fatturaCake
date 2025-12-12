<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Piano $piano
 */
?>
<div class="piani form content form-content">
    <div class="page-header">
        <h3><?= __('Nuovo Piano') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($piano) ?>

    <!-- Dati Piano -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="credit-card" style="width:18px;height:18px;"></i>
            Dati Piano
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('nome', [
                        'label' => ['text' => 'Nome Piano', 'class' => 'required'],
                        'placeholder' => 'es. Base, Professional, Enterprise',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('sort_order', [
                        'label' => 'Ordine',
                        'type' => 'number',
                        'default' => 0,
                        'help' => 'Ordine di visualizzazione (0 = primo)',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('descrizione', [
                        'label' => 'Descrizione',
                        'type' => 'textarea',
                        'rows' => 3,
                        'placeholder' => 'Descrizione opzionale del piano',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Prezzi -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="euro" style="width:18px;height:18px;"></i>
            Prezzi
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('prezzo_mensile', [
                        'label' => ['text' => 'Prezzo Mensile', 'class' => 'required'],
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'default' => '0.00',
                        'help' => 'Prezzo in EUR',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('prezzo_annuale', [
                        'label' => ['text' => 'Prezzo Annuale', 'class' => 'required'],
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'default' => '0.00',
                        'help' => 'Prezzo in EUR',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stato -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="toggle-left" style="width:18px;height:18px;"></i>
            Stato
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('is_active', [
                        'label' => 'Piano Attivo',
                        'type' => 'checkbox',
                        'default' => true,
                        'help' => 'I piani disattivi non sono selezionabili per nuovi abbonamenti',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Azioni -->
    <div class="form-card">
        <div class="form-card-body">
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
