<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="users view content">
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
                ['action' => 'edit', $user->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                ['action' => 'delete', $user->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare {0}?', $user->username),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0"><?= h($user->username) ?></h3>
        <div class="ms-3">
            <?php
            $roleBadge = match($user->role) {
                'superadmin' => 'bg-danger',
                'admin' => 'bg-danger',
                'manager' => 'bg-warning',
                'user' => 'bg-info',
                default => 'bg-secondary'
            };
            ?>
            <span class="badge <?= $roleBadge ?>"><?= h(ucfirst($user->role)) ?></span>
            <?= $user->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?>
        </div>
    </div>

    <div class="row">
        <!-- Dati Principali -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="user" style="width:16px;height:16px;"></i> Dati Utente
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Username') ?></th>
                            <td><strong><?= h($user->username) ?></strong></td>
                        </tr>
                        <tr>
                            <th><?= __('Email') ?></th>
                            <td>
                                <?= h($user->email) ?>
                                <?= $user->email_verified ? '<span class="badge bg-success ms-2">Verificata</span>' : '<span class="badge bg-warning ms-2">Non verificata</span>' ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Nome') ?></th>
                            <td><?= h($user->nome) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Cognome') ?></th>
                            <td><?= h($user->cognome) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Telefono') ?></th>
                            <td><?= h($user->telefono) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Ruolo') ?></th>
                            <td><span class="badge <?= $roleBadge ?>"><?= h(ucfirst($user->role)) ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Sistema -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="settings" style="width:16px;height:16px;"></i> Informazioni Sistema
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('ID') ?></th>
                            <td><code><?= $this->Number->format($user->id) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Tenant') ?></th>
                            <td>
                                <?php if ($user->hasValue('tenant')): ?>
                                    <?= $this->Html->link(h($user->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $user->tenant->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Stato') ?></th>
                            <td><?= $user->is_active ? '<span class="badge bg-success">Attivo</span>' : '<span class="badge bg-secondary">Non attivo</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Ultimo Login') ?></th>
                            <td><?= $user->last_login ? $user->last_login->format('d/m/Y H:i') : '<em class="text-muted">Mai</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Creato il') ?></th>
                            <td><?= $user->created ? $user->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Modificato il') ?></th>
                            <td><?= $user->modified ? $user->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Attività Correlati -->
    <?php if (!empty($user->log_attivita)): ?>
    <div class="card">
        <div class="card-header">
            <i data-lucide="activity" style="width:16px;height:16px;"></i> Ultime Attività
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><?= __('Data') ?></th>
                            <th><?= __('Azione') ?></th>
                            <th><?= __('Modello') ?></th>
                            <th><?= __('IP') ?></th>
                            <th class="actions"><?= __('Azioni') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($user->log_attivita, 0, 10) as $log): ?>
                        <tr>
                            <td><?= $log->created ? $log->created->format('d/m/Y H:i') : '-' ?></td>
                            <td><span class="badge bg-secondary"><?= h($log->azione) ?></span></td>
                            <td><?= h($log->modello) ?> #<?= h($log->modello_id) ?></td>
                            <td><code><?= h($log->ip_address) ?></code></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Vedi'), ['controller' => 'LogAttivita', 'action' => 'view', $log->id]) ?>
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
