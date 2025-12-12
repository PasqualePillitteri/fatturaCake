<?php
declare(strict_types=1);

namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Excel Import Service
 *
 * Handles parsing, validation and import of invoices from Excel files.
 */
class ExcelImportService
{
    /**
     * Required columns for Fatture sheet.
     */
    protected const FATTURE_REQUIRED = [
        'numero',
        'data_emissione',
        'tipo_documento',
        'cliente_denominazione',
        'importo_totale',
        'imponibile_totale',
        'imposta_totale',
    ];

    /**
     * Required columns for Righe sheet.
     */
    protected const RIGHE_REQUIRED = [
        'numero_fattura',
        'numero_riga',
        'descrizione',
        'quantita',
        'prezzo_unitario',
        'aliquota_iva',
        'importo_riga',
    ];

    /**
     * Valid tipo documento codes.
     */
    protected const VALID_TIPO_DOC = ['TD01', 'TD02', 'TD03', 'TD04', 'TD05', 'TD06', 'TD24', 'TD25'];

    /**
     * Parsed data.
     *
     * @var array
     */
    protected array $data = [
        'fatture' => [],
        'righe' => [],
    ];

    /**
     * Validation errors.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Warnings (non-blocking).
     *
     * @var array
     */
    protected array $warnings = [];

    /**
     * Import statistics.
     *
     * @var array
     */
    protected array $stats = [
        'fatture_create' => 0,
        'righe_create' => 0,
        'anagrafiche_create' => 0,
        'prodotti_create' => 0,
        'errors' => 0,
    ];

    /**
     * Tenant ID for import.
     *
     * @var int|null
     */
    protected ?int $tenantId = null;

    /**
     * Parse Excel file.
     *
     * @param string $filepath Path to the uploaded file.
     * @return bool True if parsing was successful.
     */
    public function parseFile(string $filepath): bool
    {
        try {
            $spreadsheet = IOFactory::load($filepath);

            // Parse Fatture sheet
            $fattureSheet = $spreadsheet->getSheetByName('Fatture');
            if (!$fattureSheet) {
                $this->errors[] = 'Foglio "Fatture" non trovato nel file.';

                return false;
            }
            $this->data['fatture'] = $this->parseSheet($fattureSheet);

            // Parse Righe sheet
            $righeSheet = $spreadsheet->getSheetByName('Righe');
            if (!$righeSheet) {
                $this->errors[] = 'Foglio "Righe" non trovato nel file.';

                return false;
            }
            $this->data['righe'] = $this->parseSheet($righeSheet);

            return true;
        } catch (\Exception $e) {
            $this->errors[] = 'Errore lettura file: ' . $e->getMessage();
            Log::error('Excel import parse error: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Parse a single sheet into array of rows.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet.
     * @return array Parsed rows.
     */
    protected function parseSheet($sheet): array
    {
        $data = [];
        $headers = [];
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Get headers from first row
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $value = $sheet->getCell($col . '1')->getValue();
            if ($value) {
                // Remove asterisk from required field markers and normalize
                $headerName = strtolower(str_replace(['*', ' '], ['', '_'], trim((string)$value)));
                $headers[$col] = $headerName;
            }
        }

        // Store headers for validation
        $data['_headers'] = array_values($headers);

        // Parse data rows (skip header and example row)
        for ($row = 3; $row <= $highestRow; $row++) {
            $rowData = [];
            $hasData = false;

            foreach ($headers as $col => $header) {
                $cell = $sheet->getCell($col . $row);
                $value = $cell->getValue();

                // Handle date cells
                if ($header === 'data_emissione' && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                    $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
                }

                $rowData[$header] = $value !== null ? trim((string)$value) : '';

                if ($value !== null && $value !== '') {
                    $hasData = true;
                }
            }

            // Only add rows that have data
            if ($hasData) {
                $rowData['_row'] = $row;
                $data[] = $rowData;
            }
        }

        return $data;
    }

    /**
     * Check if sheet has required columns.
     *
     * @param array $headers Headers from sheet.
     * @param array $required Required column names.
     * @param string $sheetName Sheet name for error messages.
     * @return bool
     */
    protected function checkRequiredColumns(array $headers, array $required, string $sheetName): bool
    {
        $missing = array_diff($required, $headers);
        if (!empty($missing)) {
            $this->errors[] = "Foglio '{$sheetName}': colonne obbligatorie mancanti: " . implode(', ', $missing) .
                ". Assicurati di usare il template di import corretto.";
            return false;
        }
        return true;
    }

    /**
     * Validate parsed data.
     *
     * @return bool True if validation passed.
     */
    public function validate(): bool
    {
        $this->errors = [];
        $this->warnings = [];

        // Check that required columns exist in Fatture sheet
        $fattureHeaders = $this->data['fatture']['_headers'] ?? [];
        if (!empty($fattureHeaders)) {
            $this->checkRequiredColumns($fattureHeaders, self::FATTURE_REQUIRED, 'Fatture');
        }

        // Check that required columns exist in Righe sheet
        $righeHeaders = $this->data['righe']['_headers'] ?? [];
        if (!empty($righeHeaders)) {
            $this->checkRequiredColumns($righeHeaders, self::RIGHE_REQUIRED, 'Righe');
        }

        // Filter out metadata entries (entries starting with _)
        $fatture = array_filter($this->data['fatture'], fn($key) => !is_string($key) || !str_starts_with($key, '_'), ARRAY_FILTER_USE_KEY);
        $righe = array_filter($this->data['righe'], fn($key) => !is_string($key) || !str_starts_with($key, '_'), ARRAY_FILTER_USE_KEY);

        // Validate fatture
        foreach ($fatture as $index => $fattura) {
            if (is_array($fattura)) {
                $this->validateFattura($fattura, $index);
            }
        }

        // Validate righe
        $fattureNumeri = array_column($fatture, 'numero');
        foreach ($righe as $index => $riga) {
            if (is_array($riga)) {
                $this->validateRiga($riga, $index, $fattureNumeri);
            }
        }

        // Check that each fattura has at least one riga
        foreach ($fatture as $fattura) {
            if (!is_array($fattura)) {
                continue;
            }
            $fatturaNumero = $fattura['numero'] ?? '';
            $fatturaRow = $fattura['_row'] ?? '?';
            if (empty($fatturaNumero)) {
                continue;
            }
            $righeCount = count(array_filter($righe, function ($r) use ($fatturaNumero) {
                return is_array($r) && ($r['numero_fattura'] ?? '') === $fatturaNumero;
            }));

            if ($righeCount === 0) {
                $this->warnings[] = "Fattura {$fatturaNumero}: nessuna riga trovata (riga {$fatturaRow})";
            }
        }

        return empty($this->errors);
    }

    /**
     * Validate a single fattura row.
     *
     * @param array $fattura Fattura data.
     * @param int $index Row index.
     * @return void
     */
    protected function validateFattura(array $fattura, int $index): void
    {
        $row = $fattura['_row'] ?? $index + 3;

        // Required fields
        foreach (self::FATTURE_REQUIRED as $field) {
            if (!isset($fattura[$field]) || $fattura[$field] === '') {
                $this->errors[] = "Fatture riga {$row}: campo '{$field}' obbligatorio mancante.";
            }
        }

        // At least P.IVA or CF
        if (empty($fattura['cliente_piva'] ?? '') && empty($fattura['cliente_cf'] ?? '')) {
            $this->errors[] = "Fatture riga {$row}: specificare almeno P.IVA o Codice Fiscale del cliente.";
        }

        // Validate P.IVA format
        if (!empty($fattura['cliente_piva'])) {
            $piva = preg_replace('/[^0-9]/', '', $fattura['cliente_piva']);
            if (strlen($piva) !== 11) {
                $this->errors[] = "Fatture riga {$row}: P.IVA deve avere 11 cifre.";
            }
        }

        // Validate CF format
        if (!empty($fattura['cliente_cf'])) {
            $cf = strtoupper(trim($fattura['cliente_cf']));
            if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $cf) &&
                !preg_match('/^[0-9]{11}$/', $cf)) {
                $this->warnings[] = "Fatture riga {$row}: formato Codice Fiscale non standard.";
            }
        }

        // Validate date format
        if (!empty($fattura['data_emissione'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $fattura['data_emissione']);
            if (!$date) {
                $this->errors[] = "Fatture riga {$row}: data_emissione deve essere in formato YYYY-MM-DD.";
            }
        }

        // Validate tipo_documento
        if (!empty($fattura['tipo_documento']) && !in_array($fattura['tipo_documento'], self::VALID_TIPO_DOC)) {
            $this->errors[] = "Fatture riga {$row}: tipo_documento '{$fattura['tipo_documento']}' non valido.";
        }

        // Validate numeric fields
        $numericFields = ['importo_totale', 'imponibile_totale', 'imposta_totale'];
        foreach ($numericFields as $field) {
            if (!empty($fattura[$field]) && !is_numeric($fattura[$field])) {
                $this->errors[] = "Fatture riga {$row}: {$field} deve essere un numero.";
            }
        }

        // Validate amounts coherence
        if (!empty($fattura['importo_totale']) && !empty($fattura['imponibile_totale']) && !empty($fattura['imposta_totale'])) {
            $expected = (float)$fattura['imponibile_totale'] + (float)$fattura['imposta_totale'];
            $actual = (float)$fattura['importo_totale'];
            if (abs($expected - $actual) > 0.02) {
                $this->warnings[] = "Fatture riga {$row}: importo_totale ({$actual}) non corrisponde a imponibile + imposta ({$expected}).";
            }
        }
    }

    /**
     * Validate a single riga row.
     *
     * @param array $riga Riga data.
     * @param int $index Row index.
     * @param array $fattureNumeri Valid fattura numbers.
     * @return void
     */
    protected function validateRiga(array $riga, int $index, array $fattureNumeri): void
    {
        $row = $riga['_row'] ?? $index + 3;

        // Required fields
        foreach (self::RIGHE_REQUIRED as $field) {
            $value = $riga[$field] ?? '';
            if ($value === '' && $value !== '0') {
                $this->errors[] = "Righe riga {$row}: campo '{$field}' obbligatorio mancante.";
            }
        }

        // Check fattura exists
        $numeroFattura = $riga['numero_fattura'] ?? '';
        if (!empty($numeroFattura) && !in_array($numeroFattura, $fattureNumeri)) {
            $this->errors[] = "Righe riga {$row}: numero_fattura '{$numeroFattura}' non trovato nel foglio Fatture.";
        }

        // Validate numeric fields
        $numericFields = ['quantita', 'prezzo_unitario', 'aliquota_iva', 'importo_riga'];
        foreach ($numericFields as $field) {
            $value = $riga[$field] ?? '';
            if ($value !== '' && !is_numeric($value)) {
                $this->errors[] = "Righe riga {$row}: {$field} deve essere un numero.";
            }
        }

        // Validate aliquota_iva
        $aliquotaIva = $riga['aliquota_iva'] ?? '';
        if ($aliquotaIva !== '') {
            $aliquota = (int)$aliquotaIva;
            if (!in_array($aliquota, [0, 4, 5, 10, 22])) {
                $this->warnings[] = "Righe riga {$row}: aliquota IVA {$aliquota}% non standard.";
            }

            // If 0, natura_iva should be specified
            if ($aliquota === 0 && empty($riga['natura_iva'] ?? '')) {
                $this->warnings[] = "Righe riga {$row}: per aliquota 0% specificare natura_iva.";
            }
        }
    }

    /**
     * Execute the import.
     *
     * @param int $tenantId Tenant ID for import.
     * @param array $options Import options.
     * @return bool True if import was successful.
     */
    public function import(int $tenantId, array $options = []): bool
    {
        $this->tenantId = $tenantId;
        $options = array_merge([
            'create_anagrafiche' => true,
            'create_prodotti' => true,
            'skip_errors' => false,
        ], $options);

        $fattureTable = TableRegistry::getTableLocator()->get('Fatture');
        $righeTable = TableRegistry::getTableLocator()->get('FatturaRighe');
        $anagraficheTable = TableRegistry::getTableLocator()->get('Anagrafiche');
        $prodottiTable = TableRegistry::getTableLocator()->get('Prodotti');

        $connection = $fattureTable->getConnection();

        try {
            $connection->begin();

            // Filter out metadata entries and group righe by fattura
            $righeByFattura = [];
            foreach ($this->data['righe'] as $key => $riga) {
                if (!is_int($key) || !is_array($riga)) {
                    continue;
                }
                $numeroFattura = $riga['numero_fattura'] ?? '';
                if ($numeroFattura !== '') {
                    $righeByFattura[$numeroFattura][] = $riga;
                }
            }

            // Import each fattura with its righe
            foreach ($this->data['fatture'] as $key => $fatturaData) {
                if (!is_int($key) || !is_array($fatturaData)) {
                    continue;
                }
                // Find or create anagrafica
                $anagraficaId = $this->findOrCreateAnagrafica($fatturaData, $options);
                if (!$anagraficaId) {
                    $fatturaNumero = $fatturaData['numero'] ?? 'sconosciuto';
                    if (!$options['skip_errors']) {
                        throw new \Exception("Impossibile creare anagrafica per fattura {$fatturaNumero}");
                    }
                    $this->stats['errors']++;
                    continue;
                }

                // Create fattura
                $dataEmissione = $fatturaData['data_emissione'] ?? $fatturaData['data'] ?? date('Y-m-d');
                $anno = date('Y', strtotime($dataEmissione));

                $fattura = $fattureTable->newEntity([
                    'tenant_id' => $tenantId,
                    'anagrafica_id' => $anagraficaId,
                    'numero' => $fatturaData['numero'] ?? '',
                    'data' => $dataEmissione,
                    'anno' => (int)$anno,
                    'tipo_documento' => $fatturaData['tipo_documento'] ?? 'TD01',
                    'direzione' => 'emessa',
                    'divisa' => ($fatturaData['divisa'] ?? '') ?: 'EUR',
                    'totale_documento' => (float)($fatturaData['importo_totale'] ?? 0),
                    'totale_imponibile' => (float)($fatturaData['imponibile_totale'] ?? 0),
                    'totale_imposta' => (float)($fatturaData['imposta_totale'] ?? 0),
                    'causale' => $fatturaData['causale'] ?? null,
                    'note' => $fatturaData['note'] ?? null,
                    'stato_sdi' => 'bozza',
                ]);

                if (!$fattureTable->save($fattura)) {
                    $errors = json_encode($fattura->getErrors(), JSON_UNESCAPED_UNICODE);
                    $fatturaNumero = $fatturaData['numero'] ?? 'sconosciuto';
                    if (!$options['skip_errors']) {
                        throw new \Exception("Errore salvataggio fattura {$fatturaNumero}: {$errors}");
                    }
                    $this->errors[] = "Errore salvataggio fattura {$fatturaNumero}: {$errors}";
                    $this->stats['errors']++;
                    continue;
                }

                $this->stats['fatture_create']++;

                // Import righe for this fattura
                $fatturaNumero = $fatturaData['numero'] ?? '';
                $righe = $righeByFattura[$fatturaNumero] ?? [];
                foreach ($righe as $rigaData) {
                    $prodottoId = null;
                    if (!empty($rigaData['prodotto_codice'] ?? '') && $options['create_prodotti']) {
                        $prodottoId = $this->findOrCreateProdotto($rigaData, $tenantId);
                    }

                    $quantita = (float)($rigaData['quantita'] ?? 1);
                    $prezzoUnitario = (float)($rigaData['prezzo_unitario'] ?? 0);
                    $importoRiga = (float)($rigaData['importo_riga'] ?? ($quantita * $prezzoUnitario));

                    $riga = $righeTable->newEntity([
                        'prodotto_id' => $prodottoId,
                        'numero_linea' => (int)($rigaData['numero_riga'] ?? $rigaData['numero_linea'] ?? 1),
                        'descrizione' => $rigaData['descrizione'] ?? 'Articolo',
                        'quantita' => $quantita,
                        'unita_misura' => ($rigaData['unita_misura'] ?? '') ?: 'NR',
                        'prezzo_unitario' => $prezzoUnitario,
                        'aliquota_iva' => (float)($rigaData['aliquota_iva'] ?? 22),
                        'natura' => ($rigaData['natura_iva'] ?? '') ?: null,
                        'prezzo_totale' => $importoRiga,
                        'ritenuta' => false,
                        'sort_order' => 0,
                    ]);
                    // Assegna fattura_id direttamente (campo protetto nell'entity)
                    $riga->setAccess('fattura_id', true);
                    $riga->fattura_id = $fattura->id;

                    if (!$righeTable->save($riga)) {
                        Log::warning("Errore salvataggio riga fattura {$fatturaNumero}: " . json_encode($riga->getErrors()));
                        if (!$options['skip_errors']) {
                            throw new \Exception("Errore salvataggio riga fattura {$fatturaNumero}");
                        }
                        $this->stats['errors']++;
                        continue;
                    }

                    $this->stats['righe_create']++;
                }
            }

            $connection->commit();

            return true;
        } catch (\Exception $e) {
            $connection->rollback();
            $this->errors[] = $e->getMessage();
            Log::error('Excel import error: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Find or create anagrafica from fattura data.
     *
     * @param array $fattura Fattura data with cliente_* fields.
     * @param array $options Import options.
     * @return int|null Anagrafica ID or null on failure.
     */
    protected function findOrCreateAnagrafica(array $fattura, array $options): ?int
    {
        $anagraficheTable = TableRegistry::getTableLocator()->get('Anagrafiche');

        // Try to find by P.IVA
        if (!empty($fattura['cliente_piva'])) {
            $piva = preg_replace('/[^0-9]/', '', $fattura['cliente_piva']);
            $existing = $anagraficheTable->find()
                ->where([
                    'tenant_id' => $this->tenantId,
                    'partita_iva' => $piva,
                ])
                ->first();

            if ($existing) {
                return $existing->id;
            }
        }

        // Try to find by CF
        if (!empty($fattura['cliente_cf'])) {
            $cf = strtoupper(trim($fattura['cliente_cf']));
            $existing = $anagraficheTable->find()
                ->where([
                    'tenant_id' => $this->tenantId,
                    'codice_fiscale' => $cf,
                ])
                ->first();

            if ($existing) {
                return $existing->id;
            }
        }

        // Create new if allowed
        if (!$options['create_anagrafiche']) {
            return null;
        }

        $anagrafica = $anagraficheTable->newEntity([
            'tenant_id' => $this->tenantId,
            'tipo' => 'cliente',
            'denominazione' => $fattura['cliente_denominazione'] ?? 'Cliente sconosciuto',
            'partita_iva' => !empty($fattura['cliente_piva'] ?? '') ? preg_replace('/[^0-9]/', '', $fattura['cliente_piva']) : null,
            'codice_fiscale' => !empty($fattura['cliente_cf'] ?? '') ? strtoupper(trim($fattura['cliente_cf'])) : null,
            'indirizzo' => ($fattura['cliente_indirizzo'] ?? '') ?: 'Non specificato',
            'cap' => ($fattura['cliente_cap'] ?? '') ?: '00000',
            'comune' => ($fattura['cliente_citta'] ?? $fattura['cliente_comune'] ?? '') ?: 'Non specificato',
            'provincia' => ($fattura['cliente_provincia'] ?? '') ?: 'XX',
            'nazione' => ($fattura['cliente_nazione'] ?? '') ?: 'IT',
            'regime_fiscale' => ($fattura['cliente_regime_fiscale'] ?? '') ?: 'RF01',
            'pec' => $fattura['cliente_pec'] ?? null,
            'codice_destinatario' => $fattura['cliente_codice_sdi'] ?? '0000000',
            'is_active' => true,
        ]);

        if ($anagraficheTable->save($anagrafica)) {
            $this->stats['anagrafiche_create']++;

            return $anagrafica->id;
        }

        // Log validation errors
        $errors = $anagrafica->getErrors();
        if (!empty($errors)) {
            $this->errors[] = "Errore creazione anagrafica: " . json_encode($errors, JSON_UNESCAPED_UNICODE);
        }

        return null;
    }

    /**
     * Find or create product from riga data.
     *
     * @param array $riga Riga data.
     * @param int $tenantId Tenant ID.
     * @return int|null Product ID or null.
     */
    protected function findOrCreateProdotto(array $riga, int $tenantId): ?int
    {
        $prodottiTable = TableRegistry::getTableLocator()->get('Prodotti');
        $codice = trim($riga['prodotto_codice']);

        // Try to find existing
        $existing = $prodottiTable->find()
            ->where([
                'tenant_id' => $tenantId,
                'codice' => $codice,
            ])
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // Create new
        $prodotto = $prodottiTable->newEntity([
            'tenant_id' => $tenantId,
            'codice' => $codice,
            'nome' => $riga['descrizione'],
            'descrizione' => $riga['descrizione'],
            'prezzo' => (float)$riga['prezzo_unitario'],
            'aliquota_iva' => (float)$riga['aliquota_iva'],
            'unita_misura' => $riga['unita_misura'] ?: 'PZ',
            'is_active' => true,
        ]);

        if ($prodottiTable->save($prodotto)) {
            $this->stats['prodotti_create']++;

            return $prodotto->id;
        }

        return null;
    }

    /**
     * Get parsed data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get warnings.
     *
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Get import statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Check if there are errors.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get preview data for display.
     *
     * @return array
     */
    public function getPreviewData(): array
    {
        // Filter out metadata entries
        $fatture = array_values(array_filter($this->data['fatture'], fn($key) => is_int($key), ARRAY_FILTER_USE_KEY));
        $righe = array_values(array_filter($this->data['righe'], fn($key) => is_int($key), ARRAY_FILTER_USE_KEY));

        return [
            'fatture' => array_slice($fatture, 0, 50),
            'righe' => array_slice($righe, 0, 100),
            'totals' => [
                'fatture' => count($fatture),
                'righe' => count($righe),
            ],
            'errors' => $this->errors,
            'warnings' => $this->warnings,
        ];
    }
}
