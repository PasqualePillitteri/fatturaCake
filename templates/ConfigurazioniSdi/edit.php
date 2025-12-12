<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ConfigurazioniSdi $configurazioniSdi
 * @var string[]|\Cake\Collection\CollectionInterface $tenants
 */

$ambienti = ['test' => 'Test (Sandbox)', 'produzione' => 'Produzione'];
$regimiFiscali = [
    'RF01' => 'RF01 - Ordinario',
    'RF02' => 'RF02 - Contribuenti minimi',
    'RF04' => 'RF04 - Agricoltura',
    'RF05' => 'RF05 - Pesca',
    'RF06' => 'RF06 - Vendita sali e tabacchi',
    'RF07' => 'RF07 - Commercio fiammiferi',
    'RF08' => 'RF08 - Editoria',
    'RF09' => 'RF09 - Gestione servizi telefonia',
    'RF10' => 'RF10 - Rivendita documenti',
    'RF11' => 'RF11 - Agenzie viaggi',
    'RF12' => 'RF12 - Agriturismo',
    'RF13' => 'RF13 - Vendite a domicilio',
    'RF14' => 'RF14 - Intrattenimenti e giochi',
    'RF15' => 'RF15 - Agenzie vendite all\'asta',
    'RF16' => 'RF16 - IVA per cassa',
    'RF17' => 'RF17 - IVA ventilazione',
    'RF18' => 'RF18 - Altro',
    'RF19' => 'RF19 - Forfettario',
];
$formatiTrasmissione = [
    'FPR12' => 'FPR12 - Fattura verso privati',
    'FPA12' => 'FPA12 - Fattura verso PA',
];
?>
<div class="configurazioniSdi form content form-content">
    <div class="page-header">
        <h3><?= __('Modifica Configurazione SDI') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="eye" style="width:16px;height:16px;"></i> ' . __('Visualizza'),
                ['action' => 'view', $configurazioniSdi->id],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($configurazioniSdi) ?>

    <!-- Tenant -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="building" style="width:18px;height:18px;"></i>
            Associazione Tenant
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('tenant_id', ['options' => $tenants, 'empty' => '-- Seleziona Tenant --', 'label' => ['text' => 'Tenant', 'class' => 'required']]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Configurazione Aruba -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="shield" style="width:18px;height:18px;"></i>
            Credenziali Aruba
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('aruba_username', ['label' => ['text' => 'Username Aruba', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('aruba_password', ['type' => 'password', 'label' => ['text' => 'Password Aruba', 'class' => 'required'], 'value' => '']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('ambiente', ['type' => 'select', 'options' => $ambienti, 'label' => 'Ambiente']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Endpoints -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="globe" style="width:18px;height:18px;"></i>
            Endpoints API
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('endpoint_upload', ['label' => 'Endpoint Upload']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('endpoint_stato', ['label' => 'Endpoint Stato']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('endpoint_notifiche', ['label' => 'Endpoint Notifiche']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dati Cedente/Prestatore -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="user" style="width:18px;height:18px;"></i>
            Dati Cedente/Prestatore
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('cedente_denominazione', ['label' => 'Denominazione/Ragione Sociale']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('cedente_nome', ['label' => 'Nome (se persona fisica)']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('cedente_cognome', ['label' => 'Cognome (se persona fisica)']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('cedente_partita_iva', ['label' => ['text' => 'Partita IVA', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('cedente_codice_fiscale', ['label' => 'Codice Fiscale']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('cedente_regime_fiscale', ['type' => 'select', 'options' => $regimiFiscali, 'label' => 'Regime Fiscale']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('cedente_indirizzo', ['label' => 'Indirizzo']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('cedente_numero_civico', ['label' => 'N. Civico']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('cedente_comune', ['label' => 'Comune']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('cedente_provincia', ['label' => 'Provincia', 'maxlength' => 2]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('cedente_cap', ['label' => 'CAP', 'maxlength' => 5]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('cedente_nazione', ['label' => 'Nazione', 'maxlength' => 2]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('cedente_telefono', ['label' => 'Telefono']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('cedente_email', ['type' => 'email', 'label' => 'Email']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('cedente_pec', ['type' => 'email', 'label' => 'PEC']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dati Trasmissione -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="send" style="width:18px;height:18px;"></i>
            Dati Trasmissione
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('id_paese_trasmittente', ['label' => 'Paese Trasmittente', 'maxlength' => 2]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('id_codice_trasmittente', ['label' => 'Codice Trasmittente']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('codice_fiscale_trasmittente', ['label' => 'CF Trasmittente']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('progressivo_invio', ['type' => 'number', 'label' => 'Progressivo Invio']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('formato_trasmissione', ['type' => 'select', 'options' => $formatiTrasmissione, 'label' => 'Formato Trasmissione']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dati Bancari -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="credit-card" style="width:18px;height:18px;"></i>
            Dati Bancari Predefiniti
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('iban_predefinito', ['label' => 'IBAN Predefinito']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('banca_predefinita', ['label' => 'Banca']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Firma Digitale -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="lock" style="width:18px;height:18px;"></i>
            Firma Digitale
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('usa_firma_digitale', ['type' => 'checkbox', 'label' => 'Usa Firma Digitale']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('certificato_path', ['label' => 'Percorso Certificato']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('certificato_password', ['type' => 'password', 'label' => 'Password Certificato', 'value' => '']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sincronizzazione e Stato -->
    <div class="form-card">
        <div class="form-card-body">
            <?php if ($configurazioniSdi->ultima_sincronizzazione): ?>
            <div class="form-row">
                <div class="form-col-half">
                    <label class="form-label">Ultima Sincronizzazione</label>
                    <p class="form-control-static"><?= h($configurazioniSdi->ultima_sincronizzazione->format('d/m/Y H:i:s')) ?></p>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo']) ?>
                </div>
            </div>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->postLink(
                        '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                        ['action' => 'delete', $configurazioniSdi->id],
                        ['confirm' => __('Sei sicuro di voler eliminare questa configurazione SDI?'), 'class' => 'btn btn-danger', 'escapeTitle' => false]
                    ) ?>
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva modifiche'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
