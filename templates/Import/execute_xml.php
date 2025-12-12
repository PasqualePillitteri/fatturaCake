<?php
/**
 * Import Fatture XML - Result Page
 *
 * @var \App\View\AppView $this
 * @var bool $success
 * @var array $stats
 * @var array $errors
 * @var array $warnings
 * @var string $filename
 */
?>
<div class="import-result-xml content">
    <div class="page-header">
        <h3><i data-lucide="check-circle" style="width:24px;height:24px;"></i> <?= __('Risultato Import XML') ?></h3>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success d-flex align-items-center mb-4">
        <i data-lucide="check-circle" style="width:24px;height:24px;" class="me-3"></i>
        <div>
            <h5 class="mb-1">Import completato con successo!</h5>
            <p class="mb-0">File: <strong><?= h($filename) ?></strong></p>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger d-flex align-items-center mb-4">
        <i data-lucide="x-circle" style="width:24px;height:24px;" class="me-3"></i>
        <div>
            <h5 class="mb-1">Import completato con errori</h5>
            <p class="mb-0">File: <strong><?= h($filename) ?></strong></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="form-card text-center">
                <div class="form-card-body py-3">
                    <h2 class="mb-0 text-primary"><?= $stats['files_parsed'] ?? 0 ?></h2>
                    <small class="text-muted">File analizzati</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-card text-center">
                <div class="form-card-body py-3">
                    <h2 class="mb-0 text-success"><?= $stats['fatture_create'] ?? 0 ?></h2>
                    <small class="text-muted">Fatture create</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-card text-center">
                <div class="form-card-body py-3">
                    <h2 class="mb-0 text-info"><?= $stats['righe_create'] ?? 0 ?></h2>
                    <small class="text-muted">Righe create</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-card text-center">
                <div class="form-card-body py-3">
                    <h2 class="mb-0 text-secondary"><?= $stats['anagrafiche_create'] ?? 0 ?></h2>
                    <small class="text-muted">Anagrafiche create</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-card text-center">
                <div class="form-card-body py-3">
                    <h2 class="mb-0 text-warning"><?= $stats['skipped'] ?? 0 ?></h2>
                    <small class="text-muted">Saltate (duplicati)</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-card text-center">
                <div class="form-card-body py-3">
                    <h2 class="mb-0 text-danger"><?= $stats['errors'] ?? 0 ?></h2>
                    <small class="text-muted">Errori</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="form-card mb-4">
        <div class="form-card-header bg-danger text-white">
            <i data-lucide="alert-circle" style="width:18px;height:18px;"></i>
            Errori (<?= count($errors) ?>)
        </div>
        <div class="form-card-body">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li class="text-danger"><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($warnings)): ?>
    <div class="form-card mb-4">
        <div class="form-card-header bg-warning">
            <i data-lucide="alert-triangle" style="width:18px;height:18px;"></i>
            Avvisi (<?= count($warnings) ?>)
        </div>
        <div class="form-card-body">
            <ul class="mb-0">
                <?php foreach ($warnings as $warning): ?>
                <li class="text-warning"><?= h($warning) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="d-flex gap-2">
        <?= $this->Html->link(
            '<i data-lucide="upload" style="width:16px;height:16px;"></i> ' . __('Importa altri file'),
            ['action' => 'fattureXml'],
            ['class' => 'btn btn-primary btn-lg', 'escapeTitle' => false]
        ) ?>
        <?= $this->Html->link(
            '<i data-lucide="file-text" style="width:16px;height:16px;"></i> ' . __('Vai alle Fatture'),
            ['controller' => 'Fatture', 'action' => 'index'],
            ['class' => 'btn btn-outline-secondary btn-lg', 'escapeTitle' => false]
        ) ?>
    </div>
</div>
