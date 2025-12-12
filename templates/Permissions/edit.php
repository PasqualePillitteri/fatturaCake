<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Permission $permission
 * @var array $groups
 * @var array $controllers
 * @var array $actions
 */
?>
<div class="permissions form content form-content">
    <div class="page-header">
        <h3><?= __('Modifica Permesso') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
            <?= $this->Html->link(
                '<i data-lucide="eye" style="width:16px;height:16px;"></i> ' . __('Visualizza'),
                ['action' => 'view', $permission->id],
                ['class' => 'btn btn-outline-secondary', 'escapeTitle' => false]
            ) ?>
        </div>
    </div>

    <?= $this->Form->create($permission) ?>

    <div class="form-card">
        <div class="form-card-header">
            <i data-lucide="key" style="width:18px;height:18px;"></i>
            Dati Permesso
        </div>
        <div class="form-card-body">
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('controller', [
                        'label' => ['text' => 'Controller', 'class' => 'required'],
                        'options' => $controllers,
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('action', [
                        'label' => ['text' => 'Azione', 'class' => 'required'],
                        'options' => $actions,
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('display_name', [
                        'label' => 'Nome Visualizzato',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('group_name', [
                        'label' => 'Gruppo',
                        'options' => $groups,
                        'empty' => '-- Seleziona --',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('prefix', [
                        'label' => 'Prefix (opzionale)',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('plugin', [
                        'label' => 'Plugin (opzionale)',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <?= $this->Form->control('description', [
                        'label' => 'Descrizione',
                        'type' => 'textarea',
                        'rows' => 2,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-card">
        <div class="form-card-body">
            <div class="form-actions">
                <div class="btn-group-left">
                    <?= $this->Html->link(__('Annulla'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->postLink(
                        '<i data-lucide="trash-2" style="width:16px;height:16px;"></i> ' . __('Elimina'),
                        ['action' => 'delete', $permission->id],
                        ['confirm' => __('Sei sicuro di voler eliminare questo permesso?'), 'class' => 'btn btn-danger', 'escapeTitle' => false]
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
