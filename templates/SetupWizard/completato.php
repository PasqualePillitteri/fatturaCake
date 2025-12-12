<?php
/**
 * Setup Wizard - Step 5: Completamento
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tenant $tenant
 * @var array $stats
 * @var int $currentStep
 * @var array $steps
 */

$this->assign('title', 'Configurazione Completata');
?>

<div class="setup-wizard">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?= $this->element('SetupWizard/progress', ['currentStep' => $currentStep, 'steps' => $steps]) ?>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-4 mb-3">
                            <i data-lucide="check-circle" class="text-success" style="width:64px;height:64px;"></i>
                        </div>
                        <h2 class="mb-2">Configurazione Completata!</h2>
                        <p class="text-secondary fs-5">
                            La tua applicazione è pronta per essere utilizzata.
                        </p>
                    </div>

                    <!-- Summary -->
                    <div class="row g-3 mb-4 justify-content-center">
                        <div class="col-md-3">
                            <div class="card bg-body-secondary border-0 h-100">
                                <div class="card-body text-center py-4">
                                    <i data-lucide="building" class="text-primary mb-2" style="width:32px;height:32px;"></i>
                                    <h5 class="mb-1"><?= h($tenant->nome) ?></h5>
                                    <small class="text-secondary">Azienda</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-body-secondary border-0 h-100">
                                <div class="card-body text-center py-4">
                                    <i data-lucide="folder" class="text-primary mb-2" style="width:32px;height:32px;"></i>
                                    <h5 class="mb-1"><?= $stats['categorie'] ?></h5>
                                    <small class="text-secondary">Categorie</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-body-secondary border-0 h-100">
                                <div class="card-body text-center py-4">
                                    <i data-lucide="list" class="text-primary mb-2" style="width:32px;height:32px;"></i>
                                    <h5 class="mb-1"><?= $stats['listini'] ?></h5>
                                    <small class="text-secondary">Listini</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-body-secondary border-0 h-100">
                                <div class="card-body text-center py-4">
                                    <i data-lucide="package" class="text-primary mb-2" style="width:32px;height:32px;"></i>
                                    <h5 class="mb-1"><?= $stats['prodotti'] ?></h5>
                                    <small class="text-secondary">Prodotti</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="mb-4">
                        <h6 class="text-secondary mb-3">Prossimi passi consigliati:</h6>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="<?= $this->Url->build(['controller' => 'Anagrafiche', 'action' => 'addCliente']) ?>" class="btn btn-outline-primary">
                                <i data-lucide="user-plus" style="width:16px;height:16px;" class="me-1"></i>
                                Aggiungi Cliente
                            </a>
                            <a href="<?= $this->Url->build(['controller' => 'Fatture', 'action' => 'addAttiva']) ?>" class="btn btn-outline-primary">
                                <i data-lucide="file-plus" style="width:16px;height:16px;" class="me-1"></i>
                                Crea Prima Fattura
                            </a>
                            <a href="<?= $this->Url->build(['controller' => 'Import', 'action' => 'fattureXml']) ?>" class="btn btn-outline-primary">
                                <i data-lucide="upload" style="width:16px;height:16px;" class="me-1"></i>
                                Importa Fatture
                            </a>
                        </div>
                    </div>

                    <!-- Complete Button -->
                    <?= $this->Form->create(null, ['url' => ['action' => 'completato']]) ?>
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i data-lucide="home" style="width:20px;height:20px;" class="me-2"></i>
                        Vai alla Dashboard
                    </button>
                    <?= $this->Form->end() ?>

                    <p class="text-secondary small mt-4 mb-0">
                        <i data-lucide="info" style="width:14px;height:14px;" class="me-1"></i>
                        Potrai sempre modificare queste impostazioni dal menu Impostazioni.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
