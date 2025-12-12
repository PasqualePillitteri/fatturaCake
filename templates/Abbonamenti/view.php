<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Abbonamento $abbonamento
 */
?>
<div class="abbonamenti view content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Abbonamento #{0}', $abbonamento->id) ?></h3>
        <div class="btn-group">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="edit" style="width:16px;height:16px;"></i> ' . __('Modifica'),
                ['action' => 'edit', $abbonamento->id],
                ['class' => 'btn btn-primary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="building" style="width:18px;height:18px;"></i>
                    Tenant
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width:40%;">Nome</th>
                            <td><strong><?= h($abbonamento->tenant->nome ?? 'N/D') ?></strong></td>
                        </tr>
                        <?php if (!empty($abbonamento->tenant)): ?>
                        <tr>
                            <th>Email</th>
                            <td><?= h($abbonamento->tenant->email ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>P.IVA</th>
                            <td><?= h($abbonamento->tenant->partita_iva ?? '-') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="credit-card" style="width:18px;height:18px;"></i>
                    Piano
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width:40%;">Nome Piano</th>
                            <td><strong><?= h($abbonamento->piano->nome ?? 'N/D') ?></strong></td>
                        </tr>
                        <?php if (!empty($abbonamento->piano)): ?>
                        <tr>
                            <th>Prezzo Mensile</th>
                            <td><?= $abbonamento->piano->prezzo_mensile !== null ? $this->Number->currency($abbonamento->piano->prezzo_mensile, 'EUR') : '-' ?></td>
                        </tr>
                        <tr>
                            <th>Prezzo Annuale</th>
                            <td><?= $abbonamento->piano->prezzo_annuale !== null ? $this->Number->currency($abbonamento->piano->prezzo_annuale, 'EUR') : '-' ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="calendar-check" style="width:18px;height:18px;"></i>
                    Dettagli Abbonamento
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width:40%;">Tipo</th>
                                    <td>
                                        <?php
                                        $tipoBadge = $abbonamento->tipo === 'annuale' ? 'bg-info' : 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($abbonamento->tipo)) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Data Inizio</th>
                                    <td><?= $abbonamento->data_inizio->format('d/m/Y') ?></td>
                                </tr>
                                <tr>
                                    <th>Data Fine</th>
                                    <td><?= $abbonamento->data_fine ? $abbonamento->data_fine->format('d/m/Y') : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width:40%;">Importo</th>
                                    <td><strong><?= $abbonamento->importo !== null ? $this->Number->currency($abbonamento->importo, 'EUR') : '-' ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Stato</th>
                                    <td>
                                        <?php
                                        $statoBadge = match($abbonamento->stato) {
                                            'attivo' => 'bg-success',
                                            'scaduto' => 'bg-warning',
                                            'cancellato' => 'bg-danger',
                                            'sospeso' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $statoBadge ?>"><?= h(ucfirst($abbonamento->stato)) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Creato</th>
                                    <td><?= $abbonamento->created ? $abbonamento->created->format('d/m/Y H:i') : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php if ($abbonamento->note): ?>
                    <hr>
                    <h6>Note</h6>
                    <p class="mb-0"><?= nl2br(h($abbonamento->note)) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
