<?php
/**
 * Dashboard principale - FatturaCake
 *
 * @var \App\View\AppView $this
 * @var int $fattureEmesseMese
 * @var object $totaleEmessoMese
 * @var int $fattureRicevuteMese
 * @var object $totaleRicevutoMese
 * @var float $variazioneEmesso
 * @var float $variazioneRicevuto
 * @var int $totaleClienti
 * @var int $totaleFornitori
 * @var int $totaleProdotti
 * @var \Cake\ORM\ResultSet $prodottiPerCategoria
 * @var \Cake\ORM\ResultSet $ultimeFattureEmesse
 * @var \Cake\ORM\ResultSet $ultimeFattureRicevute
 * @var int $fattureInAttesaSdi
 * @var array $andamentoMensile
 * @var array $anniDisponibili
 * @var int $annoSelezionato
 * @var string $periodoSelezionato
 * @var int $meseSelezionato
 * @var array $mesi
 * @var string $etichettaPeriodo
 * @var \Cake\I18n\DateTime $inizioPeriodo
 * @var \Cake\I18n\DateTime $finePeriodo
 */

$this->assign('title', 'Dashboard');
$this->Breadcrumbs->add('Dashboard', ['controller' => 'Dashboard', 'action' => 'index']);

// Helper per formattare importi
$formatImporto = function ($valore) {
    return number_format((float)$valore, 2, ',', '.');
};

// Helper per badge variazione
$badgeVariazione = function ($valore) {
    if ($valore > 0) {
        return '<span class="badge bg-success-subtle text-success"><i data-lucide="trending-up" style="width:12px;height:12px;"></i> +' . number_format($valore, 1) . '%</span>';
    } elseif ($valore < 0) {
        return '<span class="badge bg-danger-subtle text-danger"><i data-lucide="trending-down" style="width:12px;height:12px;"></i> ' . number_format($valore, 1) . '%</span>';
    }
    return '<span class="badge bg-secondary-subtle text-secondary">0%</span>';
};
?>

<div class="dashboard">
    <!-- Intestazione -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Dashboard</h2>
            <p class="text-secondary mb-0">
                <i data-lucide="calendar" style="width:14px;height:14px;" class="me-1"></i>
                <?= h($etichettaPeriodo) ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= $this->Url->build(['controller' => 'Fatture', 'action' => 'add']) ?>" class="btn btn-primary">
                <i data-lucide="plus" style="width:16px;height:16px;" class="me-1"></i>
                Nuova Fattura
            </a>
        </div>
    </div>

    <!-- Filtri Periodo -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="get" action="<?= $this->Url->build(['controller' => 'Dashboard', 'action' => 'index']) ?>" id="filtriDashboard" class="row g-2 align-items-end">
                <!-- Anno -->
                <div class="col-auto">
                    <label for="anno" class="form-label small text-secondary mb-1">Anno</label>
                    <select name="anno" id="anno" class="form-select form-select-sm" style="min-width: 100px;">
                        <?php foreach ($anniDisponibili as $annoOpt): ?>
                            <option value="<?= $annoOpt ?>" <?= $annoSelezionato == $annoOpt ? 'selected' : '' ?>><?= $annoOpt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Periodo -->
                <div class="col-auto">
                    <label for="periodo" class="form-label small text-secondary mb-1">Periodo</label>
                    <select name="periodo" id="periodo" class="form-select form-select-sm" style="min-width: 140px;">
                        <option value="mese" <?= $periodoSelezionato === 'mese' ? 'selected' : '' ?>>Mese</option>
                        <optgroup label="Trimestri">
                            <option value="q1" <?= $periodoSelezionato === 'q1' ? 'selected' : '' ?>>Q1 (Gen-Mar)</option>
                            <option value="q2" <?= $periodoSelezionato === 'q2' ? 'selected' : '' ?>>Q2 (Apr-Giu)</option>
                            <option value="q3" <?= $periodoSelezionato === 'q3' ? 'selected' : '' ?>>Q3 (Lug-Set)</option>
                            <option value="q4" <?= $periodoSelezionato === 'q4' ? 'selected' : '' ?>>Q4 (Ott-Dic)</option>
                        </optgroup>
                        <optgroup label="Semestri">
                            <option value="h1" <?= $periodoSelezionato === 'h1' ? 'selected' : '' ?>>1° Semestre</option>
                            <option value="h2" <?= $periodoSelezionato === 'h2' ? 'selected' : '' ?>>2° Semestre</option>
                        </optgroup>
                        <option value="anno" <?= $periodoSelezionato === 'anno' ? 'selected' : '' ?>>Anno intero</option>
                        <option value="custom" <?= $periodoSelezionato === 'custom' ? 'selected' : '' ?>>Personalizzato</option>
                    </select>
                </div>

                <!-- Mese (visibile solo se periodo=mese) -->
                <div class="col-auto" id="meseContainer" style="<?= $periodoSelezionato !== 'mese' ? 'display:none;' : '' ?>">
                    <label for="mese" class="form-label small text-secondary mb-1">Mese</label>
                    <select name="mese" id="mese" class="form-select form-select-sm" style="min-width: 130px;">
                        <?php foreach ($mesi as $num => $nome): ?>
                            <option value="<?= $num ?>" <?= $meseSelezionato == $num ? 'selected' : '' ?>><?= $nome ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date custom (visibile solo se periodo=custom) -->
                <div class="col-auto" id="customDateContainer" style="<?= $periodoSelezionato !== 'custom' ? 'display:none;' : '' ?>">
                    <label for="data_inizio" class="form-label small text-secondary mb-1">Dal</label>
                    <input type="date" name="data_inizio" id="data_inizio" class="form-control form-control-sm"
                           value="<?= $this->request->getQuery('data_inizio', $inizioPeriodo->format('Y-m-d')) ?>">
                </div>
                <div class="col-auto" id="customDateContainer2" style="<?= $periodoSelezionato !== 'custom' ? 'display:none;' : '' ?>">
                    <label for="data_fine" class="form-label small text-secondary mb-1">Al</label>
                    <input type="date" name="data_fine" id="data_fine" class="form-control form-control-sm"
                           value="<?= $this->request->getQuery('data_fine', $finePeriodo->format('Y-m-d')) ?>">
                </div>

                <!-- Bottone Applica -->
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i data-lucide="filter" style="width:14px;height:14px;" class="me-1"></i>
                        Applica
                    </button>
                </div>

                <!-- Reset -->
                <div class="col-auto">
                    <a href="<?= $this->Url->build(['controller' => 'Dashboard', 'action' => 'index']) ?>" class="btn btn-outline-secondary btn-sm">
                        <i data-lucide="x" style="width:14px;height:14px;"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards Statistiche Principali -->
    <div class="row g-3 mb-4">
        <!-- Fatturato Emesso -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2">
                            <i data-lucide="trending-up" class="text-success" style="width:24px;height:24px;"></i>
                        </div>
                        <?= $badgeVariazione($variazioneEmesso) ?>
                    </div>
                    <h3 class="mb-1">&euro; <?= $formatImporto($totaleEmessoMese->totale ?? 0) ?></h3>
                    <p class="text-secondary mb-0 small">Fatturato Emesso</p>
                    <p class="text-secondary mb-0 small"><?= $fattureEmesseMese ?> fatture questo mese</p>
                </div>
            </div>
        </div>

        <!-- Fatturato Ricevuto -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                            <i data-lucide="trending-down" class="text-danger" style="width:24px;height:24px;"></i>
                        </div>
                        <?= $badgeVariazione($variazioneRicevuto) ?>
                    </div>
                    <h3 class="mb-1">&euro; <?= $formatImporto($totaleRicevutoMese->totale ?? 0) ?></h3>
                    <p class="text-secondary mb-0 small">Fatture Ricevute</p>
                    <p class="text-secondary mb-0 small"><?= $fattureRicevuteMese ?> fatture questo mese</p>
                </div>
            </div>
        </div>

        <!-- Clienti -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle bg-info bg-opacity-10 p-2">
                            <i data-lucide="building-2" class="text-info" style="width:24px;height:24px;"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?= $totaleClienti ?></h3>
                    <p class="text-secondary mb-0 small">Clienti Attivi</p>
                    <a href="<?= $this->Url->build(['controller' => 'Anagrafiche', 'action' => 'indexClienti']) ?>" class="small">
                        Vedi tutti <i data-lucide="arrow-right" style="width:12px;height:12px;"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Fornitori -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                            <i data-lucide="truck" class="text-warning" style="width:24px;height:24px;"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?= $totaleFornitori ?></h3>
                    <p class="text-secondary mb-0 small">Fornitori Attivi</p>
                    <a href="<?= $this->Url->build(['controller' => 'Anagrafiche', 'action' => 'indexFornitori']) ?>" class="small">
                        Vedi tutti <i data-lucide="arrow-right" style="width:12px;height:12px;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafico Andamento + Prodotti per Categoria -->
    <div class="row g-3 mb-4">
        <!-- Grafico Andamento Mensile -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">
                        <i data-lucide="bar-chart-3" style="width:18px;height:18px;" class="me-2 text-primary"></i>
                        Andamento <?= $annoSelezionato ?>
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartAndamento" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Prodotti per Categoria -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">
                        <i data-lucide="package" style="width:18px;height:18px;" class="me-2 text-primary"></i>
                        Catalogo Prodotti
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 bg-body-secondary rounded">
                        <span class="fw-semibold">Totale Prodotti/Servizi</span>
                        <span class="badge bg-primary fs-6"><?= $totaleProdotti ?></span>
                    </div>

                    <?php if ($prodottiPerCategoria->count() > 0): ?>
                        <h6 class="text-secondary small mb-2">Per categoria:</h6>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($prodottiPerCategoria as $cat): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span><?= h($cat->categoria ?? 'Senza categoria') ?></span>
                                    <span class="badge bg-secondary"><?= $cat->count ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-secondary small mb-0">Nessun prodotto presente</p>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="<?= $this->Url->build(['controller' => 'Prodotti', 'action' => 'index']) ?>" class="btn btn-outline-primary btn-sm w-100">
                            Gestisci Catalogo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ultime Fatture -->
    <div class="row g-3">
        <!-- Ultime Fatture Emesse -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i data-lucide="file-output" style="width:18px;height:18px;" class="me-2 text-success"></i>
                        Ultime Fatture Emesse
                    </h5>
                    <a href="<?= $this->Url->build(['controller' => 'Fatture', 'action' => 'indexAttive']) ?>" class="btn btn-sm btn-outline-secondary">
                        Vedi tutte
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if ($ultimeFattureEmesse->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Numero</th>
                                        <th>Data</th>
                                        <th class="text-end">Importo</th>
                                        <th>Stato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimeFattureEmesse as $fattura): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= $this->Url->build(['controller' => 'Fatture', 'action' => 'view', $fattura->id]) ?>">
                                                    <?= h($fattura->numero) ?>
                                                </a>
                                            </td>
                                            <td><?= $fattura->data->format('d/m/Y') ?></td>
                                            <td class="text-end fw-semibold">&euro; <?= $formatImporto($fattura->totale_documento) ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = match ($fattura->stato_sdi) {
                                                    'consegnato' => 'bg-success',
                                                    'accettato' => 'bg-success',
                                                    'inviato' => 'bg-info',
                                                    'generato' => 'bg-warning',
                                                    'errore', 'scartato' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= h(ucfirst($fattura->stato_sdi)) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-secondary">
                            <i data-lucide="inbox" style="width:48px;height:48px;" class="mb-2 opacity-50"></i>
                            <p class="mb-0">Nessuna fattura emessa</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ultime Fatture Ricevute -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i data-lucide="file-input" style="width:18px;height:18px;" class="me-2 text-danger"></i>
                        Ultime Fatture Ricevute
                    </h5>
                    <a href="<?= $this->Url->build(['controller' => 'Fatture', 'action' => 'indexPassive']) ?>" class="btn btn-sm btn-outline-secondary">
                        Vedi tutte
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if ($ultimeFattureRicevute->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Numero</th>
                                        <th>Data</th>
                                        <th class="text-end">Importo</th>
                                        <th>Stato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimeFattureRicevute as $fattura): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= $this->Url->build(['controller' => 'Fatture', 'action' => 'view', $fattura->id]) ?>">
                                                    <?= h($fattura->numero) ?>
                                                </a>
                                            </td>
                                            <td><?= $fattura->data->format('d/m/Y') ?></td>
                                            <td class="text-end fw-semibold">&euro; <?= $formatImporto($fattura->totale_documento) ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = match ($fattura->stato_sdi) {
                                                    'consegnato', 'accettato' => 'bg-success',
                                                    'inviato' => 'bg-info',
                                                    'generato' => 'bg-warning',
                                                    'errore', 'scartato' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= h(ucfirst($fattura->stato_sdi)) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-secondary">
                            <i data-lucide="inbox" style="width:48px;height:48px;" class="mb-2 opacity-50"></i>
                            <p class="mb-0">Nessuna fattura ricevuta</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->scriptStart(['block' => true]); ?>
// Gestione filtri periodo
(function() {
    const periodoSelect = document.getElementById('periodo');
    const meseContainer = document.getElementById('meseContainer');
    const customDateContainer = document.getElementById('customDateContainer');
    const customDateContainer2 = document.getElementById('customDateContainer2');

    if (periodoSelect) {
        periodoSelect.addEventListener('change', function() {
            const periodo = this.value;

            // Nascondi tutto
            meseContainer.style.display = 'none';
            customDateContainer.style.display = 'none';
            customDateContainer2.style.display = 'none';

            // Mostra in base al periodo
            if (periodo === 'mese') {
                meseContainer.style.display = '';
            } else if (periodo === 'custom') {
                customDateContainer.style.display = '';
                customDateContainer2.style.display = '';
            }
        });
    }
})();

// Grafico Andamento Mensile con Chart.js
(function() {
    const ctx = document.getElementById('chartAndamento');
    if (!ctx) return;

    // Carica Chart.js se non presente
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
        script.onload = initChart;
        document.head.appendChild(script);
    } else {
        initChart();
    }

    function initChart() {
        const data = <?= json_encode($andamentoMensile) ?>;
        const labels = data.map(d => d.mese);
        const emesso = data.map(d => d.emesso);
        const ricevuto = data.map(d => d.ricevuto);

        // Colori che si adattano al tema
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
        const textColor = isDark ? '#adb5bd' : '#6c757d';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Fatturato Emesso',
                        data: emesso,
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgb(25, 135, 84)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Fatture Ricevute',
                        data: ricevuto,
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgb(220, 53, 69)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': € ' + context.raw.toLocaleString('it-IT', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: textColor
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                return '€ ' + value.toLocaleString('it-IT');
                            }
                        }
                    }
                }
            }
        });
    }

    // Re-render icons after page load
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 100);
})();
<?php $this->Html->scriptEnd(); ?>
