<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Piano> $piani
 */
?>
<div class="piani index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><?= __('Gestione Piani') ?></h3>
        <div class="btn-group">
            <?= $this->Html->link(
                '<i data-lucide="plus" style="width:16px;height:16px;"></i> ' . __('Nuovo Piano'),
                ['action' => 'add'],
                ['class' => 'button', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('nome', 'Nome') ?></th>
                    <th><?= $this->Paginator->sort('prezzo_mensile', 'Mensile') ?></th>
                    <th><?= $this->Paginator->sort('prezzo_annuale', 'Annuale') ?></th>
                    <th><?= $this->Paginator->sort('sort_order', 'Ordine') ?></th>
                    <th><?= $this->Paginator->sort('is_active', 'Stato') ?></th>
                    <th class="actions"><?= __('Azioni') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($piani as $piano): ?>
                <tr>
                    <td>
                        <strong><?= h($piano->nome) ?></strong>
                        <?php if ($piano->descrizione): ?>
                            <br><small class="text-muted"><?= h($piano->descrizione) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= $piano->prezzo_mensile !== null ? $this->Number->currency($piano->prezzo_mensile, 'EUR') : '-' ?></td>
                    <td><?= $piano->prezzo_annuale !== null ? $this->Number->currency($piano->prezzo_annuale, 'EUR') : '-' ?></td>
                    <td>
                        <span class="badge bg-secondary"><?= $piano->sort_order ?></span>
                    </td>
                    <td>
                        <?= $piano->is_active
                            ? '<span class="badge bg-success">Attivo</span>'
                            : '<span class="badge bg-danger">Disattivo</span>'
                        ?>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link(__('Vedi'), ['action' => 'view', $piano->id]) ?>
                        <?= $this->Html->link(__('Modifica'), ['action' => 'edit', $piano->id]) ?>
                        <?= $this->Form->postLink(
                            __('Elimina'),
                            ['action' => 'delete', $piano->id],
                            ['confirm' => __('Sei sicuro di voler eliminare il piano {0}?', $piano->nome)]
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
