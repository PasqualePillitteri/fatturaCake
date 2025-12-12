<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CategorieProdotti $categorieProdotti
 * @var string[]|\Cake\Collection\CollectionInterface $tenants
 * @var string[]|\Cake\Collection\CollectionInterface $parentCategorieProdotti
 */
?>
<div class="categorieProdotti form content form-content">
    <div class="page-header">
        <h3><?= __('Modifica Categoria') ?>: <?= h($categorieProdotti->nome) ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($categorieProdotti) ?>

    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="folder-tree" style="width:18px;height:18px;"></i>
            Dati Categoria
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('nome', ['label' => ['text' => 'Nome', 'class' => 'required']]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('parent_id', ['options' => $parentCategorieProdotti, 'empty' => '-- Categoria root --', 'label' => 'Categoria Padre']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-full">
                    <?= $this->Form->control('descrizione', ['type' => 'textarea', 'rows' => 2, 'label' => 'Descrizione']) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-quarter">
                    <?= $this->Form->control('sort_order', ['type' => 'number', 'label' => 'Ordine']) ?>
                </div>
                <div class="form-col-quarter">
                    <?= $this->Form->control('is_active', ['type' => 'checkbox', 'label' => 'Attivo']) ?>
                </div>
            </div>

            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->postLink(
                        '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                        ['action' => 'delete', $categorieProdotti->id],
                        ['confirm' => __('Sei sicuro di voler eliminare {0}?', $categorieProdotti->nome), 'class' => 'btn btn-danger', 'escapeTitle' => false]
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
