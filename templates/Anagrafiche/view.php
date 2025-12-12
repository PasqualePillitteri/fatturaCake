<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Anagrafiche $anagrafiche
 */

$regimiFiscali = [
    'RF01' => 'Ordinario',
    'RF02' => 'Contribuenti minimi',
    'RF04' => 'Agricoltura',
    'RF05' => 'Pesca',
    'RF18' => 'Altro',
    'RF19' => 'Forfettario',
];
?>
<div class="anagrafiche view content">
    <!-- Toolbar Azioni -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary btn-sm', 'escapeTitle' => false]
            ) ?>
        </div>
        <div class="d-flex gap-2">
            <?= $this->Html->link(
                '<i data-lucide="edit" style="width:16px;height:16px;"></i> ' . __('Modifica'),
                ['action' => 'edit', $anagrafiche->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                ['action' => 'delete', $anagrafiche->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare {0}?', $anagrafiche->denominazione ?: $anagrafiche->nome . ' ' . $anagrafiche->cognome),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0"><?= h($anagrafiche->denominazione ?: $anagrafiche->nome . ' ' . $anagrafiche->cognome) ?></h3>
        <div class="ms-3">
            <?php
            $tipoBadge = match($anagrafiche->tipo) {
                'cliente' => 'bg-primary',
                'fornitore' => 'bg-info',
                'entrambi' => 'bg-warning',
                default => 'bg-secondary'
            };
            ?>
            <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($anagrafiche->tipo)) ?></span>
            <?= $anagrafiche->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?>
            <?php if ($anagrafiche->split_payment): ?>
                <span class="badge bg-warning">Split Payment</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Dati Anagrafici -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="user" style="width:16px;height:16px;"></i> Dati Anagrafici
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Tipo') ?></th>
                            <td><span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($anagrafiche->tipo)) ?></span></td>
                        </tr>
                        <?php if ($anagrafiche->denominazione): ?>
                        <tr>
                            <th><?= __('Denominazione') ?></th>
                            <td><strong><?= h($anagrafiche->denominazione) ?></strong></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <th><?= __('Nome') ?></th>
                            <td><strong><?= h($anagrafiche->nome) ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Cognome') ?></th>
                            <td><strong><?= h($anagrafiche->cognome) ?></strong></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?= __('Codice Fiscale') ?></th>
                            <td><code><?= h($anagrafiche->codice_fiscale) ?: '-' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Partita IVA') ?></th>
                            <td><code><?= h($anagrafiche->partita_iva) ?: '-' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Regime Fiscale') ?></th>
                            <td>
                                <?php if ($anagrafiche->regime_fiscale): ?>
                                    <?= h($anagrafiche->regime_fiscale) ?>
                                    <small class="text-muted">(<?= $regimiFiscali[$anagrafiche->regime_fiscale] ?? '' ?>)</small>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Contatti -->
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="phone" style="width:16px;height:16px;"></i> Contatti
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Telefono') ?></th>
                            <td><?= h($anagrafiche->telefono) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Email') ?></th>
                            <td><?= $anagrafiche->email ? '<a href="mailto:' . h($anagrafiche->email) . '">' . h($anagrafiche->email) . '</a>' : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('PEC') ?></th>
                            <td><?= $anagrafiche->pec ? '<a href="mailto:' . h($anagrafiche->pec) . '">' . h($anagrafiche->pec) . '</a>' : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Codice SDI') ?></th>
                            <td><code><?= h($anagrafiche->codice_sdi) ?: '0000000' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Rif. Amministrazione') ?></th>
                            <td><?= h($anagrafiche->riferimento_amministrazione) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Indirizzo e Sistema -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="map-pin" style="width:16px;height:16px;"></i> Indirizzo
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Indirizzo') ?></th>
                            <td><?= h($anagrafiche->indirizzo) ?: '<em class="text-muted">-</em>' ?><?= $anagrafiche->numero_civico ? ', ' . h($anagrafiche->numero_civico) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('CAP') ?></th>
                            <td><?= h($anagrafiche->cap) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Comune') ?></th>
                            <td><?= h($anagrafiche->comune) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Provincia') ?></th>
                            <td><?= h($anagrafiche->provincia) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Nazione') ?></th>
                            <td><?= h($anagrafiche->nazione) ?: 'IT' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="settings" style="width:16px;height:16px;"></i> Informazioni Sistema
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('ID') ?></th>
                            <td><code><?= $this->Number->format($anagrafiche->id) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Tenant') ?></th>
                            <td>
                                <?php if ($anagrafiche->hasValue('tenant')): ?>
                                    <?= $this->Html->link(h($anagrafiche->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $anagrafiche->tenant->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Split Payment') ?></th>
                            <td><?= $anagrafiche->split_payment ? '<span class="badge bg-warning">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Stato') ?></th>
                            <td><?= $anagrafiche->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Creato il') ?></th>
                            <td><?= $anagrafiche->created ? $anagrafiche->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Modificato il') ?></th>
                            <td><?= $anagrafiche->modified ? $anagrafiche->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Note -->
    <?php if ($anagrafiche->note): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="sticky-note" style="width:16px;height:16px;"></i> Note
        </div>
        <div class="card-body">
            <?= $this->Text->autoParagraph(h($anagrafiche->note)) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Fatture Correlate -->
    <?php if (!empty($anagrafiche->fatture)): ?>
    <div class="card">
        <div class="card-header">
            <i data-lucide="file-text" style="width:16px;height:16px;"></i> Fatture
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= __('Numero') ?></th>
                            <th><?= __('Data') ?></th>
                            <th><?= __('Tipo') ?></th>
                            <th><?= __('Direzione') ?></th>
                            <th><?= __('Totale') ?></th>
                            <th><?= __('Stato SDI') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($anagrafiche->fatture, 0, 10) as $fattura): ?>
                        <tr>
                            <td><strong><?= h($fattura->numero) ?></strong></td>
                            <td><?= $fattura->data ? $fattura->data->format('d/m/Y') : '-' ?></td>
                            <td><span class="badge bg-secondary"><?= h($fattura->tipo_documento) ?></span></td>
                            <td>
                                <?php
                                $direzioneBadge = $fattura->direzione === 'attiva' ? 'bg-success' : 'bg-info';
                                ?>
                                <span class="badge <?= $direzioneBadge ?>"><?= h(ucfirst($fattura->direzione)) ?></span>
                            </td>
                            <td class="text-end"><strong><?= $this->Number->currency($fattura->totale_documento, 'EUR') ?></strong></td>
                            <td>
                                <?php
                                $statoClass = match($fattura->stato_sdi) {
                                    'inviata', 'accettata', 'consegnata' => 'bg-success',
                                    'scartata', 'rifiutata' => 'bg-danger',
                                    'in_elaborazione' => 'bg-warning',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statoClass ?>"><?= h(ucfirst(str_replace('_', ' ', $fattura->stato_sdi))) ?></span>
                            </td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['controller' => 'Fatture', 'action' => 'view', $fattura->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($anagrafiche->fatture) > 10): ?>
            <div class="card-footer text-center">
                <?= $this->Html->link(
                    __('Vedi tutte le fatture ({0})', count($anagrafiche->fatture)),
                    ['controller' => 'Fatture', 'action' => 'index', '?' => ['anagrafica_id' => $anagrafiche->id]]
                ) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
