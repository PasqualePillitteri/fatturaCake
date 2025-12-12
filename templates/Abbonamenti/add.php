<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Abbonamento $abbonamento
 * @var array $tenants
 * @var array $piani
 */
?>
<div class="abbonamenti form content form-content">
    <div class="page-header">
        <h3><?= __('Nuovo Abbonamento') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($abbonamento) ?>

    <!-- Selezione Tenant e Piano -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="building" style="width:18px;height:18px;"></i>
            Tenant e Piano
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('tenant_id', [
                        'label' => ['text' => 'Tenant', 'class' => 'required'],
                        'options' => $tenants,
                        'empty' => '-- Seleziona Tenant --',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('piano_id', [
                        'label' => ['text' => 'Piano', 'class' => 'required'],
                        'options' => $piani,
                        'empty' => '-- Seleziona Piano --',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dettagli Abbonamento -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="calendar-check" style="width:18px;height:18px;"></i>
            Dettagli Abbonamento
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('tipo', [
                        'label' => ['text' => 'Tipo Abbonamento', 'class' => 'required'],
                        'type' => 'select',
                        'options' => [
                            'mensile' => 'Mensile',
                            'annuale' => 'Annuale',
                        ],
                        'default' => 'mensile',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('stato', [
                        'label' => ['text' => 'Stato', 'class' => 'required'],
                        'type' => 'select',
                        'options' => [
                            'attivo' => 'Attivo',
                            'sospeso' => 'Sospeso',
                            'scaduto' => 'Scaduto',
                            'cancellato' => 'Cancellato',
                        ],
                        'default' => 'attivo',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('data_inizio', [
                        'label' => ['text' => 'Data Inizio', 'class' => 'required'],
                        'type' => 'date',
                        'default' => date('Y-m-d'),
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('data_fine', [
                        'label' => 'Data Fine',
                        'type' => 'date',
                        'help' => 'Lasciare vuoto per abbonamento senza scadenza',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('importo', [
                        'label' => ['text' => 'Importo', 'class' => 'required'],
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'default' => '0.00',
                        'help' => 'Importo in EUR',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="file-text" style="width:18px;height:18px;"></i>
            Note
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('note', [
                        'label' => 'Note',
                        'type' => 'textarea',
                        'rows' => 3,
                        'placeholder' => 'Note opzionali sull\'abbonamento',
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
