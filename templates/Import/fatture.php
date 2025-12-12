<?php
/**
 * Import Fatture - Upload Page
 *
 * @var \App\View\AppView $this
 */
?>
<div class="import-fatture content">
    <div class="page-header">
        <h3><i data-lucide="upload" style="width:24px;height:24px;"></i> <?= __('Import Fatture da Excel') ?></h3>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Upload Form -->
            <div class="form-card">
                <div class="form-card-header">
                    <i data-lucide="file-spreadsheet" style="width:18px;height:18px;"></i>
                    Carica File Excel
                </div>
                <div class="form-card-body">
                    <?= $this->Form->create(null, [
                        'url' => ['action' => 'preview'],
                        'type' => 'file',
                    ]) ?>

                    <div class="mb-4">
                        <label class="form-label required">File Excel (.xlsx)</label>
                        <input type="file" name="excel_file" class="form-control form-control-lg"
                               accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Seleziona un file Excel compilato secondo il template.
                            Formati supportati: .xlsx, .xls
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <?= $this->Form->button(
                            '<i data-lucide="eye" style="width:16px;height:16px;"></i> ' . __('Anteprima'),
                            ['class' => 'btn btn-primary btn-lg', 'escapeTitle' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i data-lucide="file-code" style="width:16px;height:16px;"></i> ' . __('Import da XML/ZIP'),
                            ['action' => 'fattureXml'],
                            ['class' => 'btn btn-outline-secondary btn-lg', 'escapeTitle' => false]
                        ) ?>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Download Template -->
            <div class="form-card">
                <div class="form-card-header">
                    <i data-lucide="download" style="width:18px;height:18px;"></i>
                    Template
                </div>
                <div class="form-card-body">
                    <p class="mb-3">
                        Scarica il template Excel preformattato per l'import delle fatture.
                    </p>
                    <?= $this->Html->link(
                        '<i data-lucide="file-down" style="width:16px;height:16px;"></i> Scarica Template',
                        ['action' => 'downloadTemplate'],
                        ['class' => 'btn btn-success w-100', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>

            <!-- Instructions -->
            <div class="form-card mt-3">
                <div class="form-card-header">
                    <i data-lucide="info" style="width:18px;height:18px;"></i>
                    Istruzioni
                </div>
                <div class="form-card-body">
                    <ol class="mb-0 ps-3">
                        <li class="mb-2">Scarica il template Excel</li>
                        <li class="mb-2">Compila il foglio <strong>Fatture</strong> con i dati delle fatture</li>
                        <li class="mb-2">Compila il foglio <strong>Righe</strong> con le righe di ogni fattura</li>
                        <li class="mb-2">Carica il file compilato</li>
                        <li class="mb-2">Verifica l'anteprima e conferma</li>
                    </ol>
                </div>
            </div>

            <!-- Features -->
            <div class="form-card mt-3">
                <div class="form-card-header">
                    <i data-lucide="sparkles" style="width:18px;height:18px;"></i>
                    Funzionalita
                </div>
                <div class="form-card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Creazione automatica clienti
                        </li>
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Creazione automatica prodotti
                        </li>
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Validazione dati pre-import
                        </li>
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Anteprima prima dell'import
                        </li>
                        <li>
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Report dettagliato risultati
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
