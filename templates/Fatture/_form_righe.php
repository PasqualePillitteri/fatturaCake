<?php
/**
 * Partial per le righe fattura - Layout Tabellare Moderno
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fatture $fatture
 * @var \Cake\Collection\CollectionInterface|string[] $prodotti
 * @var bool $isEditable
 */

// Default: editabile se non specificato (per i form di creazione)
$isEditable = $isEditable ?? true;

$nature = [
    '' => '- Nessuna -',
    'N1' => 'N1 - Escluse ex art.15',
    'N2.1' => 'N2.1 - Non soggette art.7',
    'N2.2' => 'N2.2 - Non soggette altri',
    'N3.1' => 'N3.1 - Non imponibili export',
    'N3.2' => 'N3.2 - Non imponibili cess. intra',
    'N3.3' => 'N3.3 - Non imponibili S.Marino',
    'N3.4' => 'N3.4 - Non imponibili op. ass.',
    'N3.5' => 'N3.5 - Non imponibili lett.c',
    'N3.6' => 'N3.6 - Non imponibili altre',
    'N4' => 'N4 - Esenti',
    'N5' => 'N5 - Regime margine',
    'N6.1' => 'N6.1 - Inv. contabile cess. rot.',
    'N6.2' => 'N6.2 - Inv. contabile cess. cel.',
    'N6.3' => 'N6.3 - Inv. contabile subapp.',
    'N6.4' => 'N6.4 - Inv. contabile cess. fab.',
    'N6.5' => 'N6.5 - Inv. contabile cess. cel.2',
    'N6.6' => 'N6.6 - Inv. contabile prod. elett.',
    'N6.7' => 'N6.7 - Inv. contabile prest. edili',
    'N6.8' => 'N6.8 - Inv. contabile settore ener.',
    'N6.9' => 'N6.9 - Inv. contabile altre',
    'N7' => 'N7 - IVA paesi UE',
];
?>

<!-- Righe Fattura - Layout Tabellare -->
<div class="form-card righe-fattura-card" id="righe-fattura-container">
    <div class="form-card-header">
        <div class="d-flex align-items-center gap-2">
            <i data-lucide="list" style="width:18px;height:18px;"></i>
            <span>Righe Fattura</span>
            <span class="righe-count-badge" id="righe-count">0</span>
        </div>
        <?php if ($isEditable): ?>
        <button type="button" class="btn btn-success btn-sm btn-add-riga" id="btn-add-riga">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
            <span class="d-none d-sm-inline">Aggiungi Riga</span>
        </button>
        <?php endif; ?>
    </div>

    <div class="form-card-body p-0">
        <!-- Tabella Righe Desktop -->
        <div class="righe-table-wrapper">
            <table class="righe-table" id="righe-table">
                <thead>
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-prodotto">Prodotto/Servizio</th>
                        <th class="col-descrizione">Descrizione</th>
                        <th class="col-qta">Q.ta</th>
                        <th class="col-um">U.M.</th>
                        <th class="col-prezzo">Prezzo Unit.</th>
                        <th class="col-iva">IVA %</th>
                        <th class="col-natura">Natura</th>
                        <th class="col-totale">Totale</th>
                        <th class="col-actions"></th>
                    </tr>
                </thead>
                <tbody id="righe-tbody">
                    <?php if (!empty($fatture->fattura_righe)): ?>
                        <?php foreach ($fatture->fattura_righe as $index => $riga): ?>
                        <tr class="riga-row" data-index="<?= $index ?>">
                            <?php if (!$riga->isNew()): ?>
                                <input type="hidden" name="fattura_righe[<?= $index ?>][id]" value="<?= $riga->id ?>">
                            <?php endif; ?>
                            <input type="hidden" name="fattura_righe[<?= $index ?>][numero_linea]" value="<?= $index + 1 ?>" class="numero-linea-input">

                            <td class="col-num">
                                <span class="riga-numero"><?= $index + 1 ?></span>
                            </td>
                            <td class="col-prodotto">
                                <select name="fattura_righe[<?= $index ?>][prodotto_id]" class="form-select form-select-sm prodotto-select">
                                    <option value="">-- Seleziona --</option>
                                    <?php foreach ($prodotti as $id => $nome): ?>
                                    <option value="<?= $id ?>" <?= $riga->prodotto_id == $id ? 'selected' : '' ?>><?= h($nome) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="col-descrizione">
                                <input type="text" name="fattura_righe[<?= $index ?>][descrizione]" value="<?= h($riga->descrizione) ?>" class="form-control form-control-sm descrizione-input" placeholder="Descrizione..." required>
                            </td>
                            <td class="col-qta">
                                <input type="number" name="fattura_righe[<?= $index ?>][quantita]" value="<?= $riga->quantita ?? 1 ?>" step="0.00001" min="0" class="form-control form-control-sm quantita-input text-end">
                            </td>
                            <td class="col-um">
                                <input type="text" name="fattura_righe[<?= $index ?>][unita_misura]" value="<?= h($riga->unita_misura) ?>" class="form-control form-control-sm um-input" placeholder="pz" maxlength="10">
                            </td>
                            <td class="col-prezzo">
                                <input type="number" name="fattura_righe[<?= $index ?>][prezzo_unitario]" value="<?= $riga->prezzo_unitario ?>" step="0.01" min="0" class="form-control form-control-sm prezzo-input text-end" required>
                            </td>
                            <td class="col-iva">
                                <input type="number" name="fattura_righe[<?= $index ?>][aliquota_iva]" value="<?= $riga->aliquota_iva ?? 22 ?>" step="0.01" min="0" max="100" class="form-control form-control-sm iva-input text-end">
                            </td>
                            <td class="col-natura">
                                <select name="fattura_righe[<?= $index ?>][natura]" class="form-select form-select-sm natura-select">
                                    <?php foreach ($nature as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $riga->natura == $key ? 'selected' : '' ?>><?= $key ?: '-' ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="col-totale">
                                <span class="totale-riga-display"><?= number_format($riga->prezzo_totale ?? 0, 2, ',', '.') ?></span>
                                <input type="hidden" name="fattura_righe[<?= $index ?>][prezzo_totale]" value="<?= $riga->prezzo_totale ?? 0 ?>" class="prezzo-totale-input">
                            </td>
                            <td class="col-actions">
                                <?php if ($isEditable): ?>
                                <button type="button" class="btn-remove-riga" title="Rimuovi riga">
                                    <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Empty State -->
            <div class="righe-empty-state" id="righe-empty-state" style="<?= !empty($fatture->fattura_righe) ? 'display:none;' : '' ?>">
                <i data-lucide="file-plus" style="width:48px;height:48px;"></i>
                <p>Nessuna riga inserita</p>
                <?php if ($isEditable): ?>
                <button type="button" class="btn btn-primary btn-sm" id="btn-add-riga-empty">
                    <i data-lucide="plus" style="width:16px;height:16px;"></i>
                    Aggiungi la prima riga
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Riepilogo Totali -->
        <div class="righe-totali-wrapper" id="righe-totali">
            <div class="righe-totali-box">
                <?php if ($fatture->isNew() || ($fatture->stato_sdi ?? 'bozza') === 'bozza'): ?>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-ricalcola" id="btn-ricalcola" title="Ricalcola totali">
                    <i data-lucide="calculator" style="width:14px;height:14px;"></i>
                    <span>Ricalcola</span>
                </button>
                <?php endif; ?>
                <div class="totali-row">
                    <span class="totali-label">Imponibile</span>
                    <span class="totali-value" id="display-imponibile">0,00</span>
                    <span class="totali-currency">EUR</span>
                </div>
                <div class="totali-row totali-iva">
                    <span class="totali-label">IVA</span>
                    <span class="totali-value" id="display-iva">0,00</span>
                    <span class="totali-currency">EUR</span>
                </div>
                <div class="totali-row" id="riga-totale-bollo" style="display:none;">
                    <span class="totali-label">Bollo</span>
                    <span class="totali-value" id="display-bollo">0,00</span>
                    <span class="totali-currency">EUR</span>
                </div>
                <div class="totali-divider"></div>
                <div class="totali-row totali-finale">
                    <span class="totali-label">TOTALE</span>
                    <span class="totali-value" id="display-totale">0,00</span>
                    <span class="totali-currency">EUR</span>
                </div>
                <div class="totali-row totali-ritenuta" id="riga-totale-ritenuta" style="display:none;">
                    <span class="totali-label">- Ritenuta d'acconto</span>
                    <span class="totali-value" id="display-ritenuta">0,00</span>
                    <span class="totali-currency">EUR</span>
                </div>
                <div class="totali-row totali-netto" id="riga-netto-pagare" style="display:none;">
                    <span class="totali-label"><strong>NETTO A PAGARE</strong></span>
                    <span class="totali-value" id="display-netto"><strong>0,00</strong></span>
                    <span class="totali-currency">EUR</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template per nuova riga -->
<template id="riga-template">
    <tr class="riga-row riga-new" data-index="__INDEX__">
        <input type="hidden" name="fattura_righe[__INDEX__][numero_linea]" value="__NUM__" class="numero-linea-input">

        <td class="col-num">
            <span class="riga-numero">__NUM__</span>
        </td>
        <td class="col-prodotto">
            <select name="fattura_righe[__INDEX__][prodotto_id]" class="form-select form-select-sm prodotto-select">
                <option value="">-- Seleziona --</option>
                <?php foreach ($prodotti as $id => $nome): ?>
                <option value="<?= $id ?>"><?= h($nome) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="col-descrizione">
            <input type="text" name="fattura_righe[__INDEX__][descrizione]" class="form-control form-control-sm descrizione-input" placeholder="Descrizione..." required>
        </td>
        <td class="col-qta">
            <input type="number" name="fattura_righe[__INDEX__][quantita]" value="1" step="0.00001" min="0" class="form-control form-control-sm quantita-input text-end">
        </td>
        <td class="col-um">
            <input type="text" name="fattura_righe[__INDEX__][unita_misura]" class="form-control form-control-sm um-input" placeholder="pz" maxlength="10">
        </td>
        <td class="col-prezzo">
            <input type="number" name="fattura_righe[__INDEX__][prezzo_unitario]" value="0" step="0.01" min="0" class="form-control form-control-sm prezzo-input text-end" required>
        </td>
        <td class="col-iva">
            <input type="number" name="fattura_righe[__INDEX__][aliquota_iva]" value="22" step="0.01" min="0" max="100" class="form-control form-control-sm iva-input text-end">
        </td>
        <td class="col-natura">
            <select name="fattura_righe[__INDEX__][natura]" class="form-select form-select-sm natura-select">
                <?php foreach ($nature as $key => $label): ?>
                <option value="<?= $key ?>"><?= $key ?: '-' ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="col-totale">
            <span class="totale-riga-display">0,00</span>
            <input type="hidden" name="fattura_righe[__INDEX__][prezzo_totale]" value="0" class="prezzo-totale-input">
        </td>
        <td class="col-actions">
            <button type="button" class="btn-remove-riga" title="Rimuovi riga">
                <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
            </button>
        </td>
    </tr>
</template>

<!-- Template per riga mobile -->
<template id="riga-mobile-template">
    <div class="riga-mobile-card" data-index="__INDEX__">
        <input type="hidden" name="fattura_righe[__INDEX__][numero_linea]" value="__NUM__" class="numero-linea-input">

        <div class="riga-mobile-header">
            <span class="riga-mobile-num">#__NUM__</span>
            <button type="button" class="btn-remove-riga" title="Rimuovi">
                <i data-lucide="trash-2" style="width:18px;height:18px;"></i>
            </button>
        </div>

        <div class="riga-mobile-body">
            <div class="riga-mobile-field full">
                <label>Prodotto</label>
                <select name="fattura_righe[__INDEX__][prodotto_id]" class="form-select form-select-sm prodotto-select">
                    <option value="">-- Seleziona --</option>
                    <?php foreach ($prodotti as $id => $nome): ?>
                    <option value="<?= $id ?>"><?= h($nome) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="riga-mobile-field full">
                <label>Descrizione</label>
                <input type="text" name="fattura_righe[__INDEX__][descrizione]" class="form-control form-control-sm descrizione-input" placeholder="Descrizione..." required>
            </div>
            <div class="riga-mobile-row">
                <div class="riga-mobile-field">
                    <label>Q.ta</label>
                    <input type="number" name="fattura_righe[__INDEX__][quantita]" value="1" step="0.00001" class="form-control form-control-sm quantita-input text-end">
                </div>
                <div class="riga-mobile-field">
                    <label>U.M.</label>
                    <input type="text" name="fattura_righe[__INDEX__][unita_misura]" class="form-control form-control-sm um-input" placeholder="pz">
                </div>
                <div class="riga-mobile-field">
                    <label>Prezzo</label>
                    <input type="number" name="fattura_righe[__INDEX__][prezzo_unitario]" value="0" step="0.01" class="form-control form-control-sm prezzo-input text-end" required>
                </div>
            </div>
            <div class="riga-mobile-row">
                <div class="riga-mobile-field">
                    <label>IVA %</label>
                    <input type="number" name="fattura_righe[__INDEX__][aliquota_iva]" value="22" step="0.01" class="form-control form-control-sm iva-input text-end">
                </div>
                <div class="riga-mobile-field">
                    <label>Natura</label>
                    <select name="fattura_righe[__INDEX__][natura]" class="form-select form-select-sm natura-select">
                        <?php foreach ($nature as $key => $label): ?>
                        <option value="<?= $key ?>"><?= $key ?: '-' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="riga-mobile-footer">
            <span class="riga-mobile-totale-label">Totale riga:</span>
            <span class="totale-riga-display">0,00 EUR</span>
            <input type="hidden" name="fattura_righe[__INDEX__][prezzo_totale]" value="0" class="prezzo-totale-input">
        </div>
    </div>
</template>
