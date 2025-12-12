<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Piano $piano
 */
?>
<div class="piani view content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= h($piano->nome) ?></h3>
        <div class="btn-group">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="edit" style="width:16px;height:16px;"></i> ' . __('Modifica'),
                ['action' => 'edit', $piano->id],
                ['class' => 'btn btn-primary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="credit-card" style="width:18px;height:18px;"></i>
                    Dettagli Piano
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width:40%;">Nome</th>
                            <td><strong><?= h($piano->nome) ?></strong></td>
                        </tr>
                        <tr>
                            <th>Descrizione</th>
                            <td><?= h($piano->descrizione) ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Prezzo Mensile</th>
                            <td><?= $piano->prezzo_mensile !== null ? $this->Number->currency($piano->prezzo_mensile, 'EUR') : '-' ?></td>
                        </tr>
                        <tr>
                            <th>Prezzo Annuale</th>
                            <td><?= $piano->prezzo_annuale !== null ? $this->Number->currency($piano->prezzo_annuale, 'EUR') : '-' ?></td>
                        </tr>
                        <tr>
                            <th>Ordine</th>
                            <td><?= $piano->sort_order ?></td>
                        </tr>
                        <tr>
                            <th>Stato</th>
                            <td>
                                <?= $piano->is_active
                                    ? '<span class="badge bg-success">Attivo</span>'
                                    : '<span class="badge bg-danger">Disattivo</span>'
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Creato</th>
                            <td><?= $piano->created ? $piano->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th>Modificato</th>
                            <td><?= $piano->modified ? $piano->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="calendar-check" style="width:18px;height:18px;"></i>
                    Abbonamenti Attivi
                    <span class="badge bg-primary float-end"><?= count($piano->abbonamenti ?? []) ?></span>
                </div>
                <div class="card-body">
                    <?php if (!empty($piano->abbonamenti)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Inizio</th>
                                        <th>Stato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($piano->abbonamenti as $abbonamento): ?>
                                    <tr>
                                        <td><?= h($abbonamento->tenant->nome ?? 'N/D') ?></td>
                                        <td><?= $abbonamento->data_inizio->format('d/m/Y') ?></td>
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
                                            <span class="badge <?= $statoBadge ?>"><?= h($abbonamento->stato) ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nessun abbonamento associato a questo piano.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
