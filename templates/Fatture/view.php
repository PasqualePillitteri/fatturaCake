<?php
/**
 * View Fattura - Layout stile Fattura Elettronica
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fatture $fattura
 */

// Mappature per decodifica codici
$tipiDocumento = [
    'TD01' => 'Fattura',
    'TD02' => 'Acconto/Anticipo fattura',
    'TD03' => 'Acconto/Anticipo parcella',
    'TD04' => 'Nota di credito',
    'TD05' => 'Nota di debito',
    'TD06' => 'Parcella',
    'TD24' => 'Fattura differita',
];

$regimiFiscali = [
    'RF01' => 'Ordinario',
    'RF02' => 'Contribuenti minimi',
    'RF04' => 'Agricoltura',
    'RF05' => 'Pesca',
    'RF06' => 'Vendita sali e tabacchi',
    'RF07' => 'Commercio fiammiferi',
    'RF08' => 'Editoria',
    'RF09' => 'Gestione telefonia',
    'RF10' => 'Rivendita documenti trasporto',
    'RF11' => 'Agenzie viaggi',
    'RF12' => 'Agriturismo',
    'RF13' => 'Vendite a domicilio',
    'RF14' => 'Rivendita beni usati',
    'RF15' => 'Agenzie vendite aste',
    'RF16' => 'IVA per cassa P.A.',
    'RF17' => 'IVA per cassa',
    'RF18' => 'Altro',
    'RF19' => 'Forfettario',
];

$esigibilitaIva = [
    'I' => 'Immediata',
    'D' => 'Differita',
    'S' => 'Split Payment',
];

$modalitaPagamento = [
    'MP01' => 'Contanti',
    'MP02' => 'Assegno',
    'MP03' => 'Assegno circolare',
    'MP04' => 'Contanti c/o Tesoreria',
    'MP05' => 'Bonifico',
    'MP06' => 'Vaglia cambiario',
    'MP07' => 'Bollettino bancario',
    'MP08' => 'Carta di credito',
    'MP09' => 'RID',
    'MP10' => 'RID utenze',
    'MP11' => 'RID veloce',
    'MP12' => 'RIBA',
    'MP13' => 'MAV',
    'MP14' => 'Quietanza erario',
    'MP15' => 'Giroconto',
    'MP16' => 'Domiciliazione bancaria',
    'MP17' => 'Domiciliazione postale',
    'MP18' => 'Bollettino di c/c postale',
    'MP19' => 'SEPA DD',
    'MP20' => 'SEPA DD CORE',
    'MP21' => 'SEPA DD B2B',
    'MP22' => 'Trattenuta su somme',
    'MP23' => 'PagoPA',
];

$tipiRitenuta = [
    'RT01' => 'Ritenuta persone fisiche',
    'RT02' => 'Ritenuta persone giuridiche',
    'RT03' => 'Contributo INPS',
    'RT04' => 'Contributo ENASARCO',
    'RT05' => 'Contributo ENPAM',
    'RT06' => 'Altro contributo prev.',
];

// Helper per formattare valuta
$formatCurrency = function($value) {
    return number_format((float)$value, 2, ',', '.');
};

// Helper per formattare data
$formatDate = function($date) {
    if (!$date) return '-';
    return $date->format('d-m-Y');
};

// Dati cedente (tenant)
$cedente = $fattura->tenant;
// Dati cliente (anagrafica)
$cliente = $fattura->anagrafiche;
?>

<div class="fattura-view-container">
    <!-- Toolbar Azioni -->
    <div class="fattura-toolbar">
        <div class="toolbar-left">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left"></i> Torna alla lista',
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary btn-sm', 'escapeTitle' => false]
            ) ?>
        </div>
        <div class="toolbar-right">
            <?= $this->Html->link(
                '<i data-lucide="edit"></i> Modifica',
                ['action' => 'edit', $fattura->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i data-lucide="printer"></i> Stampa
            </button>
            <?= $this->Html->link(
                '<i data-lucide="file-code"></i> Genera XML',
                ['action' => 'generateXml', $fattura->id],
                ['class' => 'btn btn-outline-success btn-sm', 'escapeTitle' => false]
            ) ?>
            <?php if (!empty($fattura->xml_content)): ?>
                <?= $this->Html->link(
                    '<i data-lucide="download"></i> Download XML',
                    ['action' => 'downloadXml', $fattura->id],
                    ['class' => 'btn btn-outline-primary btn-sm', 'escapeTitle' => false]
                ) ?>
            <?php else: ?>
                <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="XML non ancora generato">
                    <i data-lucide="download"></i> Download XML
                </button>
            <?php endif; ?>
            <?php if ($fattura->direzione === 'emessa' && !in_array($fattura->stato_sdi, ['inviata', 'consegnata', 'accettata'])): ?>
                <?= $this->Form->postLink(
                    '<i data-lucide="send"></i> Invia SDI',
                    ['action' => 'inviaSDI', $fattura->id],
                    [
                        'confirm' => __('ATTENZIONE: Questa è una SIMULAZIONE. La fattura NON verrà realmente trasmessa allo SDI. Vuoi procedere con l\'invio simulato?'),
                        'class' => 'btn btn-outline-info btn-sm',
                        'escapeTitle' => false,
                        'title' => 'Simulazione invio SDI'
                    ]
                ) ?>
            <?php elseif (in_array($fattura->stato_sdi, ['inviata', 'consegnata', 'accettata'])): ?>
                <button type="button" class="btn btn-success btn-sm" disabled title="Fattura già inviata">
                    <i data-lucide="check"></i> Inviata SDI
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Solo per fatture emesse">
                    <i data-lucide="send"></i> Invia SDI
                </button>
            <?php endif; ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2"></i> Elimina',
                ['action' => 'delete', $fattura->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare la fattura {0}?', $fattura->numero),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Stato SDI Badge -->
    <?php
    $statoClass = match($fattura->stato_sdi) {
        'inviata' => 'badge-success',
        'accettata' => 'badge-success',
        'consegnata' => 'badge-success',
        'scartata' => 'badge-danger',
        'rifiutata' => 'badge-danger',
        'in_elaborazione' => 'badge-warning',
        default => 'badge-secondary'
    };
    ?>
    <div class="fattura-stato-banner <?= $statoClass ?>">
        <i data-lucide="<?= $fattura->stato_sdi === 'bozza' ? 'file-edit' : 'send' ?>"></i>
        <span>Stato: <?= h(ucfirst(str_replace('_', ' ', $fattura->stato_sdi))) ?></span>
        <?php if ($fattura->sdi_identificativo): ?>
            <span class="sdi-id">ID SDI: <?= h($fattura->sdi_identificativo) ?></span>
        <?php endif; ?>
    </div>

    <!-- Documento Fattura -->
    <div class="fattura-documento">

        <!-- Header: Cedente e Cessionario -->
        <div class="fattura-header">
            <div class="fattura-soggetto cedente">
                <div class="soggetto-title">Cedente/Prestatore (fornitore)</div>
                <div class="soggetto-content">
                    <?php if ($cedente->partita_iva): ?>
                        <div class="soggetto-row">
                            <span class="label">Identificativo fiscale ai fini IVA:</span>
                            <span class="value">IT<?= h($cedente->partita_iva) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($cedente->codice_fiscale): ?>
                        <div class="soggetto-row">
                            <span class="label">Codice fiscale:</span>
                            <span class="value"><?= h($cedente->codice_fiscale) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="soggetto-row">
                        <span class="label"><?= $cedente->tipo === 'azienda' ? 'Denominazione:' : 'Cognome nome:' ?></span>
                        <span class="value"><?= h($cedente->nome) ?></span>
                    </div>
                    <div class="soggetto-row">
                        <span class="label">Indirizzo:</span>
                        <span class="value"><?= h($cedente->indirizzo) ?></span>
                    </div>
                    <div class="soggetto-row">
                        <span class="label">Comune:</span>
                        <span class="value"><?= h($cedente->citta) ?></span>
                        <span class="label ms-3">Provincia:</span>
                        <span class="value"><?= h($cedente->provincia) ?></span>
                    </div>
                    <div class="soggetto-row">
                        <span class="label">Cap:</span>
                        <span class="value"><?= h($cedente->cap) ?></span>
                        <span class="label ms-3">Nazione:</span>
                        <span class="value">IT</span>
                    </div>
                    <?php if ($cedente->email): ?>
                        <div class="soggetto-row">
                            <span class="label">Email:</span>
                            <span class="value"><?= h($cedente->email) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($cedente->pec): ?>
                        <div class="soggetto-row">
                            <span class="label">PEC:</span>
                            <span class="value"><?= h($cedente->pec) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="fattura-soggetto cessionario">
                <div class="soggetto-title">Cessionario/Committente (cliente)</div>
                <div class="soggetto-content">
                    <?php if ($cliente->partita_iva): ?>
                        <div class="soggetto-row">
                            <span class="label">Identificativo fiscale ai fini IVA:</span>
                            <span class="value">IT<?= h($cliente->partita_iva) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($cliente->codice_fiscale): ?>
                        <div class="soggetto-row">
                            <span class="label">Codice fiscale:</span>
                            <span class="value"><?= h($cliente->codice_fiscale) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="soggetto-row">
                        <span class="label"><?= $cliente->denominazione ? 'Denominazione:' : 'Cognome nome:' ?></span>
                        <span class="value"><?= h($cliente->denominazione ?: $cliente->nome . ' ' . $cliente->cognome) ?></span>
                    </div>
                    <?php if ($cliente->regime_fiscale && $cliente->regime_fiscale !== 'RF01'): ?>
                        <div class="soggetto-row">
                            <span class="label">Regime fiscale:</span>
                            <span class="value"><?= h($cliente->regime_fiscale) ?> <?= $regimiFiscali[$cliente->regime_fiscale] ?? '' ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="soggetto-row">
                        <span class="label">Indirizzo:</span>
                        <span class="value"><?= h($cliente->indirizzo) ?><?= $cliente->numero_civico ? ', ' . h($cliente->numero_civico) : '' ?></span>
                    </div>
                    <div class="soggetto-row">
                        <span class="label">Comune:</span>
                        <span class="value"><?= h($cliente->comune) ?></span>
                        <span class="label ms-3">Provincia:</span>
                        <span class="value"><?= h($cliente->provincia) ?></span>
                    </div>
                    <div class="soggetto-row">
                        <span class="label">Cap:</span>
                        <span class="value"><?= h($cliente->cap) ?></span>
                        <span class="label ms-3">Nazione:</span>
                        <span class="value"><?= h($cliente->nazione) ?></span>
                    </div>
                    <?php if ($cliente->pec): ?>
                        <div class="soggetto-row">
                            <span class="label">PEC:</span>
                            <span class="value"><?= h($cliente->pec) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($cliente->codice_sdi): ?>
                        <div class="soggetto-row">
                            <span class="label">Codice SDI:</span>
                            <span class="value"><?= h($cliente->codice_sdi) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Dati Documento -->
        <div class="fattura-dati-documento">
            <table class="dati-documento-table">
                <thead>
                    <tr>
                        <th>Tipologia documento</th>
                        <th>Art. 73</th>
                        <th>Numero documento</th>
                        <th>Data documento</th>
                        <th>Codice destinatario</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= h($fattura->tipo_documento) ?> <?= $tipiDocumento[$fattura->tipo_documento] ?? '' ?></td>
                        <td>-</td>
                        <td><strong><?= h($fattura->numero) ?></strong></td>
                        <td><?= $formatDate($fattura->data) ?></td>
                        <td><?= $cliente->codice_sdi ?: ($cliente->pec ? 'Indicata PEC' : '0000000') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Righe Fattura -->
        <div class="fattura-righe">
            <table class="righe-table">
                <thead>
                    <tr>
                        <th class="col-codice">Cod. articolo</th>
                        <th class="col-descrizione">Descrizione</th>
                        <th class="col-qta">Quantita</th>
                        <th class="col-prezzo">Prezzo unitario</th>
                        <th class="col-um">UM</th>
                        <th class="col-sconto">Sconto o magg.</th>
                        <th class="col-iva">%IVA</th>
                        <th class="col-totale">Prezzo totale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fattura->fattura_righe)): ?>
                        <?php foreach ($fattura->fattura_righe as $riga): ?>
                        <tr>
                            <td class="col-codice">
                                <?php if ($riga->prodotto): ?>
                                    <?= h($riga->prodotto->codice) ?><br>
                                    <small class="text-muted">(Codice articolo)</small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="col-descrizione"><?= h($riga->descrizione) ?></td>
                            <td class="col-qta text-end"><?= $formatCurrency($riga->quantita) ?></td>
                            <td class="col-prezzo text-end"><?= $formatCurrency($riga->prezzo_unitario) ?></td>
                            <td class="col-um text-center"><?= h($riga->unita_misura) ?: '-' ?></td>
                            <td class="col-sconto text-end">
                                <?php if ($riga->sconto_maggiorazione_percentuale): ?>
                                    <?= $formatCurrency($riga->sconto_maggiorazione_percentuale) ?>%
                                <?php elseif ($riga->sconto_maggiorazione_importo): ?>
                                    <?= $formatCurrency($riga->sconto_maggiorazione_importo) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="col-iva text-end"><?= $formatCurrency($riga->aliquota_iva) ?></td>
                            <td class="col-totale text-end"><strong><?= $formatCurrency($riga->prezzo_totale) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Nessuna riga presente</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Riepilogo IVA e Totali -->
        <div class="fattura-riepilogo">
            <div class="riepilogo-title">RIEPILOGHI IVA E TOTALI</div>
            <table class="riepilogo-table">
                <thead>
                    <tr>
                        <th>esigibilita iva / riferimenti normativi</th>
                        <th class="text-end">%IVA</th>
                        <th class="text-end">Spese accessorie</th>
                        <th class="text-end">Arr.</th>
                        <th class="text-end">Totale imponibile</th>
                        <th class="text-end">Totale imposta</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?= h($fattura->esigibilita_iva) ?>
                            (<?= $esigibilitaIva[$fattura->esigibilita_iva] ?? 'esigibilita' ?>)
                        </td>
                        <td class="text-end">
                            <?php
                            // Calcola aliquota media dalle righe
                            $aliquotaMedia = 22; // default
                            if (!empty($fattura->fattura_righe)) {
                                $aliquote = array_column($fattura->fattura_righe, 'aliquota_iva');
                                $aliquotaMedia = count($aliquote) > 0 ? array_sum($aliquote) / count($aliquote) : 22;
                            }
                            echo $formatCurrency($aliquotaMedia);
                            ?>
                        </td>
                        <td class="text-end">-</td>
                        <td class="text-end">-</td>
                        <td class="text-end"><?= $formatCurrency($fattura->imponibile_totale) ?></td>
                        <td class="text-end"><?= $formatCurrency($fattura->iva_totale) ?></td>
                    </tr>
                </tbody>
            </table>

            <table class="totale-table">
                <tr>
                    <th>Importo bollo</th>
                    <th>Sconto/Maggiorazione</th>
                    <th>Arr.</th>
                    <th class="text-end totale-label">Totale documento</th>
                </tr>
                <tr>
                    <td><?= $fattura->bollo_virtuale ? $formatCurrency($fattura->importo_bollo) : '-' ?></td>
                    <td>
                        <?php if ($fattura->sconto_maggiorazione_importo): ?>
                            <?= $fattura->sconto_maggiorazione_tipo ?> <?= $formatCurrency($fattura->sconto_maggiorazione_importo) ?>
                        <?php elseif ($fattura->sconto_maggiorazione_percentuale): ?>
                            <?= $fattura->sconto_maggiorazione_tipo ?> <?= $formatCurrency($fattura->sconto_maggiorazione_percentuale) ?>%
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>-</td>
                    <td class="text-end totale-value"><?= $formatCurrency($fattura->totale_documento) ?></td>
                </tr>
            </table>
        </div>

        <!-- Dati Ritenuta d'Acconto (se presente) -->
        <?php if ($fattura->ritenuta_acconto && $fattura->ritenuta_acconto > 0): ?>
        <div class="fattura-ritenuta">
            <table class="ritenuta-table">
                <thead>
                    <tr>
                        <th>Dati ritenuta d'acconto</th>
                        <th class="text-end">Aliquota ritenuta</th>
                        <th>Causale</th>
                        <th class="text-end">Importo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= h($fattura->tipo_ritenuta) ?> <?= $tipiRitenuta[$fattura->tipo_ritenuta] ?? '' ?></td>
                        <td class="text-end"><?= $formatCurrency($fattura->aliquota_ritenuta) ?></td>
                        <td><?= h($fattura->causale_pagamento_ritenuta) ?> (decodifica come da modello CU)</td>
                        <td class="text-end"><strong><?= $formatCurrency($fattura->ritenuta_acconto) ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Dati Pagamento -->
        <div class="fattura-pagamento">
            <table class="pagamento-table">
                <thead>
                    <tr>
                        <th>Modalita pagamento</th>
                        <th>Dettagli</th>
                        <th>Scadenze</th>
                        <th class="text-end">Importo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong><?= h($fattura->modalita_pagamento) ?></strong>
                            <?= $modalitaPagamento[$fattura->modalita_pagamento] ?? '' ?>
                        </td>
                        <td>
                            <?php if ($fattura->iban): ?>
                                Beneficiario: <?= h($cedente->nome) ?><br>
                                IBAN: <?= h($fattura->iban) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($fattura->data_scadenza_pagamento): ?>
                                Data scadenza <?= $formatDate($fattura->data_scadenza_pagamento) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <strong>
                            <?php
                            // Importo da pagare = totale - ritenuta
                            $importoPagamento = $fattura->totale_documento - ($fattura->ritenuta_acconto ?? 0);
                            echo $formatCurrency($importoPagamento);
                            ?>
                            </strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Causale -->
        <?php if ($fattura->causale): ?>
        <div class="fattura-causale">
            <div class="causale-title">Causale</div>
            <div class="causale-content"><?= h($fattura->causale) ?></div>
        </div>
        <?php endif; ?>

        <!-- Note -->
        <?php if ($fattura->note): ?>
        <div class="fattura-note">
            <div class="note-title">Note interne</div>
            <div class="note-content"><?= h($fattura->note) ?></div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Storico Stati SDI -->
    <?php if (!empty($fattura->fattura_stati_sdi)): ?>
    <div class="fattura-storico-sdi">
        <div class="storico-title">
            <i data-lucide="history"></i> Storico Stati SDI
        </div>
        <table class="storico-table">
            <thead>
                <tr>
                    <th>Data/Ora</th>
                    <th>Stato</th>
                    <th>ID SDI</th>
                    <th>Messaggio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fattura->fattura_stati_sdi as $stato): ?>
                <tr>
                    <td><?= $stato->data_ora_ricezione ? $stato->data_ora_ricezione->format('d/m/Y H:i') : $stato->created->format('d/m/Y H:i') ?></td>
                    <td><span class="badge bg-secondary"><?= h($stato->stato) ?></span></td>
                    <td><?= h($stato->identificativo_sdi) ?: '-' ?></td>
                    <td><?= h($stato->messaggio) ?: '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Allegati -->
    <?php if (!empty($fattura->fattura_allegati)): ?>
    <div class="fattura-allegati">
        <div class="allegati-title">
            <i data-lucide="paperclip"></i> Allegati
        </div>
        <div class="allegati-list">
            <?php foreach ($fattura->fattura_allegati as $allegato): ?>
            <div class="allegato-item">
                <i data-lucide="file"></i>
                <span><?= h($allegato->nome_attachment) ?></span>
                <small class="text-muted">(<?= h($allegato->formato_attachment) ?>)</small>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Metadati -->
    <div class="fattura-meta">
        <small class="text-muted">
            Creata il <?= $fattura->created->format('d/m/Y H:i') ?>
            <?php if ($fattura->created_by_user): ?>
                da <?= h($fattura->created_by_user->username) ?>
            <?php endif; ?>
            <?php if ($fattura->modified && $fattura->modified != $fattura->created): ?>
                | Modificata il <?= $fattura->modified->format('d/m/Y H:i') ?>
                <?php if ($fattura->modified_by_user): ?>
                    da <?= h($fattura->modified_by_user->username) ?>
                <?php endif; ?>
            <?php endif; ?>
        </small>
    </div>
</div>
