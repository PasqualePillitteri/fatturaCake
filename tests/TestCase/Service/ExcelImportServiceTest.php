<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\ExcelImportService;
use Cake\TestSuite\TestCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * App\Service\ExcelImportService Test Case
 */
class ExcelImportServiceTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Service\ExcelImportService
     */
    protected ExcelImportService $service;

    /**
     * Temporary file paths to cleanup
     *
     * @var array
     */
    protected array $tempFiles = [];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExcelImportService();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->service);

        // Cleanup temp files
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
        $this->tempFiles = [];

        parent::tearDown();
    }

    /**
     * Create a valid test Excel file
     *
     * @return string Path to the created file
     */
    protected function createValidExcelFile(): string
    {
        $spreadsheet = new Spreadsheet();

        // Fatture sheet
        $fattureSheet = $spreadsheet->getActiveSheet();
        $fattureSheet->setTitle('Fatture');
        $fattureSheet->fromArray([
            ['numero*', 'data_emissione*', 'tipo_documento*', 'cliente_denominazione*', 'cliente_piva', 'cliente_cf', 'importo_totale*', 'imponibile_totale*', 'imposta_totale*', 'causale'],
            ['Esempio', '2025-01-01', 'TD01', 'Esempio SRL', '12345678901', '', '122.00', '100.00', '22.00', 'Esempio'],
            ['2025/001', '2025-01-15', 'TD01', 'Cliente Test SRL', '01234567890', '', '1220.00', '1000.00', '220.00', 'Vendita prodotti'],
            ['2025/002', '2025-01-20', 'TD01', 'Altro Cliente SPA', '09876543210', '', '610.00', '500.00', '110.00', 'Servizi'],
        ], null, 'A1');

        // Righe sheet
        $righeSheet = $spreadsheet->createSheet();
        $righeSheet->setTitle('Righe');
        $righeSheet->fromArray([
            ['numero_fattura*', 'numero_riga*', 'descrizione*', 'quantita*', 'unita_misura', 'prezzo_unitario*', 'aliquota_iva*', 'importo_riga*', 'natura_iva'],
            ['Esempio', '1', 'Riga esempio', '1', 'NR', '100.00', '22', '100.00', ''],
            ['2025/001', '1', 'Prodotto A', '2', 'PZ', '250.00', '22', '500.00', ''],
            ['2025/001', '2', 'Prodotto B', '5', 'PZ', '100.00', '22', '500.00', ''],
            ['2025/002', '1', 'Servizio consulenza', '1', 'NR', '500.00', '22', '500.00', ''],
        ], null, 'A1');

        $filepath = TMP . 'test_import_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->tempFiles[] = $filepath;

        return $filepath;
    }

    /**
     * Create an Excel file without Fatture sheet
     *
     * @return string Path to the created file
     */
    protected function createExcelWithoutFattureSheet(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Righe');
        $sheet->fromArray([
            ['numero_fattura*', 'numero_riga*', 'descrizione*'],
            ['001', '1', 'Test'],
        ], null, 'A1');

        $filepath = TMP . 'test_no_fatture_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->tempFiles[] = $filepath;

        return $filepath;
    }

    /**
     * Create an Excel file without Righe sheet
     *
     * @return string Path to the created file
     */
    protected function createExcelWithoutRigheSheet(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Fatture');
        $sheet->fromArray([
            ['numero*', 'data_emissione*', 'tipo_documento*'],
            ['001', '2025-01-01', 'TD01'],
        ], null, 'A1');

        $filepath = TMP . 'test_no_righe_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->tempFiles[] = $filepath;

        return $filepath;
    }

    /**
     * Create an Excel file with missing required columns
     *
     * @return string Path to the created file
     */
    protected function createExcelMissingColumns(): string
    {
        $spreadsheet = new Spreadsheet();

        // Fatture sheet with missing columns
        $fattureSheet = $spreadsheet->getActiveSheet();
        $fattureSheet->setTitle('Fatture');
        $fattureSheet->fromArray([
            ['numero*', 'data_emissione*'],  // Missing: tipo_documento, cliente_denominazione, importo_totale, etc.
            ['', ''],
            ['001', '2025-01-01'],
        ], null, 'A1');

        // Righe sheet with missing columns
        $righeSheet = $spreadsheet->createSheet();
        $righeSheet->setTitle('Righe');
        $righeSheet->fromArray([
            ['numero_fattura*', 'descrizione*'],  // Missing: numero_riga, quantita, prezzo_unitario, etc.
            ['', ''],
            ['001', 'Test'],
        ], null, 'A1');

        $filepath = TMP . 'test_missing_cols_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->tempFiles[] = $filepath;

        return $filepath;
    }

    /**
     * Create an Excel file with invalid data
     *
     * @return string Path to the created file
     */
    protected function createExcelInvalidData(): string
    {
        $spreadsheet = new Spreadsheet();

        // Fatture sheet
        $fattureSheet = $spreadsheet->getActiveSheet();
        $fattureSheet->setTitle('Fatture');
        $fattureSheet->fromArray([
            ['numero*', 'data_emissione*', 'tipo_documento*', 'cliente_denominazione*', 'cliente_piva', 'cliente_cf', 'importo_totale*', 'imponibile_totale*', 'imposta_totale*'],
            ['Esempio', '2025-01-01', 'TD01', 'Esempio', '12345678901', '', '100', '100', '0'],
            ['001', 'invalid-date', 'INVALID', 'Cliente Test', '123', '', 'not-a-number', '100.00', '22.00'],
        ], null, 'A1');

        // Righe sheet
        $righeSheet = $spreadsheet->createSheet();
        $righeSheet->setTitle('Righe');
        $righeSheet->fromArray([
            ['numero_fattura*', 'numero_riga*', 'descrizione*', 'quantita*', 'prezzo_unitario*', 'aliquota_iva*', 'importo_riga*'],
            ['Esempio', '1', 'Esempio', '1', '100', '22', '100'],
            ['001', '1', 'Test', 'not-a-number', '100.00', '22', '100.00'],
            ['999', '1', 'Orphan riga', '1', '100.00', '22', '100.00'],  // Non-existent fattura
        ], null, 'A1');

        $filepath = TMP . 'test_invalid_data_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->tempFiles[] = $filepath;

        return $filepath;
    }

    /**
     * Create an Excel file for testing amount coherence
     *
     * @return string Path to the created file
     */
    protected function createExcelIncoherentAmounts(): string
    {
        $spreadsheet = new Spreadsheet();

        $fattureSheet = $spreadsheet->getActiveSheet();
        $fattureSheet->setTitle('Fatture');
        $fattureSheet->fromArray([
            ['numero*', 'data_emissione*', 'tipo_documento*', 'cliente_denominazione*', 'cliente_piva', 'cliente_cf', 'importo_totale*', 'imponibile_totale*', 'imposta_totale*'],
            ['Esempio', '2025-01-01', 'TD01', 'Esempio', '12345678901', '', '100', '100', '0'],
            ['001', '2025-01-15', 'TD01', 'Cliente Test', '01234567890', '', '500.00', '100.00', '22.00'],  // 100+22 != 500
        ], null, 'A1');

        $righeSheet = $spreadsheet->createSheet();
        $righeSheet->setTitle('Righe');
        $righeSheet->fromArray([
            ['numero_fattura*', 'numero_riga*', 'descrizione*', 'quantita*', 'prezzo_unitario*', 'aliquota_iva*', 'importo_riga*'],
            ['Esempio', '1', 'Esempio', '1', '100', '22', '100'],
            ['001', '1', 'Test', '1', '100.00', '22', '100.00'],
        ], null, 'A1');

        $filepath = TMP . 'test_incoherent_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->tempFiles[] = $filepath;

        return $filepath;
    }

    /**
     * Create an Excel file with fattura without righe
     *
     * @return string Path to the created file
     */
    protected function createExcelFatturaWithoutRighe(): string
    {
        $spreadsheet = new Spreadsheet();

        $fattureSheet = $spreadsheet->getActiveSheet();
        $fattureSheet->setTitle('Fatture');
        $fattureSheet->fromArray([
            ['numero*', 'data_emissione*', 'tipo_documento*', 'cliente_denominazione*', 'cliente_piva', 'cliente_cf', 'importo_totale*', 'imponibile_totale*', 'imposta_totale*'],
            ['Esempio', '2025-01-01', 'TD01', 'Esempio', '12345678901', '', '100', '100', '0'],
            ['001', '2025-01-15', 'TD01', 'Cliente Test', '01234567890', '', '122.00', '100.00', '22.00'],
            ['002', '2025-01-20', 'TD01', 'Altro Cliente', '09876543210', '', '61.00', '50.00', '11.00'],  // No righe
        ], null, 'A1');

        $righeSheet = $spreadsheet->createSheet();
        $righeSheet->setTitle('Righe');
        $righeSheet->fromArray([
            ['numero_fattura*', 'numero_riga*', 'descrizione*', 'quantita*', 'prezzo_unitario*', 'aliquota_iva*', 'importo_riga*'],
            ['Esempio', '1', 'Esempio', '1', '100', '22', '100'],
            ['001', '1', 'Test', '1', '100.00', '22', '100.00'],  // Only 001 has righe
        ], null, 'A1');

        $filepath = TMP . 'test_no_righe_for_fattura_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->tempFiles[] = $filepath;

        return $filepath;
    }

    /**
     * Test parsing valid Excel file
     *
     * @return void
     */
    public function testParseValidExcel(): void
    {
        $filepath = $this->createValidExcelFile();
        $result = $this->service->parseFile($filepath);

        $this->assertTrue($result);
        $this->assertFalse($this->service->hasErrors());
    }

    /**
     * Test parsing Fatture sheet
     *
     * @return void
     */
    public function testParseFattureSheet(): void
    {
        $filepath = $this->createValidExcelFile();
        $this->service->parseFile($filepath);

        $data = $this->service->getData();
        $this->assertArrayHasKey('fatture', $data);

        // Should have 2 data rows (excluding header and example row)
        $fatture = array_filter($data['fatture'], fn($key) => is_int($key), ARRAY_FILTER_USE_KEY);
        $this->assertCount(2, $fatture);
    }

    /**
     * Test parsing Righe sheet
     *
     * @return void
     */
    public function testParseRigheSheet(): void
    {
        $filepath = $this->createValidExcelFile();
        $this->service->parseFile($filepath);

        $data = $this->service->getData();
        $this->assertArrayHasKey('righe', $data);

        // Should have 3 data rows
        $righe = array_filter($data['righe'], fn($key) => is_int($key), ARRAY_FILTER_USE_KEY);
        $this->assertCount(3, $righe);
    }

    /**
     * Test error when Fatture sheet is missing
     *
     * @return void
     */
    public function testMissingFattureSheet(): void
    {
        $filepath = $this->createExcelWithoutFattureSheet();
        $result = $this->service->parseFile($filepath);

        $this->assertFalse($result);
        $errors = $this->service->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Fatture', $errors[0]);
        $this->assertStringContainsString('non trovato', $errors[0]);
    }

    /**
     * Test error when Righe sheet is missing
     *
     * @return void
     */
    public function testMissingRigheSheet(): void
    {
        $filepath = $this->createExcelWithoutRigheSheet();
        $result = $this->service->parseFile($filepath);

        $this->assertFalse($result);
        $errors = $this->service->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Righe', $errors[0]);
        $this->assertStringContainsString('non trovato', $errors[0]);
    }

    /**
     * Test validation of required columns
     *
     * @return void
     */
    public function testMissingRequiredColumns(): void
    {
        $filepath = $this->createExcelMissingColumns();
        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();
        $this->assertNotEmpty($errors);

        // Should report missing columns
        $hasColumnError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'colonne obbligatorie mancanti') !== false) {
                $hasColumnError = true;
                break;
            }
        }
        $this->assertTrue($hasColumnError, 'Expected error about missing required columns');
    }

    /**
     * Test validation of P.IVA format
     *
     * @return void
     */
    public function testValidatePivaFormat(): void
    {
        $filepath = $this->createExcelInvalidData();
        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasPivaError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'P.IVA deve avere 11 cifre') !== false) {
                $hasPivaError = true;
                break;
            }
        }
        $this->assertTrue($hasPivaError, 'Expected error about P.IVA format');
    }

    /**
     * Test validation of date format
     *
     * @return void
     */
    public function testValidateDateFormat(): void
    {
        $filepath = $this->createExcelInvalidData();
        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasDateError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'data_emissione deve essere in formato') !== false) {
                $hasDateError = true;
                break;
            }
        }
        $this->assertTrue($hasDateError, 'Expected error about date format');
    }

    /**
     * Test validation of tipo_documento
     *
     * @return void
     */
    public function testValidateTipoDocumento(): void
    {
        $filepath = $this->createExcelInvalidData();
        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasTipoError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'tipo_documento') !== false && strpos($error, 'non valido') !== false) {
                $hasTipoError = true;
                break;
            }
        }
        $this->assertTrue($hasTipoError, 'Expected error about invalid tipo_documento');
    }

    /**
     * Test validation of numeric fields
     *
     * @return void
     */
    public function testValidateNumericFields(): void
    {
        $filepath = $this->createExcelInvalidData();
        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasNumericError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'deve essere un numero') !== false) {
                $hasNumericError = true;
                break;
            }
        }
        $this->assertTrue($hasNumericError, 'Expected error about numeric field');
    }

    /**
     * Test validation of amounts coherence
     *
     * @return void
     */
    public function testValidateAmountsCoherence(): void
    {
        $filepath = $this->createExcelIncoherentAmounts();
        $this->service->parseFile($filepath);
        $this->service->validate();

        $warnings = $this->service->getWarnings();

        $hasCoherenceWarning = false;
        foreach ($warnings as $warning) {
            if (strpos($warning, 'importo_totale') !== false && strpos($warning, 'non corrisponde') !== false) {
                $hasCoherenceWarning = true;
                break;
            }
        }
        $this->assertTrue($hasCoherenceWarning, 'Expected warning about amount coherence');
    }

    /**
     * Test validation of righe referencing non-existent fattura
     *
     * @return void
     */
    public function testValidateRigheReference(): void
    {
        $filepath = $this->createExcelInvalidData();
        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasReferenceError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'numero_fattura') !== false && strpos($error, 'non trovato') !== false) {
                $hasReferenceError = true;
                break;
            }
        }
        $this->assertTrue($hasReferenceError, 'Expected error about riga referencing non-existent fattura');
    }

    /**
     * Test warning for fattura without righe
     *
     * @return void
     */
    public function testWarningMissingRighe(): void
    {
        $filepath = $this->createExcelFatturaWithoutRighe();
        $this->service->parseFile($filepath);
        $this->service->validate();

        $warnings = $this->service->getWarnings();

        $hasWarning = false;
        foreach ($warnings as $warning) {
            if (strpos($warning, '002') !== false && strpos($warning, 'nessuna riga') !== false) {
                $hasWarning = true;
                break;
            }
        }
        $this->assertTrue($hasWarning, 'Expected warning about fattura without righe');
    }

    /**
     * Test validation success for valid file
     *
     * @return void
     */
    public function testValidateSuccess(): void
    {
        $filepath = $this->createValidExcelFile();
        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertTrue($result);
        $this->assertEmpty($this->service->getErrors());
    }

    /**
     * Test getPreviewData
     *
     * @return void
     */
    public function testGetPreviewData(): void
    {
        $filepath = $this->createValidExcelFile();
        $this->service->parseFile($filepath);
        $preview = $this->service->getPreviewData();

        $this->assertArrayHasKey('fatture', $preview);
        $this->assertArrayHasKey('righe', $preview);
        $this->assertArrayHasKey('totals', $preview);
        $this->assertArrayHasKey('errors', $preview);
        $this->assertArrayHasKey('warnings', $preview);

        $this->assertEquals(2, $preview['totals']['fatture']);
        $this->assertEquals(3, $preview['totals']['righe']);
    }

    /**
     * Test getStats
     *
     * @return void
     */
    public function testGetStats(): void
    {
        $stats = $this->service->getStats();

        $this->assertArrayHasKey('fatture_create', $stats);
        $this->assertArrayHasKey('righe_create', $stats);
        $this->assertArrayHasKey('anagrafiche_create', $stats);
        $this->assertArrayHasKey('prodotti_create', $stats);
        $this->assertArrayHasKey('errors', $stats);
    }

    /**
     * Test hasErrors method
     *
     * @return void
     */
    public function testHasErrors(): void
    {
        $filepath = $this->createValidExcelFile();
        $this->service->parseFile($filepath);

        $this->assertFalse($this->service->hasErrors());

        $invalidFilepath = $this->createExcelWithoutFattureSheet();
        $this->service = new ExcelImportService();
        $this->service->parseFile($invalidFilepath);

        $this->assertTrue($this->service->hasErrors());
    }

    /**
     * Test getData method
     *
     * @return void
     */
    public function testGetData(): void
    {
        $filepath = $this->createValidExcelFile();
        $this->service->parseFile($filepath);

        $data = $this->service->getData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('fatture', $data);
        $this->assertArrayHasKey('righe', $data);
    }

    /**
     * Test file read error
     *
     * @return void
     */
    public function testFileReadError(): void
    {
        $result = $this->service->parseFile('/nonexistent/path/file.xlsx');

        $this->assertFalse($result);
        $this->assertTrue($this->service->hasErrors());
        $errors = $this->service->getErrors();
        $this->assertStringContainsString('Errore lettura file', $errors[0]);
    }

    /**
     * Test validation requires P.IVA or CF
     *
     * @return void
     */
    public function testValidateRequiresPivaOrCf(): void
    {
        $spreadsheet = new Spreadsheet();

        $fattureSheet = $spreadsheet->getActiveSheet();
        $fattureSheet->setTitle('Fatture');
        $fattureSheet->fromArray([
            ['numero*', 'data_emissione*', 'tipo_documento*', 'cliente_denominazione*', 'cliente_piva', 'cliente_cf', 'importo_totale*', 'imponibile_totale*', 'imposta_totale*'],
            ['Esempio', '2025-01-01', 'TD01', 'Esempio', '12345678901', '', '100', '100', '0'],
            ['001', '2025-01-15', 'TD01', 'Cliente Senza ID', '', '', '122.00', '100.00', '22.00'],  // No P.IVA, no CF
        ], null, 'A1');

        $righeSheet = $spreadsheet->createSheet();
        $righeSheet->setTitle('Righe');
        $righeSheet->fromArray([
            ['numero_fattura*', 'numero_riga*', 'descrizione*', 'quantita*', 'prezzo_unitario*', 'aliquota_iva*', 'importo_riga*'],
            ['Esempio', '1', 'Esempio', '1', '100', '22', '100'],
            ['001', '1', 'Test', '1', '100.00', '22', '100.00'],
        ], null, 'A1');

        $filepath = TMP . 'test_no_id_' . uniqid() . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
        $this->tempFiles[] = $filepath;

        $this->service->parseFile($filepath);
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasIdError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'P.IVA o Codice Fiscale') !== false) {
                $hasIdError = true;
                break;
            }
        }
        $this->assertTrue($hasIdError, 'Expected error about missing P.IVA or CF');
    }
}
