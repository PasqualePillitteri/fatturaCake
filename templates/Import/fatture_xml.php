<?php
/**
 * Import Fatture XML - Upload Page
 *
 * @var \App\View\AppView $this
 */
?>
<div class="import-fatture-xml content">
    <div class="page-header">
        <h3><i data-lucide="file-code" style="width:24px;height:24px;"></i> <?= __('Import Fatture da XML/ZIP') ?></h3>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Upload Form -->
            <div class="form-card">
                <div class="form-card-header">
                    <i data-lucide="upload" style="width:18px;height:18px;"></i>
                    Carica File XML o ZIP
                </div>
                <div class="form-card-body">
                    <?= $this->Form->create(null, [
                        'url' => ['action' => 'previewXml'],
                        'type' => 'file',
                    ]) ?>

                    <div class="mb-4">
                        <label class="form-label required">File XML o ZIP</label>
                        <input type="file" name="xml_file" class="form-control form-control-lg"
                               accept=".xml,.zip,.p7m" required>
                        <div class="form-text">
                            Seleziona un file FatturaPA XML singolo o un archivio ZIP contenente piu XML.
                            <br>Formati supportati: .xml, .zip, .p7m
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <?= $this->Form->button(
                            '<i data-lucide="eye" style="width:16px;height:16px;"></i> ' . __('Anteprima'),
                            ['class' => 'btn btn-primary btn-lg', 'escapeTitle' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i data-lucide="file-spreadsheet" style="width:16px;height:16px;"></i> ' . __('Import da Excel'),
                            ['action' => 'fatture'],
                            ['class' => 'btn btn-outline-secondary btn-lg', 'escapeTitle' => false]
                        ) ?>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Instructions -->
            <div class="form-card">
                <div class="form-card-header">
                    <i data-lucide="info" style="width:18px;height:18px;"></i>
                    Istruzioni
                </div>
                <div class="form-card-body">
                    <ol class="mb-0 ps-3">
                        <li class="mb-2">Seleziona un file <strong>XML FatturaPA</strong> o un <strong>archivio ZIP</strong></li>
                        <li class="mb-2">Il sistema analizzera automaticamente il contenuto</li>
                        <li class="mb-2">Verifica l'anteprima dei dati estratti</li>
                        <li class="mb-2">Conferma l'import</li>
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
                            Import singolo XML FatturaPA
                        </li>
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Import massivo da ZIP
                        </li>
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Auto-detect fatture attive/passive
                        </li>
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Creazione automatica anagrafiche
                        </li>
                        <li class="mb-2">
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Skip duplicati automatico
                        </li>
                        <li>
                            <i data-lucide="check" class="text-success" style="width:16px;height:16px;"></i>
                            Supporto file firmati .p7m
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Supported Formats -->
            <div class="form-card mt-3">
                <div class="form-card-header">
                    <i data-lucide="file-type" style="width:18px;height:18px;"></i>
                    Formati Supportati
                </div>
                <div class="form-card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary">.xml</span>
                        <span class="badge bg-primary">.zip</span>
                        <span class="badge bg-secondary">.p7m</span>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Standard FatturaPA v1.2 (SDI)
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
