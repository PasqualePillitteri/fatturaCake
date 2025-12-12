<?php
/**
 * Login Page
 *
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Accedi');
?>

<div class="auth-form-header">
    <h2>Bentornato!</h2>
    <p>Inserisci le tue credenziali per accedere</p>
</div>

<?= $this->Form->create(null, ['class' => 'needs-validation', 'novalidate' => true]) ?>

<div class="mb-3">
    <div class="form-floating">
        <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder=" " required autofocus>
        <label for="email">
            <i data-lucide="mail" style="width:16px;height:16px;" class="me-2"></i>Email
        </label>
    </div>
</div>

<div class="mb-3">
    <div class="form-floating">
        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder=" " required>
        <label for="password">
            <i data-lucide="lock" style="width:16px;height:16px;" class="me-2"></i>Password
        </label>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="form-check">
        <?= $this->Form->checkbox('remember_me', [
            'class' => 'form-check-input',
            'id' => 'remember_me',
        ]) ?>
        <label class="form-check-label small" for="remember_me">
            Ricordami
        </label>
    </div>
    <?= $this->Html->link(
        __('Password dimenticata?'),
        ['action' => 'forgotPassword'],
        ['class' => 'text-decoration-none small']
    ) ?>
</div>

<div class="d-grid mb-4">
    <?= $this->Form->button(__('Accedi'), [
        'class' => 'btn btn-primary btn-auth btn-lg',
        'type' => 'submit',
    ]) ?>
</div>

<?= $this->Form->end() ?>

<div class="text-center">
    <span class="text-secondary">Non hai un account?</span>
    <?= $this->Html->link(
        __('Registrati gratis'),
        ['action' => 'register'],
        ['class' => 'fw-semibold text-decoration-none ms-1']
    ) ?>
</div>

<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    // Form validation
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
