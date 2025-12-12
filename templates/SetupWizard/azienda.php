<?php
/**
 * Setup Wizard - Step 1: Dati Azienda
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tenant $tenant
 * @var int $currentStep
 * @var array $steps
 */

$this->assign('title', 'Configurazione - Dati Azienda');
?>

<div class="setup-wizard">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Progress Bar -->
            <?= $this->element('SetupWizard/progress', ['currentStep' => $currentStep, 'steps' => $steps]) ?>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i data-lucide="building" class="text-primary" style="width:24px;height:24px;"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">Dati Azienda</h4>
                            <p class="text-secondary mb-0">Completa i dati della tua azienda</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?= $this->Form->create($tenant) ?>

                    <div class="row g-3">
                        <!-- Nome Azienda -->
                        <div class="col-md-6">
                            <?= $this->Form->control('nome', [
                                'label' => 'Ragione Sociale *',
                                'class' => 'form-control',
                                'placeholder' => 'Es: Azienda S.r.l.',
                            ]) ?>
                        </div>

                        <!-- Partita IVA -->
                        <div class="col-md-3">
                            <?= $this->Form->control('partita_iva', [
                                'label' => 'Partita IVA',
                                'class' => 'form-control',
                                'placeholder' => '12345678901',
                                'maxlength' => 11,
                            ]) ?>
                        </div>

                        <!-- Codice Fiscale -->
                        <div class="col-md-3">
                            <?= $this->Form->control('codice_fiscale', [
                                'label' => 'Codice Fiscale',
                                'class' => 'form-control',
                                'placeholder' => 'RSSMRA80A01H501U',
                                'maxlength' => 16,
                            ]) ?>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="text-secondary mb-3">Indirizzo</h6>
                        </div>

                        <!-- Indirizzo -->
                        <div class="col-md-6">
                            <?= $this->Form->control('indirizzo', [
                                'label' => 'Indirizzo',
                                'class' => 'form-control',
                                'placeholder' => 'Via Roma, 1',
                            ]) ?>
                        </div>

                        <!-- CAP -->
                        <div class="col-md-2">
                            <?= $this->Form->control('cap', [
                                'label' => 'CAP',
                                'class' => 'form-control',
                                'placeholder' => '00100',
                                'maxlength' => 5,
                            ]) ?>
                        </div>

                        <!-- Citta -->
                        <div class="col-md-3">
                            <?= $this->Form->control('citta', [
                                'label' => 'Città',
                                'class' => 'form-control',
                                'placeholder' => 'Roma',
                            ]) ?>
                        </div>

                        <!-- Provincia -->
                        <div class="col-md-1">
                            <?= $this->Form->control('provincia', [
                                'label' => 'Prov.',
                                'class' => 'form-control',
                                'placeholder' => 'RM',
                                'maxlength' => 2,
                            ]) ?>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="text-secondary mb-3">Contatti</h6>
                        </div>

                        <!-- Telefono -->
                        <div class="col-md-4">
                            <?= $this->Form->control('telefono', [
                                'label' => 'Telefono',
                                'class' => 'form-control',
                                'placeholder' => '+39 06 1234567',
                            ]) ?>
                        </div>

                        <!-- Email -->
                        <div class="col-md-4">
                            <?= $this->Form->control('email', [
                                'label' => 'Email',
                                'type' => 'email',
                                'class' => 'form-control',
                                'placeholder' => 'info@azienda.it',
                            ]) ?>
                        </div>

                        <!-- PEC -->
                        <div class="col-md-4">
                            <?= $this->Form->control('pec', [
                                'label' => 'PEC',
                                'type' => 'email',
                                'class' => 'form-control',
                                'placeholder' => 'azienda@pec.it',
                            ]) ?>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="text-secondary mb-3">Fatturazione Elettronica</h6>
                        </div>

                        <!-- Codice SDI -->
                        <div class="col-md-4">
                            <?= $this->Form->control('codice_sdi', [
                                'label' => 'Codice Destinatario SDI',
                                'class' => 'form-control',
                                'placeholder' => '0000000',
                                'maxlength' => 7,
                            ]) ?>
                        </div>

                        <!-- Regime Fiscale -->
                        <div class="col-md-4">
                            <?= $this->Form->control('regime_fiscale', [
                                'label' => 'Regime Fiscale',
                                'type' => 'select',
                                'class' => 'form-select',
                                'options' => [
                                    'RF01' => 'RF01 - Ordinario',
                                    'RF02' => 'RF02 - Contribuenti minimi',
                                    'RF04' => 'RF04 - Agricoltura',
                                    'RF05' => 'RF05 - Pesca',
                                    'RF06' => 'RF06 - Vendita sali e tabacchi',
                                    'RF07' => 'RF07 - Commercio fiammiferi',
                                    'RF08' => 'RF08 - Editoria',
                                    'RF09' => 'RF09 - Gestione servizi telefonia',
                                    'RF10' => 'RF10 - Rivendita documenti trasporto',
                                    'RF11' => 'RF11 - Agenzie viaggio e turismo',
                                    'RF12' => 'RF12 - Agriturismo',
                                    'RF13' => 'RF13 - Vendite a domicilio',
                                    'RF14' => 'RF14 - Rivendita beni usati',
                                    'RF15' => 'RF15 - Agenzie vendite all\'asta',
                                    'RF16' => 'RF16 - IVA per cassa',
                                    'RF17' => 'RF17 - IVA per cassa P.A.',
                                    'RF18' => 'RF18 - Altro',
                                    'RF19' => 'RF19 - Forfettario',
                                ],
                                'empty' => 'Seleziona...',
                            ]) ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <a href="<?= $this->Url->build(['action' => 'skip']) ?>" class="btn btn-outline-secondary">
                            <i data-lucide="x" style="width:16px;height:16px;" class="me-1"></i>
                            Salta Wizard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Salva e Continua
                            <i data-lucide="arrow-right" style="width:16px;height:16px;" class="ms-1"></i>
                        </button>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
