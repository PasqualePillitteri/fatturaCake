<?php
/**
 * Password Reset Email Template
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var string $resetUrl
 */
?>
<div style="font-family: 'Inter', Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #0ea5e9; margin: 0;">FatturaCake</h1>
        <p style="color: #64748b; margin-top: 5px;">Gestione Fatture Elettroniche</p>
    </div>

    <div style="background: #f8fafc; border-radius: 8px; padding: 30px; margin-bottom: 20px;">
        <h2 style="color: #1e293b; margin-top: 0;">Reimposta la tua password</h2>

        <p style="color: #475569;">Ciao <?= h($user->nome ?: $user->username) ?>,</p>

        <p style="color: #475569;">
            Abbiamo ricevuto una richiesta per reimpostare la password del tuo account.
            Clicca sul pulsante qui sotto per procedere:
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= h($resetUrl) ?>"
               style="display: inline-block; background: #0ea5e9; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                Reimposta Password
            </a>
        </div>

        <p style="color: #475569; font-size: 14px;">
            Oppure copia e incolla questo link nel tuo browser:<br>
            <a href="<?= h($resetUrl) ?>" style="color: #0ea5e9; word-break: break-all;">
                <?= h($resetUrl) ?>
            </a>
        </p>

        <p style="color: #64748b; font-size: 13px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <strong>Nota:</strong> Questo link scade tra 2 ore.<br>
            Se non hai richiesto tu il reset della password, puoi ignorare questa email.
        </p>
    </div>

    <div style="text-align: center; color: #94a3b8; font-size: 12px;">
        <p>&copy; <?= date('Y') ?> FatturaCake - Tutti i diritti riservati</p>
    </div>
</div>
