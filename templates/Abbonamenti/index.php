<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Abbonamento> $abbonamenti
 */
?>
<div class="abbonamenti index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Gestione Abbonamenti') ?></h3>
        <div class="btn-group">
            <?= $this->Html->link(
                '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Abbonamento'),
                ['action' => 'add'],
                ['class' => 'button', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('tenant_id', 'Tenant') ?></th>
                    <th><?= $this->Paginator->sort('piano_id', 'Piano') ?></th>
                    <th><?= $this->Paginator->sort('tipo', 'Tipo') ?></th>
                    <th><?= $this->Paginator->sort('data_inizio', 'Inizio') ?></th>
                    <th><?= $this->Paginator->sort('data_fine', 'Fine') ?></th>
                    <th><?= $this->Paginator->sort('importo', 'Importo') ?></th>
                    <th><?= $this->Paginator->sort('stato', 'Stato') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($abbonamenti as $abbonamento): ?>
                <tr>
                    <td>
                        <strong><?= h($abbonamento->tenant->nome ?? 'N/D') ?></strong>
                    </td>
                    <td><?= h($abbonamento->piano->nome ?? 'N/D') ?></td>
                    <td>
                        <?php
                        $tipoBadge = $abbonamento->tipo === 'annuale' ? 'bg-info' : 'bg-secondary';
                        ?>
                        <span class="badge <?= $tipoBadge ?>"><?= h(ucfirst($abbonamento->tipo)) ?></span>
                    </td>
                    <td><?= $abbonamento->data_inizio->format('d/m/Y') ?></td>
                    <td><?= $abbonamento->data_fine ? $abbonamento->data_fine->format('d/m/Y') : '-' ?></td>
                    <td><?= $abbonamento->importo !== null ? $this->Number->currency($abbonamento->importo, 'EUR') : '-' ?></td>
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
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $abbonamento->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $abbonamento->id]) ?>
                        <?= $this->Form->postLink(
                            __('Elimina'),
                            ['action' => 'delete', $abbonamento->id],
                            ['confirm' => __('Sei sicuro di voler eliminare questo abbonamento?')]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('prima')) ?>
            <?= $this->Paginator->prev('< ' . __('precedente')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('successiva') . ' >') ?>
            <?= $this->Paginator->last(__('ultima') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Pagina {{page}} di {{pages}}, {{current}} record su {{count}} totali')) ?></p>
    </div>
</div>
