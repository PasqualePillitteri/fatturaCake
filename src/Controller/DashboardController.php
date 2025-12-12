<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\DateTime;

/**
 * Dashboard Controller
 *
 * Controller per la dashboard principale dell'applicazione.
 */
class DashboardController extends AppController
{
    /**
     * Index method - Dashboard principale
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();

        // Check if setup wizard is needed
        if ($this->needsSetupWizard()) {
            return $this->redirect(['controller' => 'SetupWizard', 'action' => 'index']);
        }

        $fattureTable = $this->fetchTable('Fatture');

        // === FILTRI ===
        $annoCorrente = (int) DateTime::now()->format('Y');
        $meseCorrente = (int) DateTime::now()->format('n');

        // Ottieni anni disponibili dalle fatture
        $anniQuery = $fattureTable->find()
            ->select(['anno' => 'YEAR(data)'])
            ->distinct()
            ->orderByDesc('anno')
            ->all();

        $anniDisponibili = [];
        foreach ($anniQuery as $row) {
            if (!empty($row->anno)) {
                $anniDisponibili[] = (int) $row->anno;
            }
        }

        // Se non ci sono fatture, mostra almeno l'anno corrente
        if (empty($anniDisponibili)) {
            $anniDisponibili = [$annoCorrente];
        }

        // Assicura che l'anno corrente sia sempre presente
        if (!in_array($annoCorrente, $anniDisponibili)) {
            array_unshift($anniDisponibili, $annoCorrente);
        }

        // Leggi parametri filtro dalla query string
        // Default: anno intero invece del mese corrente
        $annoSelezionato = (int) $this->request->getQuery('anno', $annoCorrente);
        $periodoSelezionato = $this->request->getQuery('periodo', 'anno');
        $meseSelezionato = (int) $this->request->getQuery('mese', $meseCorrente);
        $dataInizioCustom = $this->request->getQuery('data_inizio');
        $dataFineCustom = $this->request->getQuery('data_fine');

        // Calcola date inizio/fine in base al periodo selezionato
        [$inizioPeriodo, $finePeriodo, $etichettaPeriodo] = $this->calcolaDatePeriodo(
            $annoSelezionato,
            $periodoSelezionato,
            $meseSelezionato,
            $dataInizioCustom,
            $dataFineCustom
        );

        // Calcola date periodo precedente per confronto
        [$inizioPeriodoPrec, $finePeriodoPrec] = $this->calcolaDatePeriodoPrecedente(
            $annoSelezionato,
            $periodoSelezionato,
            $meseSelezionato,
            $inizioPeriodo,
            $finePeriodo
        );

        // Usa le variabili calcolate
        $inizioMese = $inizioPeriodo;
        $fineMese = $finePeriodo;
        $inizioMeseScorso = $inizioPeriodoPrec;
        $fineMeseScorso = $finePeriodoPrec;

        // Carica i modelli necessari
        $anagraficheTable = $this->fetchTable('Anagrafiche');
        $prodottiTable = $this->fetchTable('Prodotti');
        $categorieTable = $this->fetchTable('CategorieProdotti');

        // === STATISTICHE FATTURE MESE CORRENTE ===

        // Fatture emesse (attive) - mese corrente
        $fattureEmesseMese = $fattureTable->find()
            ->where([
                'direzione' => 'emessa',
                'data >=' => $inizioMese,
                'data <=' => $fineMese,
            ])
            ->count();

        $totaleEmessoMese = $fattureTable->find()
            ->where([
                'direzione' => 'emessa',
                'data >=' => $inizioMese,
                'data <=' => $fineMese,
            ])
            ->select(['totale' => $fattureTable->find()->func()->sum('totale_documento')])
            ->first();

        // Fatture ricevute (passive) - mese corrente
        $fattureRicevuteMese = $fattureTable->find()
            ->where([
                'direzione' => 'ricevuta',
                'data >=' => $inizioMese,
                'data <=' => $fineMese,
            ])
            ->count();

        $totaleRicevutoMese = $fattureTable->find()
            ->where([
                'direzione' => 'ricevuta',
                'data >=' => $inizioMese,
                'data <=' => $fineMese,
            ])
            ->select(['totale' => $fattureTable->find()->func()->sum('totale_documento')])
            ->first();

        // === STATISTICHE MESE SCORSO (per confronto) ===

        $totaleEmessoMeseScorso = $fattureTable->find()
            ->where([
                'direzione' => 'emessa',
                'data >=' => $inizioMeseScorso,
                'data <=' => $fineMeseScorso,
            ])
            ->select(['totale' => $fattureTable->find()->func()->sum('totale_documento')])
            ->first();

        $totaleRicevutoMeseScorso = $fattureTable->find()
            ->where([
                'direzione' => 'ricevuta',
                'data >=' => $inizioMeseScorso,
                'data <=' => $fineMeseScorso,
            ])
            ->select(['totale' => $fattureTable->find()->func()->sum('totale_documento')])
            ->first();

        // === CONTATORI ANAGRAFICHE ===

        $totaleClienti = $anagraficheTable->find()
            ->where(['tipo' => 'cliente', 'is_active' => true])
            ->count();

        $totaleFornitori = $anagraficheTable->find()
            ->where(['tipo' => 'fornitore', 'is_active' => true])
            ->count();

        // === CONTATORI PRODOTTI ===

        $totaleProdotti = $prodottiTable->find()
            ->where(['is_active' => true])
            ->count();

        $prodottiPerCategoria = $prodottiTable->find()
            ->select([
                'categoria' => 'Categorias.nome',
                'count' => $prodottiTable->find()->func()->count('Prodotti.id'),
            ])
            ->leftJoinWith('Categorias')
            ->where(['Prodotti.is_active' => true])
            ->group(['Categorias.id', 'Categorias.nome'])
            ->orderByDesc('count')
            ->limit(5)
            ->all();

        // === ULTIME FATTURE ===

        $ultimeFattureEmesse = $fattureTable->find()
            ->where(['Fatture.direzione' => 'emessa'])
            ->orderByDesc('Fatture.data')
            ->orderByDesc('Fatture.created')
            ->limit(5)
            ->all();

        $ultimeFattureRicevute = $fattureTable->find()
            ->where(['Fatture.direzione' => 'ricevuta'])
            ->orderByDesc('Fatture.data')
            ->orderByDesc('Fatture.created')
            ->limit(5)
            ->all();

        // === FATTURE IN ATTESA SDI ===

        $fattureInAttesaSdi = $fattureTable->find()
            ->where([
                'direzione' => 'emessa',
                'stato_sdi IN' => ['bozza', 'generata', 'inviata'],
            ])
            ->count();

        // === ANDAMENTO MENSILE (12 mesi dell'anno selezionato) ===

        $andamentoMensile = [];
        for ($m = 1; $m <= 12; $m++) {
            $dataInizio = DateTime::create($annoSelezionato, $m, 1)->startOfMonth();
            $dataFine = DateTime::create($annoSelezionato, $m, 1)->endOfMonth();
            $meseLabel = $dataInizio->i18nFormat('MMM');

            $emesso = $fattureTable->find()
                ->where([
                    'direzione' => 'emessa',
                    'data >=' => $dataInizio,
                    'data <=' => $dataFine,
                ])
                ->select(['totale' => $fattureTable->find()->func()->sum('totale_documento')])
                ->first();

            $ricevuto = $fattureTable->find()
                ->where([
                    'direzione' => 'ricevuta',
                    'data >=' => $dataInizio,
                    'data <=' => $dataFine,
                ])
                ->select(['totale' => $fattureTable->find()->func()->sum('totale_documento')])
                ->first();

            $andamentoMensile[] = [
                'mese' => $meseLabel,
                'emesso' => (float)($emesso->totale ?? 0),
                'ricevuto' => (float)($ricevuto->totale ?? 0),
            ];
        }

        // Calcola variazioni percentuali
        $variazioneEmesso = 0;
        $variazioneRicevuto = 0;

        $emessoCorrente = (float)($totaleEmessoMese->totale ?? 0);
        $emessoScorso = (float)($totaleEmessoMeseScorso->totale ?? 0);
        $ricevutoCorrente = (float)($totaleRicevutoMese->totale ?? 0);
        $ricevutoScorso = (float)($totaleRicevutoMeseScorso->totale ?? 0);

        if ($emessoScorso > 0) {
            $variazioneEmesso = (($emessoCorrente - $emessoScorso) / $emessoScorso) * 100;
        }
        if ($ricevutoScorso > 0) {
            $variazioneRicevuto = (($ricevutoCorrente - $ricevutoScorso) / $ricevutoScorso) * 100;
        }

        // Mesi per select
        $mesi = [
            1 => 'Gennaio',
            2 => 'Febbraio',
            3 => 'Marzo',
            4 => 'Aprile',
            5 => 'Maggio',
            6 => 'Giugno',
            7 => 'Luglio',
            8 => 'Agosto',
            9 => 'Settembre',
            10 => 'Ottobre',
            11 => 'Novembre',
            12 => 'Dicembre',
        ];

        // Passa i dati alla view
        $this->set(compact(
            'fattureEmesseMese',
            'totaleEmessoMese',
            'fattureRicevuteMese',
            'totaleRicevutoMese',
            'variazioneEmesso',
            'variazioneRicevuto',
            'totaleClienti',
            'totaleFornitori',
            'totaleProdotti',
            'prodottiPerCategoria',
            'ultimeFattureEmesse',
            'ultimeFattureRicevute',
            'fattureInAttesaSdi',
            'andamentoMensile',
            // Filtri
            'anniDisponibili',
            'annoSelezionato',
            'periodoSelezionato',
            'meseSelezionato',
            'mesi',
            'etichettaPeriodo',
            'inizioPeriodo',
            'finePeriodo'
        ));

        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Calcola le date di inizio e fine in base al periodo selezionato
     *
     * @param int $anno Anno selezionato
     * @param string $periodo Tipo di periodo (mese, q1-q4, h1-h2, anno, custom)
     * @param int $mese Mese selezionato (per periodo=mese)
     * @param string|null $dataInizio Data inizio personalizzata
     * @param string|null $dataFine Data fine personalizzata
     * @return array [DateTime $inizio, DateTime $fine, string $etichetta]
     */
    private function calcolaDatePeriodo(
        int $anno,
        string $periodo,
        int $mese,
        ?string $dataInizio,
        ?string $dataFine
    ): array {
        $mesiNomi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo',
            4 => 'Aprile', 5 => 'Maggio', 6 => 'Giugno',
            7 => 'Luglio', 8 => 'Agosto', 9 => 'Settembre',
            10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre',
        ];

        switch ($periodo) {
            case 'q1':
                $inizio = DateTime::create($anno, 1, 1)->startOfDay();
                $fine = DateTime::create($anno, 3, 31)->endOfDay();
                $etichetta = "Q1 {$anno} (Gen-Mar)";
                break;

            case 'q2':
                $inizio = DateTime::create($anno, 4, 1)->startOfDay();
                $fine = DateTime::create($anno, 6, 30)->endOfDay();
                $etichetta = "Q2 {$anno} (Apr-Giu)";
                break;

            case 'q3':
                $inizio = DateTime::create($anno, 7, 1)->startOfDay();
                $fine = DateTime::create($anno, 9, 30)->endOfDay();
                $etichetta = "Q3 {$anno} (Lug-Set)";
                break;

            case 'q4':
                $inizio = DateTime::create($anno, 10, 1)->startOfDay();
                $fine = DateTime::create($anno, 12, 31)->endOfDay();
                $etichetta = "Q4 {$anno} (Ott-Dic)";
                break;

            case 'h1':
                $inizio = DateTime::create($anno, 1, 1)->startOfDay();
                $fine = DateTime::create($anno, 6, 30)->endOfDay();
                $etichetta = "1° Semestre {$anno}";
                break;

            case 'h2':
                $inizio = DateTime::create($anno, 7, 1)->startOfDay();
                $fine = DateTime::create($anno, 12, 31)->endOfDay();
                $etichetta = "2° Semestre {$anno}";
                break;

            case 'anno':
                $inizio = DateTime::create($anno, 1, 1)->startOfDay();
                $fine = DateTime::create($anno, 12, 31)->endOfDay();
                $etichetta = "Anno {$anno}";
                break;

            case 'custom':
                if ($dataInizio && $dataFine) {
                    $inizio = DateTime::parse($dataInizio)->startOfDay();
                    $fine = DateTime::parse($dataFine)->endOfDay();
                    $etichetta = $inizio->format('d/m/Y') . ' - ' . $fine->format('d/m/Y');
                } else {
                    // Fallback al mese corrente
                    $inizio = DateTime::now()->startOfMonth();
                    $fine = DateTime::now()->endOfMonth();
                    $etichetta = $mesiNomi[(int)$inizio->format('n')] . ' ' . $inizio->format('Y');
                }
                break;

            case 'mese':
            default:
                $inizio = DateTime::create($anno, $mese, 1)->startOfMonth();
                $fine = DateTime::create($anno, $mese, 1)->endOfMonth();
                $etichetta = $mesiNomi[$mese] . ' ' . $anno;
                break;
        }

        return [$inizio, $fine, $etichetta];
    }

    /**
     * Calcola le date del periodo precedente per il confronto
     *
     * @param int $anno Anno selezionato
     * @param string $periodo Tipo di periodo
     * @param int $mese Mese selezionato
     * @param DateTime $inizioCorrente Inizio periodo corrente
     * @param DateTime $fineCorrente Fine periodo corrente
     * @return array [DateTime $inizio, DateTime $fine]
     */
    private function calcolaDatePeriodoPrecedente(
        int $anno,
        string $periodo,
        int $mese,
        DateTime $inizioCorrente,
        DateTime $fineCorrente
    ): array {
        switch ($periodo) {
            case 'q1':
                // Q1 anno precedente
                return [
                    DateTime::create($anno - 1, 1, 1)->startOfDay(),
                    DateTime::create($anno - 1, 3, 31)->endOfDay(),
                ];

            case 'q2':
                // Q1 stesso anno
                return [
                    DateTime::create($anno, 1, 1)->startOfDay(),
                    DateTime::create($anno, 3, 31)->endOfDay(),
                ];

            case 'q3':
                // Q2 stesso anno
                return [
                    DateTime::create($anno, 4, 1)->startOfDay(),
                    DateTime::create($anno, 6, 30)->endOfDay(),
                ];

            case 'q4':
                // Q3 stesso anno
                return [
                    DateTime::create($anno, 7, 1)->startOfDay(),
                    DateTime::create($anno, 9, 30)->endOfDay(),
                ];

            case 'h1':
                // H1 anno precedente
                return [
                    DateTime::create($anno - 1, 1, 1)->startOfDay(),
                    DateTime::create($anno - 1, 6, 30)->endOfDay(),
                ];

            case 'h2':
                // H1 stesso anno
                return [
                    DateTime::create($anno, 1, 1)->startOfDay(),
                    DateTime::create($anno, 6, 30)->endOfDay(),
                ];

            case 'anno':
                // Anno precedente
                return [
                    DateTime::create($anno - 1, 1, 1)->startOfDay(),
                    DateTime::create($anno - 1, 12, 31)->endOfDay(),
                ];

            case 'custom':
                // Periodo precedente della stessa durata
                $durata = $inizioCorrente->diff($fineCorrente);
                $finePrec = $inizioCorrente->modify('-1 day')->endOfDay();
                $inizioPrec = (clone $finePrec)->sub($durata)->startOfDay();

                return [$inizioPrec, $finePrec];

            case 'mese':
            default:
                // Stesso mese anno precedente
                return [
                    DateTime::create($anno - 1, $mese, 1)->startOfMonth(),
                    DateTime::create($anno - 1, $mese, 1)->endOfMonth(),
                ];
        }
    }

    /**
     * Check if setup wizard is needed for current tenant.
     *
     * @return bool
     */
    private function needsSetupWizard(): bool
    {
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return false;
        }

        $tenantId = $identity->get('tenant_id');
        if (!$tenantId) {
            return false;
        }

        // Check if wizard already completed
        $tenantsTable = $this->fetchTable('Tenants');
        $tenant = $tenantsTable->find()
            ->where(['id' => $tenantId])
            ->first();

        if (!$tenant || $tenant->wizard_completed) {
            return false;
        }

        // Check if tenant has no products, categories, and listini
        $prodotti = $this->fetchTable('Prodotti')->find()->count();
        $categorie = $this->fetchTable('CategorieProdotti')->find()->count();
        $listini = $this->fetchTable('Listini')->find()->count();

        return $prodotti === 0 && $categorie === 0 && $listini === 0;
    }
}
