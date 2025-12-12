<?php
/**
 * Setup Wizard - Step 3: Listino Prezzi
 *
 * @var \App\View\AppView $this
 * @var array $listini
 * @var int $currentStep
 * @var array $steps
 */

$this->assign('title', 'Configurazione - Listino');
?>

<div class="setup-wizard">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?= $this->element('SetupWizard/progress', ['currentStep' => $currentStep, 'steps' => $steps]) ?>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i data-lucide="list" class="text-primary" style="width:24px;height:24px;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">Listino Prezzi</h4>
                                <p class="text-secondary mb-0">Crea il tuo listino prezzi base</p>
                            </div>
                        </div>
                        <?php if (empty($listini)): ?>
                            <?= $this->Form->create(null, ['url' => ['action' => 'listino']]) ?>
                            <?= $this->Form->hidden('crea_default', ['value' => '1']) ?>
                            <button type="submit" class="btn btn-outline-primary">
                                <i data-lucide="sparkles" style="width:16px;height:16px;" class="me-1"></i>
                                Crea Listino Base
                            </button>
                            <?= $this->Form->end() ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Existing Listini -->
                    <?php if (!empty($listini)): ?>
                        <div class="mb-4">
                            <h6 class="text-secondary mb-3">Listini creati:</h6>
                            <div class="row g-2">
                                <?php foreach ($listini as $list): ?>
                                    <div class="col-md-6">
                                        <div class="card bg-body-secondary border-0">
                                            <div class="card-body py-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i data-lucide="check-circle" class="text-success me-2" style="width:18px;height:18px;"></i>
                                                        <div>
                                                            <span class="fw-medium"><?= h($list->nome) ?></span>
                                                            <?php if ($list->is_default): ?>
                                                                <span class="badge bg-primary ms-2">Predefinito</span>
                                                            <?php endif; ?>
                                                            <?php if (!empty($list->descrizione)): ?>
                                                                <div class="text-secondary small"><?= h($list->descrizione) ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add New Listino -->
                    <div class="card bg-body-tertiary border-0 mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i data-lucide="plus" style="width:16px;height:16px;" class="me-1"></i>
                                Aggiungi Listino
                            </h6>
                            <?= $this->Form->create(null, ['url' => ['action' => 'listino']]) ?>
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <?= $this->Form->control('nome', [
                                        'label' => false,
                                        'class' => 'form-control',
                                        'placeholder' => 'Nome listino (es: Listino Retail)',
                                        'required' => false,
                                    ]) ?>
                                </div>
                                <div class="col-md-5">
                                    <?= $this->Form->control('descrizione', [
                                        'label' => false,
                                        'class' => 'form-control',
                                        'placeholder' => 'Descrizione (opzionale)',
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i data-lucide="plus" style="width:16px;height:16px;"></i>
                                        Aggiungi
                                    </button>
                                </div>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i data-lucide="info" style="width:18px;height:18px;" class="me-2 mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>Cos'è un listino?</strong><br>
                            Un listino raggruppa i prezzi dei tuoi prodotti. Puoi avere listini diversi per clienti retail, business, o per promozioni speciali.
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="<?= $this->Url->build(['action' => 'categorie']) ?>" class="btn btn-outline-secondary">
                            <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i>
                            Indietro
                        </a>
                        <?= $this->Form->create(null, ['url' => ['action' => 'listino']]) ?>
                        <?= $this->Form->hidden('next_step', ['value' => '1']) ?>
                        <button type="submit" class="btn btn-primary">
                            Continua
                            <i data-lucide="arrow-right" style="width:16px;height:16px;" class="ms-1"></i>
                        </button>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
