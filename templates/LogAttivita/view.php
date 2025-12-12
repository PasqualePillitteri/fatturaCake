<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LogAttivitum $logAttivitum
 */

// Badge per azioni
$azioneBadge = match($logAttivitum->azione) {
    'create' => 'bg-success',
    'update' => 'bg-info',
    'delete' => 'bg-danger',
    'login' => 'bg-primary',
    'logout' => 'bg-secondary',
    default => 'bg-secondary'
};

$azioneLabel = match($logAttivitum->azione) {
    'create' => 'Creazione',
    'update' => 'Modifica',
    'delete' => 'Eliminazione',
    'login' => 'Login',
    'logout' => 'Logout',
    default => ucfirst($logAttivitum->azione)
};
?>
<div class="log-attivita view content">
    <!-- Toolbar Azioni -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary btn-sm', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0">
            <i data-lucide="activity" style="width:24px;height:24px;"></i>
            Log Attività #<?= $logAttivitum->id ?>
        </h3>
        <div class="ms-3">
            <span class="badge <?= $azioneBadge ?>"><?= $azioneLabel ?></span>
        </div>
    </div>

    <div class="row">
        <!-- Dettagli Attività -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="info" style="width:16px;height:16px;"></i> Dettagli Attività
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Azione') ?></th>
                            <td><span class="badge <?= $azioneBadge ?>"><?= $azioneLabel ?></span></td>
                        </tr>
                        <tr>
                            <th><?= __('Modello') ?></th>
                            <td><code><?= h($logAttivitum->modello) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('ID Record') ?></th>
                            <td><code><?= $logAttivitum->modello_id !== null ? $this->Number->format($logAttivitum->modello_id) : '-' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Data/Ora') ?></th>
                            <td><?= $logAttivitum->created ? $logAttivitum->created->format('d/m/Y H:i:s') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Utente e Sessione -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="user" style="width:16px;height:16px;"></i> Utente e Sessione
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Utente') ?></th>
                            <td>
                                <?php if ($logAttivitum->hasValue('user')): ?>
                                    <?= $this->Html->link(h($logAttivitum->user->username), ['controller' => 'Users', 'action' => 'view', $logAttivitum->user->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">Sistema</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Tenant') ?></th>
                            <td>
                                <?php if ($logAttivitum->hasValue('tenant')): ?>
                                    <?= $this->Html->link(h($logAttivitum->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $logAttivitum->tenant->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Indirizzo IP') ?></th>
                            <td><code><?= h($logAttivitum->ip_address) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('User Agent') ?></th>
                            <td><small class="text-muted"><?= h($logAttivitum->user_agent) ?></small></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Dati Precedenti -->
    <?php if ($logAttivitum->dati_precedenti): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="file-minus" style="width:16px;height:16px;"></i> Dati Precedenti
        </div>
        <div class="card-body">
            <?php
            $datiPrecedenti = json_decode($logAttivitum->dati_precedenti, true);
            if ($datiPrecedenti && is_array($datiPrecedenti)):
            ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Valore</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datiPrecedenti as $campo => $valore): ?>
                        <tr>
                            <td><code><?= h($campo) ?></code></td>
                            <td><?= is_array($valore) ? '<pre>' . h(json_encode($valore, JSON_PRETTY_PRINT)) . '</pre>' : h($valore) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <pre class="bg-light p-3"><?= h($logAttivitum->dati_precedenti) ?></pre>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Dati Nuovi -->
    <?php if ($logAttivitum->dati_nuovi): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i data-lucide="file-plus" style="width:16px;height:16px;"></i> Dati Nuovi
        </div>
        <div class="card-body">
            <?php
            $datiNuovi = json_decode($logAttivitum->dati_nuovi, true);
            if ($datiNuovi && is_array($datiNuovi)):
            ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Valore</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datiNuovi as $campo => $valore): ?>
                        <tr>
                            <td><code><?= h($campo) ?></code></td>
                            <td><?= is_array($valore) ? '<pre>' . h(json_encode($valore, JSON_PRETTY_PRINT)) . '</pre>' : h($valore) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <pre class="bg-light p-3"><?= h($logAttivitum->dati_nuovi) ?></pre>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
