<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ConfigurazioniSdi $configurazioniSdi
 */

$regimiFiscali = [
    'RF01' => 'Ordinario',
    'RF02' => 'Contribuenti minimi',
    'RF04' => 'Agricoltura',
    'RF18' => 'Altro',
    'RF19' => 'Forfettario',
];
?>
<div class="configurazioni-sdi view content">
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
                ['action' => 'edit', $configurazioniSdi->id],
                ['class' => 'btn btn-primary btn-sm', 'escapeTitle' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                ['action' => 'delete', $configurazioniSdi->id],
                [
                    'confirm' => __('Sei sicuro di voler eliminare questa configurazione?'),
                    'class' => 'btn btn-outline-danger btn-sm',
                    'escapeTitle' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Titolo -->
    <div class="d-flex align-items-center mb-4">
        <h3 class="mb-0">
            <i data-lucide="server" style="width:24px;height:24px;"></i>
            Configurazione SDI
        </h3>
        <div class="ms-3">
            <?php
            $ambienteBadge = $configurazioniSdi->ambiente === 'produzione' ? 'bg-success' : 'bg-warning';
            ?>
            <span class="badge <?= $ambienteBadge ?>"><?= h(ucfirst($configurazioniSdi->ambiente)) ?></span>
            <?= $configurazioniSdi->is_active ? '<span class="badge bg-success">Attiva</span>' : '<span class="badge bg-secondary">Non attiva</span>' ?>
        </div>
    </div>

    <div class="row">
        <!-- Configurazione API -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="cloud" style="width:16px;height:16px;"></i> Configurazione API Aruba
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Ambiente') ?></th>
                            <td><span class="badge <?= $ambienteBadge ?>"><?= h(ucfirst($configurazioniSdi->ambiente)) ?></span></td>
                        </tr>
                        <tr>
                            <th><?= __('Username') ?></th>
                            <td><code><?= h($configurazioniSdi->aruba_username) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Password') ?></th>
                            <td><code>********</code></td>
                        </tr>
                        <tr>
                            <th><?= __('Endpoint Upload') ?></th>
                            <td><small><?= h($configurazioniSdi->endpoint_upload) ?: '<em class="text-muted">-</em>' ?></small></td>
                        </tr>
                        <tr>
                            <th><?= __('Endpoint Stato') ?></th>
                            <td><small><?= h($configurazioniSdi->endpoint_stato) ?: '<em class="text-muted">-</em>' ?></small></td>
                        </tr>
                        <tr>
                            <th><?= __('Progressivo Invio') ?></th>
                            <td><code><?= $this->Number->format($configurazioniSdi->progressivo_invio) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Ultima Sincronizzazione') ?></th>
                            <td><?= $configurazioniSdi->ultima_sincronizzazione ? $configurazioniSdi->ultima_sincronizzazione->format('d/m/Y H:i') : '<em class="text-muted">Mai</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Dati Trasmissione -->
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="send" style="width:16px;height:16px;"></i> Dati Trasmissione
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('ID Paese') ?></th>
                            <td><code><?= h($configurazioniSdi->id_paese_trasmittente) ?: 'IT' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('ID Codice') ?></th>
                            <td><code><?= h($configurazioniSdi->id_codice_trasmittente) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('C.F. Trasmittente') ?></th>
                            <td><code><?= h($configurazioniSdi->codice_fiscale_trasmittente) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Formato') ?></th>
                            <td><span class="badge bg-secondary"><?= h($configurazioniSdi->formato_trasmissione) ?: 'FPR12' ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Dati Cedente -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="building" style="width:16px;height:16px;"></i> Dati Cedente/Prestatore
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <?php if ($configurazioniSdi->cedente_denominazione): ?>
                        <tr>
                            <th style="width:40%"><?= __('Denominazione') ?></th>
                            <td><strong><?= h($configurazioniSdi->cedente_denominazione) ?></strong></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <th style="width:40%"><?= __('Nome') ?></th>
                            <td><strong><?= h($configurazioniSdi->cedente_nome) ?> <?= h($configurazioniSdi->cedente_cognome) ?></strong></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th><?= __('Codice Fiscale') ?></th>
                            <td><code><?= h($configurazioniSdi->cedente_codice_fiscale) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Partita IVA') ?></th>
                            <td><code><?= h($configurazioniSdi->cedente_partita_iva) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Regime Fiscale') ?></th>
                            <td>
                                <?= h($configurazioniSdi->cedente_regime_fiscale) ?>
                                <small class="text-muted">(<?= $regimiFiscali[$configurazioniSdi->cedente_regime_fiscale] ?? '' ?>)</small>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Indirizzo') ?></th>
                            <td><?= h($configurazioniSdi->cedente_indirizzo) ?><?= $configurazioniSdi->cedente_numero_civico ? ', ' . h($configurazioniSdi->cedente_numero_civico) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Comune') ?></th>
                            <td><?= h($configurazioniSdi->cedente_comune) ?> (<?= h($configurazioniSdi->cedente_provincia) ?>)</td>
                        </tr>
                        <tr>
                            <th><?= __('CAP') ?></th>
                            <td><?= h($configurazioniSdi->cedente_cap) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Email') ?></th>
                            <td><?= h($configurazioniSdi->cedente_email) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('PEC') ?></th>
                            <td><?= h($configurazioniSdi->cedente_pec) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Telefono') ?></th>
                            <td><?= h($configurazioniSdi->cedente_telefono) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Dati Pagamento -->
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="credit-card" style="width:16px;height:16px;"></i> Dati Pagamento Predefiniti
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('IBAN') ?></th>
                            <td><code><?= h($configurazioniSdi->iban_predefinito) ?: '-' ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Banca') ?></th>
                            <td><?= h($configurazioniSdi->banca_predefinita) ?: '<em class="text-muted">-</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Firma Digitale -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="shield-check" style="width:16px;height:16px;"></i> Firma Digitale
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('Usa Firma Digitale') ?></th>
                            <td><?= $configurazioniSdi->usa_firma_digitale ? '<span class="badge bg-success">Si</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Certificato') ?></th>
                            <td><?= h($configurazioniSdi->certificato_path) ?: '<em class="text-muted">Non configurato</em>' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i data-lucide="settings" style="width:16px;height:16px;"></i> Informazioni Sistema
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:40%"><?= __('ID') ?></th>
                            <td><code><?= $this->Number->format($configurazioniSdi->id) ?></code></td>
                        </tr>
                        <tr>
                            <th><?= __('Tenant') ?></th>
                            <td>
                                <?php if ($configurazioniSdi->hasValue('tenant')): ?>
                                    <?= $this->Html->link(h($configurazioniSdi->tenant->nome), ['controller' => 'Tenants', 'action' => 'view', $configurazioniSdi->tenant->id]) ?>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?= __('Creato il') ?></th>
                            <td><?= $configurazioniSdi->created ? $configurazioniSdi->created->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Modificato il') ?></th>
                            <td><?= $configurazioniSdi->modified ? $configurazioniSdi->modified->format('d/m/Y H:i') : '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
