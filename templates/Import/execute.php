<?php
/**
 * Import Result Page
 *
 * @var \App\View\AppView $this
 * @var bool $success
 * @var array $stats
 * @var array $errors
 * @var string $filename
 */
?>
<div class="import-result content">
    <div class="page-header">
        <h3>
            <?php if ($success): ?>
                <i data-lucide="check-circle" class="text-success" style="width:24px;height:24px;"></i>
            <?php else: ?>
                <i data-lucide="x-circle" class="text-danger" style="width:24px;height:24px;"></i>
            <?php endif; ?>
            <?= __('Risultato Import') ?>
        </h3>
    </div>

    <!-- Status Alert -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <h4 class="alert-heading">
                <i data-lucide="check" style="width:20px;height:20px;"></i>
                Import completato con successo!
            </h4>
            <p class="mb-0">Il file <strong><?= h($filename) ?></strong> e stato importato correttamente.</p>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <h4 class="alert-heading">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
                Import fallito
            </h4>
            <p class="mb-0">Si sono verificati errori durante l'import.</p>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <div class="h2 mb-0 text-primary"><?= $stats['fatture_create'] ?></div>
                    <small class="text-muted">Fatture Create</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <div class="h2 mb-0 text-info"><?= $stats['righe_create'] ?></div>
                    <small class="text-muted">Righe Create</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <div class="h2 mb-0 text-success"><?= $stats['anagrafiche_create'] ?></div>
                    <small class="text-muted">Anagrafiche Create</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <div class="h2 mb-0 text-warning"><?= $stats['prodotti_create'] ?></div>
                    <small class="text-muted">Prodotti Creati</small>
                </div>
            </div>
        </div>
    </div>

    <?php if ($stats['errors'] > 0): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center border-danger">
                    <div class="card-body">
                        <div class="h2 mb-0 text-danger"><?= $stats['errors'] ?></div>
                        <small class="text-muted">Errori/Righe Saltate</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Errors Detail -->
    <?php if (!empty($errors)): ?>
        <div class="form-card mb-4">
            <div class="form-card-header bg-danger text-white">
                <i data-lucide="alert-circle" style="width:18px;height:18px;"></i>
                Dettaglio Errori
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

    <!-- Actions -->
    <div class="form-card">
        <div class="form-card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <?php if ($success && $stats['fatture_create'] > 0): ?>
                        <span class="text-success">
                            <i data-lucide="info" style="width:16px;height:16px;"></i>
                            Le fatture sono state create con stato "Bozza"
                        </span>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <?= $this->Html->link(
                        '<i data-lucide="upload" style="width:16px;height:16px;"></i> Nuovo Import',
                        ['action' => 'fatture'],
                        ['class' => 'btn btn-primary', 'escapeTitle' => false]
                    ) ?>
                    <?php if ($success && $stats['fatture_create'] > 0): ?>
                        <?= $this->Html->link(
                            '<i data-lucide="file-text" style="width:16px;height:16px;"></i> Vai alle Fatture',
                            ['controller' => 'Fatture', 'action' => 'index'],
                            ['class' => 'btn btn-success', 'escapeTitle' => false]
                        ) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
