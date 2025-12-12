<?php
declare(strict_types=1);

namespace App\Test\TestCase\Service;

use App\Service\XmlImportService;
use Cake\TestSuite\TestCase;
use ReflectionClass;

/**
 * App\Service\XmlImportService Test Case
 */
class XmlImportServiceTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Service\XmlImportService
     */
    protected XmlImportService $service;

    /**
     * Fixture directory path
     *
     * @var string
     */
    protected string $fixtureDir;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new XmlImportService();
        $this->fixtureDir = TESTS . 'Fixture' . DS . 'xml' . DS;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->service);
        parent::tearDown();
    }

    /**
     * Test parseFile with valid XML
     *
     * @return void
     */
    public function testParseValidXml(): void
    {
        $result = $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');

        $this->assertTrue($result);
        $this->assertFalse($this->service->hasErrors());

        $fatture = $this->service->getFatture();
        $this->assertCount(1, $fatture);
    }

    /**
     * Test parseFile with XML without namespace
     *
     * @return void
     */
    public function testParseXmlWithoutNamespace(): void
    {
        $result = $this->service->parseFile($this->fixtureDir . 'fattura_senza_namespace.xml');

        $this->assertTrue($result);
        $this->assertFalse($this->service->hasErrors());

        $fatture = $this->service->getFatture();
        $this->assertCount(1, $fatture);
    }

    /**
     * Test parseFile with invalid XML
     *
     * @return void
     */
    public function testParseInvalidXml(): void
    {
        $result = $this->service->parseFile($this->fixtureDir . 'fattura_invalid.xml');

        $this->assertFalse($result);
        $this->assertTrue($this->service->hasErrors());

        $errors = $this->service->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('XML non valido', $errors[0]);
    }

    /**
     * Test parseFile with non-FatturaPA XML
     *
     * @return void
     */
    public function testParseNonFatturaPaXml(): void
    {
        $result = $this->service->parseFile($this->fixtureDir . 'fattura_non_fatturapa.xml');

        $this->assertFalse($result);
        $this->assertTrue($this->service->hasErrors());

        $errors = $this->service->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('non è un file FatturaPA valido', $errors[0]);
    }

    /**
     * Test parseFile with unsupported file format
     *
     * @return void
     */
    public function testUnsupportedFileFormat(): void
    {
        // Create a temporary text file
        $tempFile = TMP . 'test_file.txt';
        file_put_contents($tempFile, 'Not an XML file');

        $result = $this->service->parseFile($tempFile);

        $this->assertFalse($result);
        $errors = $this->service->getErrors();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Formato file non supportato', $errors[0]);

        @unlink($tempFile);
    }

    /**
     * Test parsing header data (cedente/cessionario)
     *
     * @return void
     */
    public function testParseHeader(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
        $fatture = $this->service->getFatture();

        $this->assertArrayHasKey('cedente', $fatture[0]);
        $this->assertArrayHasKey('cessionario', $fatture[0]);

        // Check cedente data
        $cedente = $fatture[0]['cedente'];
        $this->assertEquals('01234567890', $cedente['partita_iva']);
        $this->assertEquals('Azienda Test SRL', $cedente['denominazione']);
        $this->assertEquals('Via Roma 1', $cedente['indirizzo']);
        $this->assertEquals('00100', $cedente['cap']);
        $this->assertEquals('Roma', $cedente['citta']);
        $this->assertEquals('RM', $cedente['provincia']);
        $this->assertEquals('RF01', $cedente['regime_fiscale']);

        // Check cessionario data
        $cessionario = $fatture[0]['cessionario'];
        $this->assertEquals('09876543210', $cessionario['partita_iva']);
        $this->assertEquals('Cliente Test SPA', $cessionario['denominazione']);
        $this->assertEquals('Via Milano 2', $cessionario['indirizzo']);
    }

    /**
     * Test parsing body data (numero, data, importi)
     *
     * @return void
     */
    public function testParseBody(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
        $fatture = $this->service->getFatture();

        $fattura = $fatture[0];
        $this->assertEquals('TD01', $fattura['tipo_documento']);
        $this->assertEquals('EUR', $fattura['divisa']);
        $this->assertEquals('2025-01-15', $fattura['data_emissione']);
        $this->assertEquals('2025/001', $fattura['numero']);
        $this->assertEquals(1220.00, $fattura['importo_totale']);
        $this->assertEquals('Vendita prodotti', $fattura['causale']);
    }

    /**
     * Test parsing righe (detail lines)
     *
     * @return void
     */
    public function testParseRighe(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
        $fatture = $this->service->getFatture();

        $this->assertArrayHasKey('righe', $fatture[0]);
        $righe = $fatture[0]['righe'];

        $this->assertCount(2, $righe);

        // First riga
        $this->assertEquals(1, $righe[0]['numero_riga']);
        $this->assertEquals('Prodotto A', $righe[0]['descrizione']);
        $this->assertEquals(2.00, $righe[0]['quantita']);
        $this->assertEquals('PZ', $righe[0]['unita_misura']);
        $this->assertEquals(250.00, $righe[0]['prezzo_unitario']);
        $this->assertEquals(500.00, $righe[0]['prezzo_totale']);
        $this->assertEquals(22.00, $righe[0]['aliquota_iva']);

        // Second riga
        $this->assertEquals(2, $righe[1]['numero_riga']);
        $this->assertEquals('Prodotto B', $righe[1]['descrizione']);
        $this->assertEquals(5.00, $righe[1]['quantita']);
    }

    /**
     * Test parsing riepilogo IVA
     *
     * @return void
     */
    public function testParseRiepilogo(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
        $fatture = $this->service->getFatture();

        $this->assertArrayHasKey('riepilogo', $fatture[0]);
        $riepilogo = $fatture[0]['riepilogo'];

        $this->assertCount(1, $riepilogo);
        $this->assertEquals(22.00, $riepilogo[0]['aliquota']);
        $this->assertEquals(1000.00, $riepilogo[0]['imponibile']);
        $this->assertEquals(220.00, $riepilogo[0]['imposta']);
    }

    /**
     * Test calculated totals
     *
     * @return void
     */
    public function testCalculatedTotals(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
        $fatture = $this->service->getFatture();

        $fattura = $fatture[0];
        $this->assertEquals(1000.00, $fattura['imponibile_totale']);
        $this->assertEquals(220.00, $fattura['imposta_totale']);
    }

    /**
     * Test validation with missing numero
     *
     * @return void
     */
    public function testValidateMissingNumero(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_incompleta.xml');
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();
        $this->assertNotEmpty($errors);

        $hasNumeroError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'numero fattura mancante') !== false) {
                $hasNumeroError = true;
                break;
            }
        }
        $this->assertTrue($hasNumeroError, 'Expected error about missing numero');
    }

    /**
     * Test validation with missing date
     *
     * @return void
     */
    public function testValidateMissingDate(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_incompleta.xml');
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasDateError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'data emissione mancante') !== false) {
                $hasDateError = true;
                break;
            }
        }
        $this->assertTrue($hasDateError, 'Expected error about missing date');
    }

    /**
     * Test validation with missing P.IVA/CF
     *
     * @return void
     */
    public function testValidateMissingPiva(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_incompleta.xml');
        $result = $this->service->validate();

        $this->assertFalse($result);
        $errors = $this->service->getErrors();

        $hasPivaError = false;
        foreach ($errors as $error) {
            if (strpos($error, 'P.IVA o CF') !== false && strpos($error, 'mancante') !== false) {
                $hasPivaError = true;
                break;
            }
        }
        $this->assertTrue($hasPivaError, 'Expected error about missing P.IVA or CF');
    }

    /**
     * Test validation passes for valid XML
     *
     * @return void
     */
    public function testValidateSuccess(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
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
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
        $preview = $this->service->getPreviewData();

        $this->assertArrayHasKey('fatture', $preview);
        $this->assertArrayHasKey('totals', $preview);
        $this->assertArrayHasKey('errors', $preview);
        $this->assertArrayHasKey('warnings', $preview);

        $this->assertEquals(1, $preview['totals']['fatture']);
        $this->assertEquals(2, $preview['totals']['righe']);
    }

    /**
     * Test getStats
     *
     * @return void
     */
    public function testGetStats(): void
    {
        $stats = $this->service->getStats();

        $this->assertArrayHasKey('files_parsed', $stats);
        $this->assertArrayHasKey('fatture_create', $stats);
        $this->assertArrayHasKey('righe_create', $stats);
        $this->assertArrayHasKey('anagrafiche_create', $stats);
        $this->assertArrayHasKey('skipped', $stats);
        $this->assertArrayHasKey('errors', $stats);
    }

    /**
     * Test parsing XML with Nome/Cognome instead of Denominazione
     *
     * @return void
     */
    public function testParseNomeCognome(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_senza_namespace.xml');
        $fatture = $this->service->getFatture();

        $cessionario = $fatture[0]['cessionario'];
        // Should build denominazione from nome + cognome
        $this->assertEquals('Mario Rossi', $cessionario['denominazione']);
    }

    /**
     * Test file not found error
     *
     * @return void
     */
    public function testFileNotFound(): void
    {
        $result = $this->service->parseFile('/nonexistent/path/file.xml');

        $this->assertFalse($result);
        $this->assertTrue($this->service->hasErrors());
    }

    /**
     * Test getWarnings method
     *
     * @return void
     */
    public function testGetWarnings(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_incompleta.xml');
        $this->service->validate();

        $warnings = $this->service->getWarnings();
        $this->assertIsArray($warnings);
        // This fattura has no righe, should generate a warning
        $hasRigheWarning = false;
        foreach ($warnings as $warning) {
            if (strpos($warning, 'nessuna riga di dettaglio') !== false) {
                $hasRigheWarning = true;
                break;
            }
        }
        $this->assertTrue($hasRigheWarning, 'Expected warning about missing righe');
    }

    /**
     * Test source file tracking
     *
     * @return void
     */
    public function testSourceFileTracking(): void
    {
        $this->service->parseFile($this->fixtureDir . 'fattura_valida.xml');
        $fatture = $this->service->getFatture();

        $this->assertArrayHasKey('_source_file', $fatture[0]);
        $this->assertEquals('fattura_valida.xml', $fatture[0]['_source_file']);
        $this->assertArrayHasKey('_body_index', $fatture[0]);
        $this->assertEquals(0, $fatture[0]['_body_index']);
    }
}
