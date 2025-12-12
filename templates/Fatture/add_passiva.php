<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fatture $fatture
 * @var \Cake\Collection\CollectionInterface|string[] $anagrafiche
 * @var \Cake\Collection\CollectionInterface|string[] $prodotti
 */

$tipiDocumento = [
    'TD01' => 'TD01 - Fattura',
    'TD02' => 'TD02 - Acconto/Anticipo fattura',
    'TD03' => 'TD03 - Acconto/Anticipo parcella',
    'TD04' => 'TD04 - Nota di credito',
    'TD05' => 'TD05 - Nota di debito',
    'TD06' => 'TD06 - Parcella',
    'TD24' => 'TD24 - Fattura differita',
];

$esigibilitaIva = ['I' => 'I - Immediata', 'D' => 'D - Differita', 'S' => 'S - Split Payment'];
$modalitaPagamento = [
    'MP01' => 'MP01 - Contanti',
    'MP02' => 'MP02 - Assegno',
    'MP05' => 'MP05 - Bonifico',
    'MP08' => 'MP08 - Carta di credito',
    'MP12' => 'MP12 - RIBA',
];
$condizioniPagamento = [
    'TP01' => 'TP01 - Pagamento a rate',
    'TP02' => 'TP02 - Pagamento completo',
    'TP03' => 'TP03 - Anticipo',
];
?>
<div class="fatture form content form-content">
    <div class="page-header">
        <h3><?= __('Nuova Fattura Passiva (Ricevuta)') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'indexPassive'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($fatture) ?>
    <?= $this->Form->hidden('direzione', ['value' => 'ricevuta']) ?>
    <?php
    // Unlock dynamic fields for fattura_righe (added via JavaScript)
    $this->Form->unlockField('fattura_righe');
    ?>

    <!-- Dati Documento -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="file-text" style="width:18px;height:18px;"></i>
            Dati Documento
            <span class="badge bg-info ms-2">Fattura Ricevuta</span>
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('tipo_documento', ['type' => 'select', 'options' => $tipiDocumento, 'label' => 'Tipo Documento', 'default' => 'TD01']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('numero', ['label' => ['text' => 'Numero', 'class' => 'required'], 'placeholder' => 'Numero fattura fornitore']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('data', ['type' => 'date', 'label' => ['text' => 'Data', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('anno', ['type' => 'number', 'label' => 'Anno', 'default' => date('Y')]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('anagrafica_id', ['options' => $anagrafiche, 'empty' => '-- Seleziona fornitore --', 'label' => ['text' => 'Fornitore', 'class' => 'required']]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Righe Fattura -->
    <?= $this->element('../Fatture/_form_righe', ['fatture' => $fatture, 'prodotti' => $prodotti]) ?>

    <!-- Importi -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="euro" style="width:18px;height:18px;"></i>
            Importi (calcolati automaticamente)
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('imponibile_totale', ['type' => 'number', 'step' => '0.01', 'label' => 'Imponibile Totale', 'placeholder' => '0.00', 'id' => 'imponibile-totale', 'readonly' => true]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('iva_totale', ['type' => 'number', 'step' => '0.01', 'label' => 'IVA Totale', 'placeholder' => '0.00', 'id' => 'iva-totale', 'readonly' => true]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('totale_documento', ['type' => 'number', 'step' => '0.01', 'label' => ['text' => 'Totale Documento', 'class' => 'required'], 'placeholder' => '0.00', 'id' => 'totale-documento', 'readonly' => true]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('divisa', ['label' => 'Divisa', 'default' => 'EUR', 'maxlength' => 3]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('esigibilita_iva', ['type' => 'select', 'options' => $esigibilitaIva, 'label' => 'Esigibilita IVA', 'default' => 'I']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagamento -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="credit-card" style="width:18px;height:18px;"></i>
            Dati Pagamento
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('condizioni_pagamento', ['type' => 'select', 'options' => $condizioniPagamento, 'label' => 'Condizioni Pagamento', 'default' => 'TP02']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('modalita_pagamento', ['type' => 'select', 'options' => $modalitaPagamento, 'label' => 'Modalita Pagamento', 'default' => 'MP05']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('data_scadenza_pagamento', ['type' => 'date', 'label' => 'Scadenza Pagamento']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('iban', ['label' => 'IBAN Fornitore', 'placeholder' => 'IT...']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Ritenuta e Bollo -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="shield" style="width:18px;height:18px;"></i>
            Ritenuta e Bollo
        </div>
        <div class="form-card-body">
            <!-- Toggle Ritenuta -->
            <div class="form-row">
                <div class="form-col">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="ritenuta-enabled" name="ritenuta_enabled" <?= !empty($fatture->aliquota_ritenuta) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ritenuta-enabled">
                            <strong>Applica Ritenuta d'Acconto</strong>
                            <small class="text-muted d-block">Attiva per professionisti e prestatori di servizi</small>
                        </label>
                    </div>
                </div>
            </div>
            <!-- Campi Ritenuta (nascosti di default) -->
            <div id="ritenuta-fields" style="display:none;">
                <div class="form-row">
                    <div class="form-col-third">
                        <?php
                        $tipiRitenuta = [
                            'RT01' => 'RT01 - Ritenuta persone fisiche',
                            'RT02' => 'RT02 - Ritenuta persone giuridiche',
                            'RT03' => 'RT03 - Contributo INPS',
                            'RT04' => 'RT04 - Contributo ENASARCO',
                            'RT05' => 'RT05 - Contributo ENPAM',
                            'RT06' => 'RT06 - Altro contributo previdenziale',
                        ];
                        ?>
                        <?= $this->Form->control('tipo_ritenuta', [
                            'type' => 'select',
                            'options' => $tipiRitenuta,
                            'empty' => '-- Seleziona tipo --',
                            'label' => 'Tipo Ritenuta',
                            'id' => 'tipo-ritenuta'
                        ]) ?>
                    </div>
                    <div class="form-col-third">
                        <?= $this->Form->control('aliquota_ritenuta', [
                            'type' => 'number',
                            'step' => '0.01',
                            'label' => 'Aliquota Ritenuta %',
                            'id' => 'aliquota-ritenuta',
                            'default' => '20'
                        ]) ?>
                    </div>
                    <div class="form-col-third">
                        <?= $this->Form->control('ritenuta_acconto', [
                            'type' => 'number',
                            'step' => '0.01',
                            'label' => 'Importo Ritenuta (calcolato)',
                            'id' => 'ritenuta-acconto',
                            'readonly' => true
                        ]) ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col">
                        <?php
                        $causalePagamentoRitenuta = [
                            'A' => 'A - Prestazioni di lavoro autonomo',
                            'B' => 'B - Utilizzazione di opere dell\'ingegno',
                            'C' => 'C - Utili da contratti di associazione',
                            'D' => 'D - Utili da contratti di cointeressenza',
                            'E' => 'E - Indennita\' per cessazione rapporto agenzia',
                            'G' => 'G - Indennita\' per cessazione funzioni notarili',
                            'H' => 'H - Indennita\' per cessazione rapporto sportivo',
                            'I' => 'I - Indennita\' per cessazione rapporto collaborazione coordinata',
                            'L' => 'L - Utilizzazione di opere dell\'ingegno (diverso da B)',
                            'M' => 'M - Prestazioni di lavoro autonomo non abituale',
                            'N' => 'N - Indennita\' per cessazione di attivita\' sportiva',
                            'O' => 'O - Indennita\' per cessazione co.co.co. amministratori',
                            'P' => 'P - Compensi per attivita\' libero professionali',
                            'Q' => 'Q - Provvigioni per rappresentante',
                            'R' => 'R - Provvigioni per vendita porta a porta',
                            'S' => 'S - Provvigioni per procacciatore d\'affari',
                            'T' => 'T - Provvigioni per commissario',
                            'U' => 'U - Provvigioni per mediatore',
                            'V' => 'V - Provvigioni per incaricati vendite dirette',
                            'Z' => 'Z - Titolo diverso dai precedenti',
                        ];
                        ?>
                        <?= $this->Form->control('causale_pagamento_ritenuta', [
                            'type' => 'select',
                            'options' => $causalePagamentoRitenuta,
                            'empty' => '-- Seleziona causale --',
                            'label' => 'Causale Pagamento Ritenuta'
                        ]) ?>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <!-- Bollo -->
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('bollo_virtuale', [
                        'type' => 'checkbox',
                        'label' => 'Bollo Virtuale',
                        'id' => 'bollo-virtuale'
                    ]) ?>
                    <small class="text-muted">Obbligatorio per importi > 77,47 EUR esenti IVA</small>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('importo_bollo', [
                        'type' => 'number',
                        'step' => '0.01',
                        'label' => 'Importo Bollo',
                        'default' => '2.00',
                        'id' => 'importo-bollo'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <div class="form-card">
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('causale', ['type' => 'textarea', 'rows' => 2, 'label' => 'Causale']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('note', ['type' => 'textarea', 'rows' => 2, 'label' => 'Note interne']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo', 'default' => true]) ?>
                </div>
            </div>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'indexPassive'], ['class' => 'btn btn-secondary']) ?>
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

<?= $this->Html->script('fattura-righe', ['block' => true]) ?>
