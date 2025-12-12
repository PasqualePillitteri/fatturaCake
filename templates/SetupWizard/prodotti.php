<?php
/**
 * Setup Wizard - Step 4: Prodotti
 *
 * @var \App\View\AppView $this
 * @var array $prodotti
 * @var array $categorie
 * @var array $listini
 * @var \App\Model\Entity\Listino|null $defaultListino
 * @var int $currentStep
 * @var array $steps
 */

$this->assign('title', 'Configurazione - Prodotti');
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
                                <i data-lucide="package" class="text-primary" style="width:24px;height:24px;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">Prodotti e Servizi</h4>
                                <p class="text-secondary mb-0">Aggiungi i tuoi primi prodotti o servizi</p>
                            </div>
                        </div>
                        <?php if (empty($prodotti)): ?>
                            <?= $this->Form->create(null, ['url' => ['action' => 'prodotti']]) ?>
                            <?= $this->Form->hidden('crea_esempi', ['value' => '1']) ?>
                            <button type="submit" class="btn btn-outline-primary">
                                <i data-lucide="sparkles" style="width:16px;height:16px;" class="me-1"></i>
                                Crea Esempi
                            </button>
                            <?= $this->Form->end() ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Existing Products -->
                    <?php if (!empty($prodotti)): ?>
                        <div class="mb-4">
                            <h6 class="text-secondary mb-3">Prodotti creati:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Codice</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th class="text-end">Prezzo</th>
                                            <th class="text-center">IVA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prodotti as $prod): ?>
                                            <tr>
                                                <td><code><?= h($prod->codice) ?></code></td>
                                                <td><?= h($prod->nome) ?></td>
                                                <td><?= h($prod->categoria->nome ?? '-') ?></td>
                                                <td class="text-end fw-medium">&euro; <?= number_format($prod->prezzo, 2, ',', '.') ?></td>
                                                <td class="text-center"><?= $prod->aliquota_iva ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add New Product -->
                    <div class="card bg-body-tertiary border-0 mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i data-lucide="plus" style="width:16px;height:16px;" class="me-1"></i>
                                Aggiungi Prodotto
                            </h6>
                            <?= $this->Form->create(null, ['url' => ['action' => 'prodotti']]) ?>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <?= $this->Form->control('codice', [
                                        'label' => 'Codice',
                                        'class' => 'form-control',
                                        'placeholder' => 'Auto',
                                    ]) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $this->Form->control('nome', [
                                        'label' => 'Nome *',
                                        'class' => 'form-control',
                                        'placeholder' => 'Nome prodotto/servizio',
                                        'required' => false,
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('categoria_id', [
                                        'label' => 'Categoria',
                                        'type' => 'select',
                                        'class' => 'form-select',
                                        'options' => $categorie,
                                        'empty' => 'Nessuna',
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('prezzo', [
                                        'label' => 'Prezzo',
                                        'type' => 'number',
                                        'step' => '0.01',
                                        'class' => 'form-control',
                                        'placeholder' => '0.00',
                                    ]) ?>
                                </div>
                                <div class="col-md-1">
                                    <?= $this->Form->control('aliquota_iva', [
                                        'label' => 'IVA %',
                                        'type' => 'select',
                                        'class' => 'form-select',
                                        'options' => [
                                            '22' => '22%',
                                            '10' => '10%',
                                            '5' => '5%',
                                            '4' => '4%',
                                            '0' => '0%',
                                        ],
                                        'default' => '22',
                                    ]) ?>
                                </div>

                                <div class="col-md-6">
                                    <?= $this->Form->control('descrizione', [
                                        'label' => 'Descrizione',
                                        'class' => 'form-control',
                                        'placeholder' => 'Descrizione prodotto (opzionale)',
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('unita_misura', [
                                        'label' => 'Unità Misura',
                                        'type' => 'select',
                                        'class' => 'form-select',
                                        'options' => [
                                            'NR' => 'NR - Numero',
                                            'HUR' => 'HUR - Ora',
                                            'DAY' => 'DAY - Giorno',
                                            'MON' => 'MON - Mese',
                                            'KGM' => 'KGM - Chilogrammo',
                                            'LTR' => 'LTR - Litro',
                                            'MTR' => 'MTR - Metro',
                                            'MTK' => 'MTK - Metro quadrato',
                                        ],
                                        'default' => 'NR',
                                    ]) ?>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i data-lucide="plus" style="width:16px;height:16px;" class="me-1"></i>
                                        Aggiungi Prodotto
                                    </button>
                                </div>
                            </div>
                            <?= $this->Form->end() ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="<?= $this->Url->build(['action' => 'listino']) ?>" class="btn btn-outline-secondary">
                            <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i>
                            Indietro
                        </a>
                        <?= $this->Form->create(null, ['url' => ['action' => 'prodotti']]) ?>
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
