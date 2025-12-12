<?php
/**
 * Setup Wizard - Progress Bar Element
 *
 * @var \App\View\AppView $this
 * @var int $currentStep
 * @var array $steps
 */

$stepIcons = [
    1 => 'building',
    2 => 'folder',
    3 => 'list',
    4 => 'package',
    5 => 'check-circle',
];

$stepLabels = [
    1 => 'Azienda',
    2 => 'Categorie',
    3 => 'Listino',
    4 => 'Prodotti',
    5 => 'Completato',
];

$totalSteps = count($steps);
$progressPercent = (($currentStep - 1) / ($totalSteps - 1)) * 100;
?>

<div class="wizard-progress mb-4">
    <!-- Progress Bar -->
    <div class="progress mb-3" style="height: 4px;">
        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $progressPercent ?>%"></div>
    </div>

    <!-- Step Indicators -->
    <div class="d-flex justify-content-between">
        <?php foreach ($steps as $num => $action): ?>
            <?php
            $isActive = $num === $currentStep;
            $isCompleted = $num < $currentStep;
            $statusClass = $isActive ? 'bg-primary text-white' : ($isCompleted ? 'bg-success text-white' : 'bg-body-secondary text-secondary');
            ?>
            <div class="text-center" style="flex: 1;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle <?= $statusClass ?>" style="width: 40px; height: 40px;">
                    <?php if ($isCompleted): ?>
                        <i data-lucide="check" style="width:18px;height:18px;"></i>
                    <?php else: ?>
                        <i data-lucide="<?= $stepIcons[$num] ?? 'circle' ?>" style="width:18px;height:18px;"></i>
                    <?php endif; ?>
                </div>
                <div class="small mt-1 <?= $isActive ? 'fw-semibold text-primary' : 'text-secondary' ?>">
                    <?= $stepLabels[$num] ?? "Step {$num}" ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
