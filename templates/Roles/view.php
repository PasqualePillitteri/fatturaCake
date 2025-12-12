<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>
<div class="roles view content">
    <div class="page-header">
        <h3><?= h($role->display_name) ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="edit" style="width:16px;height:16px;"></i> ' . __('Modifica'),
                ['action' => 'edit', $role->id],
                ['class' => 'btn btn-primary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <!-- Dettagli Ruolo -->
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="shield" style="width:18px;height:18px;"></i>
            Dettagli Ruolo
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nome visualizzato:</th>
                            <td><?= h($role->display_name) ?></td>
                        </tr>
                        <tr>
                            <th>Codice:</th>
                            <td><code><?= h($role->name) ?></code></td>
                        </tr>
                        <tr>
                            <th>Descrizione:</th>
                            <td><?= h($role->description) ?: '<em class="text-muted">Nessuna</em>' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Priorità:</th>
                            <td><span class="badge bg-info"><?= $role->priority ?></span></td>
                        </tr>
                        <tr>
                            <th>Tipo:</th>
                            <td>
                                <?= $role->is_system
                                    ? '<span class="badge bg-danger">Ruolo di Sistema</span>'
                                    : '<span class="badge bg-secondary">Ruolo Custom</span>'
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Creato:</th>
                            <td><?= $role->created ? $role->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Permessi Assegnati -->
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="key" style="width:18px;height:18px;"></i>
            Permessi Assegnati (<?= count($role->permissions ?? []) ?>)
        </div>
        <div class="card-body">
            <?php if (!empty($role->permissions)): ?>
                <?php
                // Group permissions by group_name
                $grouped = [];
                foreach ($role->permissions as $permission) {
                    $group = $permission->group_name ?: 'Altro';
                    $grouped[$group][] = $permission;
                }
                ksort($grouped);
                ?>
                <div class="row">
                    <?php foreach ($grouped as $groupName => $permissions): ?>
                    <div class="col-md-4 mb-3">
                        <h6 class="text-muted"><?= h($groupName) ?></h6>
                        <ul class="list-unstyled">
                            <?php foreach ($permissions as $permission): ?>
                            <li>
                                <i data-lucide="check" style="width:14px;height:14px;" class="text-success"></i>
                                <?= h($permission->display_name ?: "{$permission->controller}:{$permission->action}") ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">
                    <?php if ($role->name === 'superadmin'): ?>
                        <i data-lucide="infinity" style="width:16px;height:16px;"></i>
                        Il ruolo superadmin ha accesso completo a tutte le funzionalità.
                    <?php else: ?>
                        Nessun permesso assegnato.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Utenti con questo ruolo -->
    <div class="card">
        <div class="card-header">
            <i data-lucide="users" style="width:18px;height:18px;"></i>
            Utenti con questo ruolo (<?= count($role->users ?? []) ?>)
        </div>
        <div class="card-body">
            <?php if (!empty($role->users)): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Nome</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($role->users as $user): ?>
                            <tr>
                                <td><?= h($user->username) ?></td>
                                <td><?= h($user->email) ?></td>
                                <td><?= h($user->nome . ' ' . $user->cognome) ?></td>
                                <td>
                                    <?= $this->Html->link(
                                        '<i data-lucide="eye" style="width:14px;height:14px;"></i>',
                                        ['controller' => 'Users', 'action' => 'view', $user->id],
                                        ['escapeTitle' => false, 'title' => 'Vedi utente']
                                    ) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">Nessun utente con questo ruolo.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
