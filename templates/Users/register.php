<?php
/**
 * Register Page
 *
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Registrati');
?>

<h2 class="h5 text-center mb-4">Crea il tuo account</h2>

<?= $this->Form->create(null, ['class' => 'needs-validation', 'novalidate' => true]) ?>

<p class="text-secondary small mb-3">Dati Azienda</p>

<div class="mb-3">
    <div class="form-floating">
        <input type="text" class="form-control" id="nome_azienda" name="nome_azienda" placeholder=" " required>
        <label for="nome_azienda">Nome Azienda *</label>
    </div>
</div>

<div class="mb-3">
    <div class="form-floating">
        <input type="text" class="form-control" id="partita_iva" name="partita_iva" placeholder=" " maxlength="11" pattern="[0-9]{11}">
        <label for="partita_iva">Partita IVA</label>
    </div>
</div>

<hr class="my-3">
<p class="text-secondary small mb-3">Dati Utente</p>

<div class="row g-2 mb-3">
    <div class="col-6">
        <div class="form-floating">
            <input type="text" class="form-control" id="nome" name="nome" placeholder=" ">
            <label for="nome">Nome</label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-floating">
            <input type="text" class="form-control" id="cognome" name="cognome" placeholder=" ">
            <label for="cognome">Cognome</label>
        </div>
    </div>
</div>

<div class="mb-3">
    <div class="form-floating">
        <input type="email" class="form-control" id="email" name="email" placeholder=" " required>
        <label for="email">Email *</label>
    </div>
</div>

<div class="mb-3">
    <div class="form-floating">
        <input type="password" class="form-control" id="password" name="password" placeholder=" " required minlength="8">
        <label for="password">Password *</label>
    </div>
    <small class="text-muted">Minimo 8 caratteri</small>
</div>

<div class="mb-4">
    <div class="form-floating">
        <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder=" " required minlength="8">
        <label for="password_confirm">Conferma Password *</label>
    </div>
</div>

<div class="d-grid">
    <?= $this->Form->button(__('Registrati'), [
        'class' => 'btn btn-primary btn-auth',
        'type' => 'submit',
    ]) ?>
</div>

<?= $this->Form->end() ?>

<div class="text-center mt-4">
    <small class="text-secondary d-block mb-2">
        <i data-lucide="gift" style="width:14px;height:14px;" class="me-1"></i>
        30 giorni di prova gratuita
    </small>
    <span class="small text-secondary">Hai già un account?</span>
    <?= $this->Html->link(
        __('Accedi'),
        ['action' => 'login'],
        ['class' => 'small']
    ) ?>
</div>

<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    const form = document.querySelector('.needs-validation');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');

    // Password match validation
    passwordConfirm.addEventListener('input', function() {
        if (password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Le password non coincidono');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    });

    password.addEventListener('input', function() {
        if (passwordConfirm.value && password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Le password non coincidono');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    });

    // Form validation
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
