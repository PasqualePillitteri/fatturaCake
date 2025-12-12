<?php
/**
 * Forgot Password Page
 *
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Recupera Password');
?>

<h2 class="h5 text-center mb-4">Recupera la tua password</h2>
<p class="text-secondary text-center small mb-4">
    Inserisci la tua email e ti invieremo un link per reimpostare la password.
</p>

<?= $this->Form->create(null, ['class' => 'needs-validation', 'novalidate' => true]) ?>

<div class="mb-4">
    <div class="form-floating">
        <input type="email" class="form-control" id="email" name="email" placeholder=" " required autofocus>
        <label for="email">Email</label>
    </div>
</div>

<div class="d-grid gap-2">
    <?= $this->Form->button(__('Invia link di recupero'), [
        'class' => 'btn btn-primary btn-auth',
        'type' => 'submit',
    ]) ?>
</div>

<?= $this->Form->end() ?>

<div class="text-center mt-4">
    <?= $this->Html->link(
        '<i data-lucide="arrow-left" style="width:14px;height:14px;" class="me-1"></i>' . __('Torna al login'),
        ['action' => 'login'],
        ['class' => 'text-decoration-none small', 'escape' => false]
    ) ?>
</div>

<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
<?php $this->end(); ?>
