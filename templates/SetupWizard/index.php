<?php
/**
 * Setup Wizard - Index
 *
 * @var \App\View\AppView $this
 * @var int $currentStep
 * @var array $steps
 * @var int $totalSteps
 * @var array $stats
 * @var \App\Model\Entity\Tenant $tenant
 */

$this->assign('title', 'Configurazione Iniziale');
$this->Breadcrumbs->add('Setup Wizard', ['action' => 'index']);
?>

<div class="setup-wizard">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-4 mb-3">
                            <i data-lucide="rocket" class="text-primary" style="width:48px;height:48px;"></i>
                        </div>
                        <h2 class="mb-2">Benvenuto in FatturaCake!</h2>
                        <p class="text-secondary fs-5">
                            Configuriamo insieme la tua applicazione in pochi semplici passi.
                        </p>
                    </div>

                    <!-- Progress Overview -->
                    <div class="row g-3 mb-4 text-start">
                        <div class="col-md-4">
                            <div class="card h-100 <?= $stats['categorie'] > 0 ? 'border-success' : 'border-secondary' ?>">
                                <div class="card-body text-center">
                                    <i data-lucide="folder" class="<?= $stats['categorie'] > 0 ? 'text-success' : 'text-secondary' ?>" style="width:32px;height:32px;"></i>
                                    <h5 class="mt-2 mb-0"><?= $stats['categorie'] ?></h5>
                                    <small class="text-secondary">Categorie</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 <?= $stats['listini'] > 0 ? 'border-success' : 'border-secondary' ?>">
                                <div class="card-body text-center">
                                    <i data-lucide="list" class="<?= $stats['listini'] > 0 ? 'text-success' : 'text-secondary' ?>" style="width:32px;height:32px;"></i>
                                    <h5 class="mt-2 mb-0"><?= $stats['listini'] ?></h5>
                                    <small class="text-secondary">Listini</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 <?= $stats['prodotti'] > 0 ? 'border-success' : 'border-secondary' ?>">
                                <div class="card-body text-center">
                                    <i data-lucide="package" class="<?= $stats['prodotti'] > 0 ? 'text-success' : 'text-secondary' ?>" style="width:32px;height:32px;"></i>
                                    <h5 class="mt-2 mb-0"><?= $stats['prodotti'] ?></h5>
                                    <small class="text-secondary">Prodotti</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Preview -->
                    <div class="mb-4">
                        <h6 class="text-secondary mb-3">Cosa configureremo:</h6>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i data-lucide="building" style="width:14px;height:14px;" class="me-1"></i>
                                Dati Azienda
                            </span>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i data-lucide="folder" style="width:14px;height:14px;" class="me-1"></i>
                                Categorie
                            </span>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i data-lucide="list" style="width:14px;height:14px;" class="me-1"></i>
                                Listino
                            </span>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i data-lucide="package" style="width:14px;height:14px;" class="me-1"></i>
                                Prodotti
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= $this->Url->build(['action' => 'azienda']) ?>" class="btn btn-primary btn-lg px-5">
                            <i data-lucide="play" style="width:18px;height:18px;" class="me-2"></i>
                            Inizia Configurazione
                        </a>
                        <a href="<?= $this->Url->build(['action' => 'skip']) ?>" class="btn btn-outline-secondary btn-lg">
                            Salta per ora
                        </a>
                    </div>

                    <p class="text-secondary small mt-4 mb-0">
                        <i data-lucide="info" style="width:14px;height:14px;" class="me-1"></i>
                        Potrai sempre modificare queste impostazioni in seguito.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
