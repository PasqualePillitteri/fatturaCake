<?php
/**
 * Reset Password Page
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $token
 */

$this->assign('title', 'Nuova Password');
?>

<h2 class="h5 text-center mb-4">Crea una nuova password</h2>
<p class="text-secondary text-center small mb-4">
    Inserisci la tua nuova password per l'account <?= h($user->email) ?>
</p>

<?= $this->Form->create(null, ['class' => 'needs-validation', 'novalidate' => true]) ?>

<div class="mb-3">
    <div class="form-floating">
        <input type="password" class="form-control" id="password" name="password" placeholder=" " required minlength="8">
        <label for="password">Nuova Password</label>
    </div>
    <div class="form-text small">Minimo 8 caratteri</div>
</div>

<div class="mb-4">
    <div class="form-floating">
        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder=" " required>
        <label for="password_confirm">Conferma Password</label>
    </div>
</div>

<div class="d-grid gap-2">
    <?= $this->Form->button(__('Reimposta Password'), [
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
    const password = document.getElementById('password');
    const confirm = document.getElementById('password_confirm');

    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (password.value !== confirm.value) {
            confirm.setCustomValidity('Le password non coincidono');
            event.preventDefault();
            event.stopPropagation();
        } else {
            confirm.setCustomValidity('');
        }

        form.classList.add('was-validated');
    });

    confirm.addEventListener('input', function() {
        if (password.value !== confirm.value) {
            confirm.setCustomValidity('Le password non coincidono');
        } else {
            confirm.setCustomValidity('');
        }
    });
});
</script>
<?php $this->end(); ?>
