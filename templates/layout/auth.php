<?php
/**
 * Auth Layout - FatturaCake
 * Modern wide layout for login/authentication pages
 *
 * @var \App\View\AppView $this
 * @var string $content
 */

use Cake\Core\Configure;

$appName = Configure::read('App.name', 'FatturaCake');
?>
<!DOCTYPE html>
<html lang="it" data-bs-theme="light" data-color="sky">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->fetch('title') ?> - <?= h($appName) ?></title>
    <?= $this->Html->meta('icon') ?>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?= $this->Html->css('admin') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .auth-container {
            min-height: 100vh;
            display: flex;
        }
        .auth-brand {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #1e3a5f 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .auth-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }
        .auth-brand-content {
            position: relative;
            z-index: 1;
        }
        .auth-logo {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        .auth-logo i {
            color: white;
            width: 40px;
            height: 40px;
        }
        .auth-brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .auth-brand p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2.5rem;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .feature-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.15);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        .feature-icon i {
            width: 22px;
            height: 22px;
        }
        .feature-text h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .feature-text p {
            font-size: 0.875rem;
            opacity: 0.8;
            margin: 0;
        }
        .auth-form-side {
            background: var(--bs-body-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .auth-form-container {
            width: 100%;
            max-width: 440px;
        }
        .auth-form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .auth-form-header p {
            color: var(--bs-secondary);
        }
        .form-floating > label {
            color: var(--bs-secondary);
        }
        .btn-auth {
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
        }
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--bs-border-color);
        }
        @media (max-width: 991.98px) {
            .auth-brand {
                display: none;
            }
            .auth-form-side {
                padding: 1.5rem;
            }
            .auth-form-container {
                max-width: 100%;
            }
        }
    </style>

    <script>
        (function() {
            const getSystemTheme = () => matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = localStorage.getItem('theme') || 'system';
            const color = localStorage.getItem('color') || 'sky';
            const html = document.documentElement;
            html.setAttribute('data-bs-theme', theme === 'system' ? getSystemTheme() : theme);
            html.setAttribute('data-color', color);
        })();
    </script>
</head>
<body>
    <div class="auth-container">
        <!-- Brand Side -->
        <div class="auth-brand col-lg-6 d-none d-lg-flex">
            <div class="auth-brand-content">
                <h1><?= h($appName) ?></h1>
                <p>La soluzione completa per la gestione delle tue fatture elettroniche.</p>

                <div class="features mt-5">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i data-lucide="zap"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Invio automatico SDI</h4>
                            <p>Genera e invia fatture elettroniche in pochi click</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i data-lucide="shield-check"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Conforme alle normative</h4>
                            <p>Sempre aggiornato con le ultime specifiche AdE</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i data-lucide="bar-chart-3"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Dashboard intuitiva</h4>
                            <p>Monitora fatturato e statistiche in tempo reale</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i data-lucide="cloud"></i>
                        </div>
                        <div class="feature-text">
                            <h4>Cloud sicuro</h4>
                            <p>I tuoi dati sempre protetti e accessibili ovunque</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Side -->
        <div class="auth-form-side col-12 col-lg-6">
            <div class="auth-form-container">
                <?= $this->Flash->render() ?>
                <?= $this->fetch('content') ?>

                <div class="auth-footer">
                    <small class="text-secondary">
                        &copy; <?= date('Y') ?> <?= h($appName) ?> - Tutti i diritti riservati
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>

    <?= $this->fetch('script') ?>
</body>
</html>
