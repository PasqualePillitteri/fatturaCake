<?php
/**
 * Import Preview Page
 *
 * @var \App\View\AppView $this
 * @var string $filename
 * @var array $preview
 * @var bool $hasErrors
 */
?>
<div class="import-preview content">
    <div class="page-header">
        <h3><i data-lucide="eye" style="width:24px;height:24px;"></i> <?= __('Anteprima Import') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Indietro'),
                ['action' => 'fatture'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <!-- Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0 text-primary"><?= $preview['totals']['fatture'] ?></div>
                    <small class="text-muted">Fatture</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0 text-info"><?= $preview['totals']['righe'] ?></div>
                    <small class="text-muted">Righe</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0 text-danger"><?= count($preview['errors']) ?></div>
                    <small class="text-muted">Errori</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0 text-warning"><?= count($preview['warnings']) ?></div>
                    <small class="text-muted">Avvisi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Errors -->
    <?php if (!empty($preview['errors'])): ?>
        <div class="alert alert-danger">
            <h5 class="alert-heading">
                <i data-lucide="alert-circle" style="width:18px;height:18px;"></i>
                Errori di Validazione
            </h5>
            <ul class="mb-0">
                <?php foreach ($preview['errors'] as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Warnings -->
    <?php if (!empty($preview['warnings'])): ?>
        <div class="alert alert-warning">
            <h5 class="alert-heading">
                <i data-lucide="alert-triangle" style="width:18px;height:18px;"></i>
                Avvisi
            </h5>
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
            Fatture (prime <?= count($preview['fatture']) ?> di <?= $preview['totals']['fatture'] ?>)
        </div>
        <div class="form-card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Cliente</th>
                            <th>P.IVA/CF</th>
                            <th class="text-end">Totale</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($preview['fatture'] as $fattura): ?>
                            <tr>
                                <td><strong><?= h($fattura['numero'] ?? '') ?></strong></td>
                                <td><?= h($fattura['data_emissione'] ?? '') ?></td>
                                <td><span class="badge bg-secondary"><?= h($fattura['tipo_documento'] ?? '') ?></span></td>
                                <td><?= h($fattura['cliente_denominazione'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($fattura['cliente_piva'] ?? '')): ?>
                                        <?= h($fattura['cliente_piva']) ?>
                                    <?php elseif (!empty($fattura['cliente_cf'] ?? '')): ?>
                                        <?= h($fattura['cliente_cf']) ?>
                                    <?php else: ?>
                                        <span class="text-danger">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?= $this->Number->currency($fattura['importo_totale'] ?? 0, 'EUR') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Righe Preview -->
    <div class="form-card mb-4">
        <div class="form-card-header">
            <i data-lucide="list" style="width:18px;height:18px;"></i>
            Righe (prime <?= count($preview['righe']) ?> di <?= $preview['totals']['righe'] ?>)
        </div>
        <div class="form-card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Fattura</th>
                            <th>#</th>
                            <th>Codice</th>
                            <th>Descrizione</th>
                            <th class="text-end">Qta</th>
                            <th class="text-end">Prezzo</th>
                            <th class="text-end">IVA %</th>
                            <th class="text-end">Importo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($preview['righe'] as $riga): ?>
                            <tr>
                                <td><?= h($riga['numero_fattura'] ?? '') ?></td>
                                <td><?= h($riga['numero_riga'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($riga['prodotto_codice'] ?? '')): ?>
                                        <code><?= h($riga['prodotto_codice']) ?></code>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= h(\Cake\Utility\Text::truncate($riga['descrizione'] ?? '', 40)) ?></td>
                                <td class="text-end"><?= h($riga['quantita'] ?? '') ?></td>
                                <td class="text-end"><?= $this->Number->currency($riga['prezzo_unitario'] ?? 0, 'EUR') ?></td>
                                <td class="text-end"><?= h($riga['aliquota_iva'] ?? '') ?>%</td>
                                <td class="text-end"><?= $this->Number->currency($riga['importo_riga'] ?? 0, 'EUR') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Form -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="settings" style="width:18px;height:18px;"></i>
            Opzioni Import
        </div>
        <div class="form-card-body">
            <?= $this->Form->create(null, ['url' => ['action' => 'execute']]) ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="create_anagrafiche"
                               id="create_anagrafiche" value="1" checked>
                        <label class="form-check-label" for="create_anagrafiche">
                            Crea anagrafiche mancanti
                        </label>
                        <div class="form-text">Se un cliente non esiste, verra creato automaticamente</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="create_prodotti"
                               id="create_prodotti" value="1" checked>
                        <label class="form-check-label" for="create_prodotti">
                            Crea prodotti mancanti
                        </label>
                        <div class="form-text">Se un codice prodotto non esiste, verra creato</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="skip_errors"
                               id="skip_errors" value="1" <?= $hasErrors ? '' : '' ?>>
                        <label class="form-check-label" for="skip_errors">
                            Ignora righe con errori
                        </label>
                        <div class="form-text">Importa comunque le righe valide</div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>File:</strong> <?= h($filename) ?>
                </div>
                <div class="d-flex gap-2">
                    <?= $this->Html->link(
                        '<i data-lucide="x" style="width:16px;height:16px;"></i> Annulla',
                        ['action' => 'fatture'],
                        ['class' => 'btn btn-secondary', 'escapeTitle' => false]
                    ) ?>
                    <?php if (!$hasErrors): ?>
                        <?= $this->Form->button(
                            '<i data-lucide="upload" style="width:16px;height:16px;"></i> Esegui Import',
                            ['class' => 'btn btn-success btn-lg', 'escapeTitle' => false]
                        ) ?>
                    <?php else: ?>
                        <?= $this->Form->button(
                            '<i data-lucide="alert-triangle" style="width:16px;height:16px;"></i> Esegui Import (con errori)',
                            [
                                'class' => 'btn btn-warning btn-lg',
                                'escapeTitle' => false,
                                'onclick' => "return confirm('Ci sono errori di validazione. Vuoi procedere comunque?');"
                            ]
                        ) ?>
                    <?php endif; ?>
                </div>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
