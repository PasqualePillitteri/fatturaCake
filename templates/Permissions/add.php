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
        <h3><?= __('Nuovo Permesso') ?></h3>
        <div class="header-actions">
            <?= $this->Html->link(
                '<i data-lucide="arrow-left" style="width:16px;height:16px;"></i> ' . __('Torna alla lista'),
                ['action' => 'index'],
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
                        'empty' => '-- Seleziona --',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('action', [
                        'label' => ['text' => 'Azione', 'class' => 'required'],
                        'options' => $actions,
                        'empty' => '-- Seleziona --',
                    ]) ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-half">
                    <?= $this->Form->control('display_name', [
                        'label' => 'Nome Visualizzato',
                        'placeholder' => 'es. Fatture: Visualizza elenco',
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
                        'placeholder' => 'es. Admin, Api',
                    ]) ?>
                </div>
                <div class="form-col-half">
                    <?= $this->Form->control('plugin', [
                        'label' => 'Plugin (opzionale)',
                        'placeholder' => 'es. DebugKit',
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
                </div>
                <div class="btn-group-right">
                    <?= $this->Form->button(
                        '<i data-lucide="save" style="width:16px;height:16px;"></i> ' . __('Salva'),
                        ['class' => 'btn btn-success', 'escapeTitle' => false]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->Form->end() ?>
</div>
