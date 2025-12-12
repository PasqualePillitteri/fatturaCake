<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string[]|\Cake\Collection\CollectionInterface $tenants
 */

// Ruoli con priorità (più alto = più privilegi)
$tuttiRuoli = [
    'user' => ['label' => 'Utente', 'priority' => 10],
    'staff' => ['label' => 'Staff', 'priority' => 20],
    'admin' => ['label' => 'Amministratore', 'priority' => 50],
    'superadmin' => ['label' => 'Super Admin', 'priority' => 100],
];

// Ruolo e priorità dell'utente corrente
$currentRole = $currentUser->role ?? 'user';
$currentPriority = $tuttiRuoli[$currentRole]['priority'] ?? 10;

// Filtra ruoli: mostra solo quelli con priorità <= alla propria
$ruoli = [];
foreach ($tuttiRuoli as $key => $info) {
    if ($info['priority'] <= $currentPriority) {
        $ruoli[$key] = $info['label'];
    }
}
?>
<div class="users form content form-content">
    <div class="page-header">
        <h3><?= __('Modifica Utente') ?>: <?= h($user->username) ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="eye" style="width:16px;height:16px;"></i> ' . __('Visualizza'),
                ['action' => 'view', $user->id],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($user) ?>

    <!-- Dati Account -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="user" style="width:18px;height:18px;"></i>
            Dati Account
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('username', ['label' => ['text' => 'Username', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('email', ['type' => 'email', 'label' => ['text' => 'Email', 'class' => 'required']]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('password', ['type' => 'password', 'label' => 'Nuova Password', 'value' => '']) ?>
                    <span class="help-text">Lasciare vuoto per non modificare la password</span>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('role', ['type' => 'select', 'options' => $ruoli, 'label' => 'Ruolo']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Dati Personali -->
    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="user-circle" style="width:18px;height:18px;"></i>
            Dati Personali
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('nome', ['label' => 'Nome']) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('cognome', ['label' => 'Cognome']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('telefono', ['label' => 'Telefono']) ?>
                </div>
                <?php if ($currentRole === 'superadmin'): ?>
                <div class="form-col-half">
                    <?= $this->Form->control('tenant_id', ['options' => $tenants, 'empty' => '-- Seleziona tenant --', 'label' => 'Tenant']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stato -->
    <div class="form-card">
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('email_verified', ['type' => 'checkbox', 'label' => 'Email verificata']) ?>
                </div>
            </div>
            <?php if ($user->last_login): ?>
            <div class="form-row">
                <div class="form-col">
                    <label>Ultimo login</label>
                    <p class="text-muted"><?= $user->last_login->format('d/m/Y H:i') ?></p>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->postLink(
                        '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                        ['action' => 'delete', $user->id],
                        ['confirm' => __('Sei sicuro di voler eliminare {0}?', $user->username), 'class' => 'btn btn-danger', 'escapeTitle' => false]
                    ) ?>
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva modifiche'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
