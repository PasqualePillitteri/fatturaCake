<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fatture $fatture
 * @var string[]|\Cake\Collection\CollectionInterface $tenants
 * @var string[]|\Cake\Collection\CollectionInterface $anagrafiche
 * @var string[]|\Cake\Collection\CollectionInterface $prodotti
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

$direzioni = ['emessa' => 'Emessa (vendita)', 'ricevuta' => 'Ricevuta (acquisto)'];
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
$statiSdi = [
    'bozza' => 'Bozza',
    'generata' => 'XML Generato',
    'inviata' => 'Inviata a SDI',
    'consegnata' => 'Consegnata',
    'accettata' => 'Accettata',
    'rifiutata' => 'Rifiutata',
    'scartata' => 'Scartata',
    'mancata_consegna' => 'Mancata consegna',
];

// La fattura è modificabile solo se è in bozza
$isEditable = ($fatture->stato_sdi ?? 'bozza') === 'bozza';
$readonlyAttr = $isEditable ? [] : ['readonly' => true, 'disabled' => true];
?>
<div class="fatture form content form-content">
    <div class="page-header">
        <h3><?= __('Modifica Fattura') ?> #<?= h($fatture->numero) ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="eye" style="width:16px;height:16px;"></i> ' . __('Visualizza'),
                ['action' => 'view', $fatture->id],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?php if (!$isEditable): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i data-lucide="lock" style="width:20px;height:20px;" class="me-2"></i>
        <div>
            <strong>Fattura non modificabile</strong> - La fattura e stata <?= h($statiSdi[$fatture->stato_sdi] ?? $fatture->stato_sdi) ?>.
            Per modificarla, e necessario prima riportarla in stato "Bozza".
        </div>
    </div>
    <?php endif; ?>

    <?= $this->Form->create($fatture) ?>
    <?php
    // Unlock dynamic fields for fattura_righe (added via JavaScript)
    $this->Form->unlockField('fattura_righe');
    ?>

    <fieldset <?= $isEditable ? '' : 'disabled' ?>>

    <!-- Dati Documento -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="file-text" style="width:18px;height:18px;"></i>
            Dati Documento
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('tipo_documento', ['type' => 'select', 'options' => $tipiDocumento, 'label' => 'Tipo Documento']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('direzione', ['type' => 'select', 'options' => $direzioni, 'label' => 'Direzione']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('numero', ['label' => ['text' => 'Numero', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('data', ['type' => 'date', 'label' => ['text' => 'Data', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('anno', ['type' => 'number', 'label' => 'Anno']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('anagrafica_id', ['options' => $anagrafiche, 'empty' => '-- Seleziona --', 'label' => ['text' => 'Cliente/Fornitore', 'class' => 'required']]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Righe Fattura -->
    <?= $this->element('../Fatture/_form_righe', ['fatture' => $fatture, 'prodotti' => $prodotti, 'isEditable' => $isEditable]) ?>

    <!-- Importi -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="euro" style="width:18px;height:18px;"></i>
            Importi (calcolati automaticamente)
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <?= $this->Form->control('imponibile_totale', ['type' => 'number', 'step' => '0.01', 'label' => 'Imponibile Totale', 'id' => 'imponibile-totale', 'readonly' => true]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('iva_totale', ['type' => 'number', 'step' => '0.01', 'label' => 'IVA Totale', 'id' => 'iva-totale', 'readonly' => true]) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('totale_documento', ['type' => 'number', 'step' => '0.01', 'label' => ['text' => 'Totale Documento', 'class' => 'required'], 'id' => 'totale-documento', 'readonly' => true]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('divisa', ['label' => 'Divisa', 'maxlength' => 3]) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('esigibilita_iva', ['type' => 'select', 'options' => $esigibilitaIva, 'label' => 'Esigibilita IVA']) ?>
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
                    <?= $this->Form->control('condizioni_pagamento', ['type' => 'select', 'options' => $condizioniPagamento, 'label' => 'Condizioni Pagamento']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('modalita_pagamento', ['type' => 'select', 'options' => $modalitaPagamento, 'label' => 'Modalita Pagamento']) ?>
                </div>
                <div class="form-col-third">
                    <?= $this->Form->control('data_scadenza_pagamento', ['type' => 'date', 'label' => 'Scadenza Pagamento']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('iban', ['label' => 'IBAN']) ?>
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
            <div id="ritenuta-fields" style="<?= !empty($fatture->aliquota_ritenuta) ? '' : 'display:none;' ?>">
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
                            'id' => 'aliquota-ritenuta'
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
                        'id' => 'importo-bollo'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stato SDI (read-only - managed by system) -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i>
            Stato Fatturazione Elettronica (sola lettura)
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-third">
                    <label>Stato SDI</label>
                    <div class="form-control-plaintext"><?= h($statiSdi[$fatture->stato_sdi] ?? $fatture->stato_sdi ?? '-') ?></div>
                </div>
                <div class="form-col-third">
                    <label>Identificativo SDI</label>
                    <div class="form-control-plaintext"><?= h($fatture->sdi_identificativo ?? '-') ?></div>
                </div>
                <div class="form-col-third">
                    <label>Data Ricezione SDI</label>
                    <div class="form-control-plaintext"><?= $fatture->sdi_data_ricezione ? $fatture->sdi_data_ricezione->format('d/m/Y') : '-' ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label>Nome File XML</label>
                    <div class="form-control-plaintext"><?= h($fatture->nome_file ?? '-') ?></div>
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
            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                </div>
                <?php if ($isEditable): ?>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva modifiche'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    </fieldset>

    <?= $this->Form->end() ?>

    <?php if ($isEditable): ?>
    <!-- Delete button outside the edit form to avoid FormProtection conflicts -->
    <div class="mt-3">
        <?= $this->Form->postLink(
            '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina fattura'),
            ['action' => 'delete', $fatture->id],
            ['confirm' => __('Sei sicuro di voler eliminare la fattura #{0}?', $fatture->numero), 'class' => 'btn btn-danger', 'escapeTitle' => false]
        ) ?>
    </div>
    <?php endif; ?>
</div>

<?= $this->Html->script('fattura-righe', ['block' => true]) ?>
