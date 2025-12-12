<?php
/**
 * Import Fatture XML - Preview Page
 *
 * @var \App\View\AppView $this
 * @var string $filename
 * @var array $preview
 * @var bool $hasErrors
 */
?>
<div class="import-preview-xml content">
    <div class="page-header">
        <h3><i data-lucide="eye" style="width:24px;height:24px;"></i> <?= __('Anteprima Import XML') ?></h3>
    </div>

    <!-- File Info -->
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i data-lucide="file-code" style="width:20px;height:20px;" class="me-2"></i>
        <div>
            <strong>File:</strong> <?= h($filename) ?>
            <span class="ms-3"><strong>Fatture trovate:</strong> <?= $preview['totals']['fatture'] ?></span>
            <span class="ms-3"><strong>Righe totali:</strong> <?= $preview['totals']['righe'] ?></span>
        </div>
    </div>

    <?php if (!empty($preview['errors'])): ?>
    <div class="alert alert-danger">
        <h6 class="alert-heading"><i data-lucide="alert-circle" style="width:16px;height:16px;"></i> Errori di validazione</h6>
        <ul class="mb-0">
            <?php foreach ($preview['errors'] as $error): ?>
            <li><?= h($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($preview['warnings'])): ?>
    <div class="alert alert-warning">
        <h6 class="alert-heading"><i data-lucide="alert-triangle" style="width:16px;height:16px;"></i> Avvisi</h6>
        <ul class="mb-0">
            <?php foreach ($preview['warnings'] as $warning): ?>
            <li><?= h($warning) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Fatture Preview -->
    <div class="form-card mb-4">
        <div class="form-card-header">
            <i data-lucide="file-text" style="width:18px;height:18px;"></i>
            Fatture da Importare
        </div>
        <div class="form-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Numero</th>
                            <th>Data</th>
                            <th>Tipo Doc</th>
                            <th>Cedente</th>
                            <th>Cessionario</th>
                            <th class="text-end">Imponibile</th>
                            <th class="text-end">IVA</th>
                            <th class="text-end">Totale</th>
                            <th>Righe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($preview['fatture'] as $fattura): ?>
                        <tr>
                            <td>
                                <small class="text-muted"><?= h($fattura['_source_file'] ?? '-') ?></small>
                            </td>
                            <td><strong><?= h($fattura['numero']) ?></strong></td>
                            <td><?= h($fattura['data_emissione']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= h($fattura['tipo_documento'] ?? 'TD01') ?></span>
                            </td>
                            <td>
                                <small>
                                    <?= h($fattura['cedente']['denominazione'] ?? '-') ?>
                                    <?php if (!empty($fattura['cedente']['partita_iva'])): ?>
                                    <br><span class="text-muted">P.IVA: <?= h($fattura['cedente']['partita_iva']) ?></span>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <small>
                                    <?= h($fattura['cessionario']['denominazione'] ?? '-') ?>
                                    <?php if (!empty($fattura['cessionario']['partita_iva'])): ?>
                                    <br><span class="text-muted">P.IVA: <?= h($fattura['cessionario']['partita_iva']) ?></span>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td class="text-end"><?= $this->Number->currency($fattura['imponibile_totale'] ?? 0, 'EUR') ?></td>
                            <td class="text-end"><?= $this->Number->currency($fattura['imposta_totale'] ?? 0, 'EUR') ?></td>
                            <td class="text-end"><strong><?= $this->Number->currency($fattura['importo_totale'] ?? 0, 'EUR') ?></strong></td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= count($fattura['righe'] ?? []) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Options & Execute -->
    <?php if (!$hasErrors): ?>
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="settings" style="width:18px;height:18px;"></i>
            Opzioni Import
        </div>
        <div class="form-card-body">
            <?= $this->Form->create(null, ['url' => ['action' => 'executeXml']]) ?>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="create_anagrafiche" name="create_anagrafiche" value="1" checked>
                        <label class="form-check-label" for="create_anagrafiche">
                            Crea anagrafiche mancanti
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1" checked>
                        <label class="form-check-label" for="skip_duplicates">
                            Salta fatture duplicate
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tipo fattura (default)</label>
                    <select name="tipo_default" class="form-select">
                        <option value="ricevuta">Ricevute (acquisto)</option>
                        <option value="emessa">Emesse (vendita)</option>
                    </select>
                    <small class="text-muted">Auto-detect in base a P.IVA tenant</small>
                </div>
            </div>

            <div class="d-flex gap-2">
                <?= $this->Form->button(
                    '<i data-lucide="download" style="width:16px;height:16px;"></i> ' . __('Importa Fatture'),
                    ['class' => 'btn btn-success btn-lg', 'escapeTitle' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i data-lucide="x" style="width:16px;height:16px;"></i> ' . __('Annulla'),
                    ['action' => 'fattureXml'],
                    ['class' => 'btn btn-outline-secondary btn-lg', 'escapeTitle' => false]
                ) ?>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
    <?php else: ?>
    <div class="d-flex gap-2">
        <?= $this->Html->link(
            '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna indietro'),
            ['action' => 'fattureXml'],
            ['class' => 'btn btn-secondary btn-lg', 'escapeTitle' => false]
        ) ?>
    </div>
    <?php endif; ?>
</div>
