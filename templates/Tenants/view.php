<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tenant $tenant
 */
?>
<div class="tenants view content">
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
                ['action' => 'edit', $tenant->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                ['action' => 'delete', $tenant->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare {0}?', $tenant->nome),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0"><?= h($tenant->nome) ?></h3>
        <div class="ms-3">
            <?php
            $tipoBadge = match($tenant->tipo) {
                'azienda' => 'bg-primary',
                'professionista' => 'bg-info',
                default => 'bg-secondary'
            };
            ?>
            <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($tenant->tipo)) ?></span>
            <?= $tenant->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?>
        </div>
    </div>

    <div class="row">
        <!-- Dati Aziendali -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="building" style="width:16px;height:16px;"></i> Dati Aziendali
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Nome') ?></th>
                            <td><strong><?= h($tenant->nome) ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Tipo') ?></th>
                            <td><span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($tenant->tipo)) ?></span></td>
                        </tr>
                        <tr>
                            <th><?= __('Codice Fiscale') ?></th>
                            <td><code><?= h($tenant->codice_fiscale) ?: '-' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Partita IVA') ?></th>
                            <td><code><?= h($tenant->partita_iva) ?: '-' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Slug') ?></th>
                            <td><code><?= h($tenant->slug) ?: '-' ?></code></td>
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
                            <td><?= h($tenant->telefono) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Email') ?></th>
                            <td><?= $tenant->email ? '<a href="mailto:' . h($tenant->email) . '">' . h($tenant->email) . '</a>' : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('PEC') ?></th>
                            <td><?= $tenant->pec ? '<a href="mailto:' . h($tenant->pec) . '">' . h($tenant->pec) . '</a>' : '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Sito Web') ?></th>
                            <td><?= $tenant->sito_web ? '<a href="' . h($tenant->sito_web) . '" target="_blank">' . h($tenant->sito_web) . '</a>' : '<em class="text-muted">-</em>' ?></td>
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
                            <td><?= h($tenant->indirizzo) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Città') ?></th>
                            <td><?= h($tenant->citta) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Provincia') ?></th>
                            <td><?= h($tenant->provincia) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('CAP') ?></th>
                            <td><?= h($tenant->cap) ?: '<em class="text-muted">-</em>' ?></td>
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
                            <td><code><?= $this->Number->format($tenant->id) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Config. SDI') ?></th>
                            <td>
                                <?php if ($tenant->hasValue('configurazioni_sdi')): ?>
                                    <?= $this->Html->link(
                                        h($tenant->configurazioni_sdi->ambiente),
                                        ['controller' => 'ConfigurazioniSdi', 'action' => 'view', $tenant->configurazioni_sdi->id]
                                    ) ?>
                                <?php else: ?>
                                    <em class="text-muted">Non configurato</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Stato') ?></th>
                            <td><?= $tenant->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Creato il') ?></th>
                            <td><?= $tenant->created ? $tenant->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Modificato il') ?></th>
                            <td><?= $tenant->modified ? $tenant->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Descrizione -->
    <?php if ($tenant->descrizione): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="align-left" style="width:16px;height:16px;"></i> Descrizione
        </div>
        <div class="card-body">
            <?= $this->Text->autoParagraph(h($tenant->descrizione)) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistiche rapide -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="mb-0"><?= count($tenant->users ?? []) ?></h4>
                    <small class="text-muted">Utenti</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="mb-0"><?= count($tenant->anagrafiche ?? []) ?></h4>
                    <small class="text-muted">Anagrafiche</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="mb-0"><?= count($tenant->prodotti ?? []) ?></h4>
                    <small class="text-muted">Prodotti</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="mb-0"><?= count($tenant->fatture ?? []) ?></h4>
                    <small class="text-muted">Fatture</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Utenti -->
    <?php if (!empty($tenant->users)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="users" style="width:16px;height:16px;"></i> Utenti
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= __('Username') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('Ruolo') ?></th>
                            <th><?= __('Attivo') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($tenant->users, 0, 5) as $user): ?>
                        <tr>
                            <td><strong><?= h($user->username) ?></strong></td>
                            <td><?= h($user->email) ?></td>
                            <td><?= h($user->nome) ?> <?= h($user->cognome) ?></td>
                            <td>
                                <?php
                                $roleBadge = match($user->role) {
                                    'superadmin', 'admin' => 'bg-danger',
                                    'manager' => 'bg-warning',
                                    default => 'bg-info'
                                };
                                ?>
                                <span class="badge <?= $roleBadge ?>"><?= h(ucfirst($user->role)) ?></span>
                            </td>
                            <td><?= $user->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['controller' => 'Users', 'action' => 'view', $user->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Listini -->
    <?php if (!empty($tenant->listini)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="list" style="width:16px;height:16px;"></i> Listini
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= __('Nome') ?></th>
                            <th><?= __('Valuta') ?></th>
                            <th><?= __('Inizio') ?></th>
                            <th><?= __('Fine') ?></th>
                            <th><?= __('Default') ?></th>
                            <th><?= __('Attivo') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tenant->listini as $listino): ?>
                        <tr>
                            <td><strong><?= h($listino->nome) ?></strong></td>
                            <td><span class="badge bg-secondary"><?= h($listino->valuta) ?></span></td>
                            <td><?= $listino->data_inizio ? $listino->data_inizio->format('d/m/Y') : '-' ?></td>
                            <td><?= $listino->data_fine ? $listino->data_fine->format('d/m/Y') : '-' ?></td>
                            <td><?= $listino->is_default ? '<span class="badge bg-primary">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td><?= $listino->is_active ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['controller' => 'Listini', 'action' => 'view', $listino->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
