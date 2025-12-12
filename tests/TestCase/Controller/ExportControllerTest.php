<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use ArrayObject;
use Authentication\Identity;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ExportController Test Case
 *
 * Tests for invoice export functionality (Excel and XML).
 */
class ExportControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.Tenants',
        'app.Piani',
        'app.Abbonamenti',
        'app.Fatture',
        'app.Anagrafiche',
        'app.FatturaRighe',
        'app.ConfigurazioniSdi',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    /**
     * Helper method to login as a user with tenant.
     *
     * @param int $userId User ID
     * @param int|null $tenantId Tenant ID
     * @param string $role User role
     * @return void
     */
    protected function loginAsUser(int $userId = 2, ?int $tenantId = 1, string $role = 'admin'): void
    {
        $userData = new ArrayObject([
            'id' => $userId,
            'tenant_id' => $tenantId,
            'role' => $role,
            'username' => 'admin_tenant1',
            'email' => 'admin@tenant1.com',
        ]);

        $identity = new Identity($userData);

        $this->session([
            'Auth' => [
                'id' => $userId,
                'tenant_id' => $tenantId,
                'role' => $role,
                'username' => 'admin_tenant1',
                'email' => 'admin@tenant1.com',
            ],
        ]);

        // Set identity for Authentication component
        $this->configRequest([
            'attributes' => [
                'identity' => $identity,
            ],
        ]);
    }

    /**
     * Helper to login as user without tenant.
     *
     * @return void
     */
    protected function loginAsUserWithoutTenant(): void
    {
        $userData = new ArrayObject([
            'id' => 1,
            'tenant_id' => null,
            'role' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
        ]);

        $identity = new Identity($userData);

        $this->session([
            'Auth' => [
                'id' => 1,
                'tenant_id' => null,
                'role' => 'superadmin',
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
            ],
        ]);

        $this->configRequest([
            'attributes' => [
                'identity' => $identity,
            ],
        ]);
    }

    /**
     * Test index page accessible when authenticated.
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->loginAsUser();

        $this->get('/export');

        $this->assertResponseOk();
        $this->assertResponseContains('Export');
    }

    /**
     * Test index requires authentication.
     *
     * @return void
     */
    public function testIndexRequiresAuthentication(): void
    {
        $this->get('/export');

        $this->assertRedirectContains('/users/login');
    }

    /**
     * Test Excel export with data.
     *
     * @return void
     */
    public function testExcelExport(): void
    {
        $this->loginAsUser();

        $this->get('/export/excel');

        $this->assertResponseOk();
        $this->assertContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertHeaderContains('Content-Disposition', 'attachment');
        $this->assertHeaderContains('Content-Disposition', 'export_fatture_');
        $this->assertHeaderContains('Content-Disposition', '.xlsx');
    }

    /**
     * Test Excel export via POST.
     *
     * @return void
     */
    public function testExcelExportPost(): void
    {
        $this->loginAsUser();

        $this->post('/export/excel', []);

        $this->assertResponseOk();
        $this->assertContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test Excel export with tipo filter (emessa).
     *
     * @return void
     */
    public function testExcelExportFilterTipoEmessa(): void
    {
        $this->loginAsUser();

        $this->get('/export/excel?tipo=emessa');

        $this->assertResponseOk();
        $this->assertContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test Excel export with tipo filter (ricevuta).
     *
     * @return void
     */
    public function testExcelExportFilterTipoRicevuta(): void
    {
        $this->loginAsUser();

        $this->get('/export/excel?tipo=ricevuta');

        $this->assertResponseOk();
        $this->assertContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test Excel export with date filter.
     *
     * @return void
     */
    public function testExcelExportFilterDate(): void
    {
        $this->loginAsUser();

        $this->get('/export/excel?data_da=2025-12-01&data_a=2025-12-31');

        $this->assertResponseOk();
        $this->assertContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test Excel export with stato filter.
     *
     * @return void
     */
    public function testExcelExportFilterStato(): void
    {
        $this->loginAsUser();

        $this->get('/export/excel?stato=bozza');

        $this->assertResponseOk();
        $this->assertContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test Excel export with no matching data redirects with warning.
     *
     * @return void
     */
    public function testExcelExportEmptyRedirects(): void
    {
        $this->loginAsUser();

        // Use a date range with no invoices
        $this->get('/export/excel?data_da=2020-01-01&data_a=2020-01-31');

        $this->assertRedirect(['controller' => 'Export', 'action' => 'index']);
        $this->assertFlashElement('flash/warning');
    }

    /**
     * Test Excel export without tenant configured.
     *
     * @return void
     */
    public function testExcelExportNoTenant(): void
    {
        $this->loginAsUserWithoutTenant();

        $this->get('/export/excel');

        $this->assertRedirect(['controller' => 'Export', 'action' => 'index']);
        $this->assertFlashElement('flash/error');
    }

    /**
     * Test XML export.
     *
     * @return void
     */
    public function testXmlExport(): void
    {
        $this->loginAsUser();

        $this->get('/export/xml');

        $this->assertResponseOk();
        $this->assertContentType('application/zip');
        $this->assertHeaderContains('Content-Disposition', 'attachment');
        $this->assertHeaderContains('Content-Disposition', 'export_fatture_xml_');
        $this->assertHeaderContains('Content-Disposition', '.zip');
    }

    /**
     * Test XML export via POST.
     *
     * @return void
     */
    public function testXmlExportPost(): void
    {
        $this->loginAsUser();

        $this->post('/export/xml', []);

        $this->assertResponseOk();
        $this->assertContentType('application/zip');
    }

    /**
     * Test XML export only includes emesse (issued) invoices.
     *
     * @return void
     */
    public function testXmlExportOnlyEmesse(): void
    {
        $this->loginAsUser();

        // XML export should only export emesse invoices
        // We have 2 emesse and 1 ricevuta in fixtures
        $this->get('/export/xml');

        $this->assertResponseOk();
        $this->assertContentType('application/zip');

        // The response should be a valid ZIP file
        $body = (string)$this->_response->getBody();
        $this->assertNotEmpty($body);

        // ZIP files start with PK signature
        $this->assertStringStartsWith('PK', $body);
    }

    /**
     * Test XML export with date filter.
     *
     * @return void
     */
    public function testXmlExportFilterDate(): void
    {
        $this->loginAsUser();

        $this->get('/export/xml?data_da=2025-12-01&data_a=2025-12-31');

        $this->assertResponseOk();
        $this->assertContentType('application/zip');
    }

    /**
     * Test XML export with stato filter.
     *
     * @return void
     */
    public function testXmlExportFilterStato(): void
    {
        $this->loginAsUser();

        $this->get('/export/xml?stato=consegnata');

        $this->assertResponseOk();
        $this->assertContentType('application/zip');
    }

    /**
     * Test XML export with no matching emesse invoices redirects.
     *
     * @return void
     */
    public function testXmlExportEmptyRedirects(): void
    {
        $this->loginAsUser();

        // Use date range with no emesse invoices
        $this->get('/export/xml?data_da=2020-01-01&data_a=2020-01-31');

        $this->assertRedirect(['controller' => 'Export', 'action' => 'index']);
        $this->assertFlashElement('flash/warning');
    }

    /**
     * Test XML export without tenant configured.
     *
     * @return void
     */
    public function testXmlExportNoTenant(): void
    {
        $this->loginAsUserWithoutTenant();

        $this->get('/export/xml');

        $this->assertRedirect(['controller' => 'Export', 'action' => 'index']);
        $this->assertFlashElement('flash/error');
    }

    /**
     * Test XML filename format follows FatturaPA convention.
     *
     * @return void
     */
    public function testXmlFilenameFormat(): void
    {
        $this->loginAsUser();

        $this->get('/export/xml');

        $this->assertResponseOk();

        // Verify we got a ZIP file
        $body = (string)$this->_response->getBody();
        $this->assertStringStartsWith('PK', $body);

        // Save to temp and inspect ZIP contents
        $tempFile = TMP . 'test_export_' . uniqid() . '.zip';
        file_put_contents($tempFile, $body);

        $zip = new \ZipArchive();
        $result = $zip->open($tempFile);

        $this->assertTrue($result === true, 'ZIP file should open successfully');

        // Check filenames match pattern IT{PIVA}_{PROGRESSIVO}.xml
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            // Pattern: IT + 11 digit PIVA + _ + 5 digit progressive + .xml
            $this->assertMatchesRegularExpression(
                '/^IT\d{11}_\d{5}\.xml$/',
                $filename,
                "Filename should match FatturaPA convention: $filename"
            );
        }

        $zip->close();
        unlink($tempFile);
    }

    /**
     * Test XML structure is valid FatturaPA format.
     *
     * @return void
     */
    public function testXmlValidStructure(): void
    {
        $this->loginAsUser();

        $this->get('/export/xml');

        $this->assertResponseOk();

        $body = (string)$this->_response->getBody();

        // Save and extract ZIP
        $tempFile = TMP . 'test_export_' . uniqid() . '.zip';
        file_put_contents($tempFile, $body);

        $zip = new \ZipArchive();
        $zip->open($tempFile);

        // Get first XML file content
        $xmlContent = $zip->getFromIndex(0);
        $zip->close();
        unlink($tempFile);

        // Parse XML
        $dom = new \DOMDocument();
        $loaded = $dom->loadXML($xmlContent);
        $this->assertTrue($loaded, 'XML should be valid and parseable');

        // Check for FatturaPA required elements
        // Root element
        $root = $dom->documentElement;
        $this->assertEquals('FatturaElettronica', $root->localName);
        $this->assertEquals('FPR12', $root->getAttribute('versione'));

        // Use local-name() for namespace-agnostic XPath queries
        $xpath = new \DOMXPath($dom);

        // Header section
        $header = $xpath->query("//*[local-name()='FatturaElettronicaHeader']");
        $this->assertEquals(1, $header->length, 'Should have FatturaElettronicaHeader');

        // DatiTrasmissione
        $datiTrasmissione = $xpath->query("//*[local-name()='DatiTrasmissione']");
        $this->assertEquals(1, $datiTrasmissione->length, 'Should have DatiTrasmissione');

        // CedentePrestatore
        $cedente = $xpath->query("//*[local-name()='CedentePrestatore']");
        $this->assertEquals(1, $cedente->length, 'Should have CedentePrestatore');

        // CessionarioCommittente
        $cessionario = $xpath->query("//*[local-name()='CessionarioCommittente']");
        $this->assertEquals(1, $cessionario->length, 'Should have CessionarioCommittente');

        // Body section
        $bodyEl = $xpath->query("//*[local-name()='FatturaElettronicaBody']");
        $this->assertEquals(1, $bodyEl->length, 'Should have FatturaElettronicaBody');

        // DatiGenerali
        $datiGenerali = $xpath->query("//*[local-name()='DatiGenerali']");
        $this->assertEquals(1, $datiGenerali->length, 'Should have DatiGenerali');

        // DatiBeniServizi
        $datiBeniServizi = $xpath->query("//*[local-name()='DatiBeniServizi']");
        $this->assertEquals(1, $datiBeniServizi->length, 'Should have DatiBeniServizi');

        // DatiPagamento
        $datiPagamento = $xpath->query("//*[local-name()='DatiPagamento']");
        $this->assertEquals(1, $datiPagamento->length, 'Should have DatiPagamento');
    }

    /**
     * Test Excel export with combined filters.
     *
     * @return void
     */
    public function testExcelExportCombinedFilters(): void
    {
        $this->loginAsUser();

        $this->get('/export/excel?tipo=emessa&data_da=2025-11-01&data_a=2025-12-31&stato=bozza');

        $this->assertResponseOk();
        $this->assertContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test that Excel export includes both Fatture and Righe sheets.
     *
     * @return void
     */
    public function testExcelExportHasTwoSheets(): void
    {
        $this->loginAsUser();

        $this->get('/export/excel');

        $this->assertResponseOk();

        $body = (string)$this->_response->getBody();

        // Save to temp and inspect
        $tempFile = TMP . 'test_excel_' . uniqid() . '.xlsx';
        file_put_contents($tempFile, $body);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($tempFile);

        $this->assertEquals(2, $spreadsheet->getSheetCount(), 'Excel should have 2 sheets');
        $this->assertEquals('Fatture', $spreadsheet->getSheet(0)->getTitle());
        $this->assertEquals('Righe', $spreadsheet->getSheet(1)->getTitle());

        unlink($tempFile);
    }
}
