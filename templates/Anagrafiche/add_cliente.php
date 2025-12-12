<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Anagrafiche $anagrafiche
 */

$regimiFiscali = [
    'RF01' => 'RF01 - Ordinario',
    'RF02' => 'RF02 - Contribuenti minimi',
    'RF04' => 'RF04 - Agricoltura',
    'RF17' => 'RF17 - IVA per cassa',
    'RF18' => 'RF18 - Altro',
    'RF19' => 'RF19 - Regime forfettario',
];
?>
<div class="anagrafiche form content form-content">
    <div class="page-header">
        <h3><?= __('Nuovo Cliente') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'indexClienti'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($anagrafiche) ?>
    <?= $this->Form->hidden('tipo', ['value' => 'cliente']) ?>

    <!-- Dati Principali -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="user" style="width:18px;height:18px;"></i>
            Dati Principali
            <span class="badge bg-primary ms-2">Cliente</span>
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('denominazione', [
                        'label' => 'Denominazione / Ragione Sociale',
                        'placeholder' => 'Es: Acme S.r.l.'
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('nome', ['label' => 'Nome', 'placeholder' => 'Nome (persona fisica)']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('cognome', ['label' => 'Cognome', 'placeholder' => 'Cognome (persona fisica)']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('partita_iva', ['label' => 'Partita IVA', 'placeholder' => 'Es: 12345678901']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('codice_fiscale', ['label' => 'Codice Fiscale', 'placeholder' => 'Es: RSSMRA80A01H501U']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('regime_fiscale', [
                        'type' => 'select',
                        'options' => $regimiFiscali,
                        'empty' => '-- Seleziona regime --',
                        'label' => 'Regime Fiscale'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Indirizzo -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="map-pin" style="width:18px;height:18px;"></i>
            Indirizzo
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('indirizzo', ['label' => 'Indirizzo', 'placeholder' => 'Via/Piazza...']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('numero_civico', ['label' => 'N. Civico', 'placeholder' => '10']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('cap', ['label' => 'CAP', 'placeholder' => '00100']) ?>
                </div>
                <div class="form-col">
                    <?= $this->Form->control('comune', ['label' => 'Comune', 'placeholder' => 'Roma']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('provincia', ['label' => 'Provincia', 'placeholder' => 'RM', 'maxlength' => 2]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('nazione', ['label' => 'Nazione', 'default' => 'IT', 'placeholder' => 'IT']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Contatti -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="phone" style="width:18px;height:18px;"></i>
            Contatti
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('telefono', ['label' => 'Telefono', 'placeholder' => '+39 06 1234567']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('email', ['label' => 'Email', 'type' => 'email', 'placeholder' => 'info@esempio.it']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('pec', ['label' => 'PEC', 'type' => 'email', 'placeholder' => 'pec@pec.it']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Fatturazione Elettronica -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="file-text" style="width:18px;height:18px;"></i>
            Fatturazione Elettronica
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('codice_sdi', ['label' => 'Codice SDI', 'placeholder' => 'Es: 0000000', 'maxlength' => 7]) ?>
                    <span class="help-text">Codice destinatario per fatturazione elettronica (7 caratteri)</span>
                </div>
                <div class="form-col">
                    <?= $this->Form->control('riferimento_amministrazione', ['label' => 'Rif. Amministrazione', 'placeholder' => 'Codice ufficio PA']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('split_payment', ['type' => 'checkbox', 'label' => 'Split Payment']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Note e Stato -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="pencil" style="width:18px;height:18px;"></i>
            Note e Stato
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('note', ['label' => 'Note', 'type' => 'textarea', 'rows' => 3, 'placeholder' => 'Note interne...']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo', 'default' => true]) ?>
                </div>
            </div>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'indexClienti'], ['class' => 'btn btn-secondary']) ?>
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
