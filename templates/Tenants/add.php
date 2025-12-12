<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tenant $tenant
 */

$tipi = ['azienda' => 'Azienda', 'professionista' => 'Professionista'];
?>
<div class="tenants form content form-content">
    <div class="page-header">
        <h3><?= __('Nuovo Tenant') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($tenant) ?>

    <!-- Dati Principali -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="building" style="width:18px;height:18px;"></i>
            Dati Tenant
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('nome', ['label' => ['text' => 'Nome / Ragione Sociale', 'class' => 'required'], 'placeholder' => 'Es: Acme S.r.l.']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('tipo', ['type' => 'select', 'options' => $tipi, 'label' => 'Tipo']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('partita_iva', ['label' => 'Partita IVA', 'placeholder' => '12345678901']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('codice_fiscale', ['label' => 'Codice Fiscale']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('descrizione', ['type' => 'textarea', 'rows' => 2, 'label' => 'Descrizione']) ?>
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
                <div class="form-col-full">
                    <?= $this->Form->control('indirizzo', ['label' => 'Indirizzo', 'placeholder' => 'Via/Piazza...']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('citta', ['label' => 'Citta']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('provincia', ['label' => 'Provincia', 'maxlength' => 2]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('cap', ['label' => 'CAP']) ?>
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
                    <?= $this->Form->control('telefono', ['label' => 'Telefono']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('email', ['type' => 'email', 'label' => 'Email']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('pec', ['type' => 'email', 'label' => 'PEC']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('sito_web', ['label' => 'Sito Web', 'placeholder' => 'https://']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('slug', ['label' => 'Slug', 'placeholder' => 'identificativo-unico']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stato -->
    <div class="form-card">
        <div class="form-card-body">
            <div class="form-row">
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
