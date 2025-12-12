<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\LogAttivitum $logAttivitum
 * @var string[]|\Cake\Collection\CollectionInterface $tenants
 * @var string[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $logAttivitum->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $logAttivitum->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Log Attivita'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="logAttivita form content">
            <?= $this->Form->create($logAttivitum) ?>
            <fieldset>
                <legend><?= __('Edit Log Attivitum') ?></legend>
                <?php
                    echo $this->Form->control('tenant_id', ['options' => $tenants, 'empty' => true]);
                    echo $this->Form->control('user_id', ['options' => $users, 'empty' => true]);
                    echo $this->Form->control('azione');
                    echo $this->Form->control('modello');
                    echo $this->Form->control('modello_id');
                    echo $this->Form->control('dati_precedenti');
                    echo $this->Form->control('dati_nuovi');
                    echo $this->Form->control('ip_address');
                    echo $this->Form->control('user_agent');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
