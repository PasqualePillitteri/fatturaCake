<?php
declare(strict_types=1);

namespace App\Service;

use Cake\I18n\Date;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use DOMDocument;
use DOMXPath;
use ZipArchive;

/**
 * XML Import Service
 *
 * Handles parsing and import of FatturaPA XML files (single or ZIP archives).
 */
class XmlImportService
{
    /**
     * FatturaPA namespace.
     */
    protected const NS_P = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2';

    /**
     * Parsed invoices data.
     *
     * @var array
     */
    protected array $fatture = [];

    /**
     * Validation errors.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * Warnings.
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
        'files_parsed' => 0,
        'fatture_create' => 0,
        'righe_create' => 0,
        'anagrafiche_create' => 0,
        'skipped' => 0,
        'errors' => 0,
    ];

    /**
     * Tenant ID for import.
     *
     * @var int|null
     */
    protected ?int $tenantId = null;

    /**
     * Tenant P.IVA for determining invoice type.
     *
     * @var string|null
     */
    protected ?string $tenantPiva = null;

    /**
     * Parse a file (XML or ZIP).
     *
     * @param string $filepath Path to file.
     * @return bool True if parsing successful.
     */
    public function parseFile(string $filepath): bool
    {
        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        if ($extension === 'zip') {
            return $this->parseZip($filepath);
        }

        if ($extension === 'xml') {
            return $this->parseSingleXml($filepath);
        }

        // Try to handle .p7m files (signed XML)
        if ($extension === 'p7m') {
            return $this->parseP7m($filepath);
        }

        $this->errors[] = "Formato file non supportato: .{$extension}";

        return false;
    }

    /**
     * Parse a ZIP archive containing XML files.
     *
     * @param string $filepath Path to ZIP file.
     * @return bool True if at least one XML was parsed.
     */
    protected function parseZip(string $filepath): bool
    {
        $zip = new ZipArchive();

        if ($zip->open($filepath) !== true) {
            $this->errors[] = 'Impossibile aprire il file ZIP.';

            return false;
        }

        $tempDir = TMP . 'xml_import_' . uniqid() . DS;
        mkdir($tempDir, 0755, true);

        $zip->extractTo($tempDir);
        $zip->close();

        $parsed = 0;
        $files = $this->findXmlFiles($tempDir);

        foreach ($files as $xmlFile) {
            if ($this->parseSingleXml($xmlFile)) {
                $parsed++;
            }
        }

        // Cleanup
        $this->deleteDirectory($tempDir);

        if ($parsed === 0) {
            $this->errors[] = 'Nessun file XML valido trovato nel ZIP.';

            return false;
        }

        return true;
    }

    /**
     * Find all XML files in a directory (recursive).
     *
     * @param string $dir Directory path.
     * @return array List of XML file paths.
     */
    protected function findXmlFiles(string $dir): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $ext = strtolower($file->getExtension());
            if ($ext === 'xml' || $ext === 'p7m') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Parse a single XML file.
     *
     * @param string $filepath Path to XML file.
     * @return bool True if parsing successful.
     */
    protected function parseSingleXml(string $filepath): bool
    {
        $filename = basename($filepath);

        try {
            $content = file_get_contents($filepath);
            if ($content === false) {
                $this->errors[] = "{$filename}: impossibile leggere il file.";

                return false;
            }

            // Remove BOM if present
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;

            if (!@$dom->loadXML($content)) {
                $this->errors[] = "{$filename}: XML non valido.";

                return false;
            }

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('p', self::NS_P);

            // Check if it's a FatturaPA
            $root = $dom->documentElement;
            if ($root === null || strpos($root->nodeName, 'FatturaElettronica') === false) {
                $this->errors[] = "{$filename}: non è un file FatturaPA valido.";

                return false;
            }

            // Parse the invoice(s) - a file can contain multiple bodies
            $bodies = $xpath->query('//FatturaElettronicaBody');
            if ($bodies === false || $bodies->length === 0) {
                $bodies = $xpath->query('//p:FatturaElettronicaBody');
            }

            if ($bodies === false || $bodies->length === 0) {
                $this->errors[] = "{$filename}: nessun body fattura trovato.";

                return false;
            }

            // Get header data (common to all bodies)
            $headerData = $this->parseHeader($xpath);

            foreach ($bodies as $index => $body) {
                $fatturaData = $this->parseBody($xpath, $body, $headerData);
                $fatturaData['_source_file'] = $filename;
                $fatturaData['_body_index'] = $index;
                $this->fatture[] = $fatturaData;
            }

            $this->stats['files_parsed']++;

            return true;
        } catch (\Exception $e) {
            $this->errors[] = "{$filename}: " . $e->getMessage();
            Log::error("XML Import error for {$filename}: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Parse p7m file (extract XML from signed envelope).
     *
     * @param string $filepath Path to p7m file.
     * @return bool True if parsing successful.
     */
    protected function parseP7m(string $filepath): bool
    {
        $content = file_get_contents($filepath);
        if ($content === false) {
            $this->errors[] = basename($filepath) . ': impossibile leggere il file.';

            return false;
        }

        // Try to find XML content within the p7m
        if (preg_match('/<\?xml.*?<\/[^>]+FatturaElettronica>/s', $content, $matches)) {
            $tempXml = TMP . 'extracted_' . uniqid() . '.xml';
            file_put_contents($tempXml, $matches[0]);
            $result = $this->parseSingleXml($tempXml);
            @unlink($tempXml);

            return $result;
        }

        // Try base64 decode
        $decoded = base64_decode($content, true);
        if ($decoded && preg_match('/<\?xml.*?<\/[^>]+FatturaElettronica>/s', $decoded, $matches)) {
            $tempXml = TMP . 'extracted_' . uniqid() . '.xml';
            file_put_contents($tempXml, $matches[0]);
            $result = $this->parseSingleXml($tempXml);
            @unlink($tempXml);

            return $result;
        }

        $this->warnings[] = basename($filepath) . ': file .p7m non supportato (usare openssl per estrarre XML).';

        return false;
    }

    /**
     * Parse FatturaElettronicaHeader.
     *
     * @param DOMXPath $xpath XPath instance.
     * @return array Header data.
     */
    protected function parseHeader(DOMXPath $xpath): array
    {
        $data = [
            'cedente' => [],
            'cessionario' => [],
        ];

        // Cedente/Prestatore
        $data['cedente'] = $this->parseAnagrafica($xpath, '//CedentePrestatore');
        if (empty($data['cedente'])) {
            $data['cedente'] = $this->parseAnagrafica($xpath, '//p:CedentePrestatore');
        }

        // Cessionario/Committente
        $data['cessionario'] = $this->parseAnagrafica($xpath, '//CessionarioCommittente');
        if (empty($data['cessionario'])) {
            $data['cessionario'] = $this->parseAnagrafica($xpath, '//p:CessionarioCommittente');
        }

        return $data;
    }

    /**
     * Parse anagrafica (cedente or cessionario).
     *
     * @param DOMXPath $xpath XPath instance.
     * @param string $basePath Base XPath.
     * @return array Anagrafica data.
     */
    protected function parseAnagrafica(DOMXPath $xpath, string $basePath): array
    {
        $data = [];

        // IdFiscaleIVA
        $data['partita_iva'] = $this->getXPathValue($xpath, "{$basePath}//IdFiscaleIVA/IdCodice");
        $data['id_paese'] = $this->getXPathValue($xpath, "{$basePath}//IdFiscaleIVA/IdPaese") ?: 'IT';

        // CodiceFiscale
        $data['codice_fiscale'] = $this->getXPathValue($xpath, "{$basePath}//CodiceFiscale");

        // Anagrafica
        $data['denominazione'] = $this->getXPathValue($xpath, "{$basePath}//Anagrafica/Denominazione");
        $data['nome'] = $this->getXPathValue($xpath, "{$basePath}//Anagrafica/Nome");
        $data['cognome'] = $this->getXPathValue($xpath, "{$basePath}//Anagrafica/Cognome");

        // If no denominazione, build from nome+cognome
        if (empty($data['denominazione']) && (!empty($data['nome']) || !empty($data['cognome']))) {
            $data['denominazione'] = trim(($data['nome'] ?? '') . ' ' . ($data['cognome'] ?? ''));
        }

        // Sede
        $data['indirizzo'] = $this->getXPathValue($xpath, "{$basePath}//Sede/Indirizzo");
        $data['cap'] = $this->getXPathValue($xpath, "{$basePath}//Sede/CAP");
        $data['citta'] = $this->getXPathValue($xpath, "{$basePath}//Sede/Comune");
        $data['provincia'] = $this->getXPathValue($xpath, "{$basePath}//Sede/Provincia");
        $data['nazione'] = $this->getXPathValue($xpath, "{$basePath}//Sede/Nazione") ?: 'IT';

        // Contatti
        $data['pec'] = $this->getXPathValue($xpath, "{$basePath}//Contatti/PEC");
        $data['email'] = $this->getXPathValue($xpath, "{$basePath}//Contatti/Email");

        // Regime fiscale (solo cedente)
        $data['regime_fiscale'] = $this->getXPathValue($xpath, "{$basePath}//RegimeFiscale");

        return $data;
    }

    /**
     * Parse FatturaElettronicaBody.
     *
     * @param DOMXPath $xpath XPath instance.
     * @param \DOMNode $body Body node.
     * @param array $headerData Header data.
     * @return array Fattura data.
     */
    protected function parseBody(DOMXPath $xpath, \DOMNode $body, array $headerData): array
    {
        $data = $headerData;

        // DatiGeneraliDocumento
        $data['tipo_documento'] = $this->getNodeValue($xpath, './/TipoDocumento', $body)
            ?: $this->getNodeValue($xpath, './/p:TipoDocumento', $body);
        $data['divisa'] = $this->getNodeValue($xpath, './/Divisa', $body)
            ?: $this->getNodeValue($xpath, './/p:Divisa', $body) ?: 'EUR';
        $data['data_emissione'] = $this->getNodeValue($xpath, './/DatiGeneraliDocumento/Data', $body)
            ?: $this->getNodeValue($xpath, './/p:DatiGeneraliDocumento/p:Data', $body);
        $data['numero'] = $this->getNodeValue($xpath, './/DatiGeneraliDocumento/Numero', $body)
            ?: $this->getNodeValue($xpath, './/p:DatiGeneraliDocumento/p:Numero', $body);

        // Causale
        $causali = $xpath->query('.//Causale', $body);
        if ($causali === false || $causali->length === 0) {
            $causali = $xpath->query('.//p:Causale', $body);
        }
        $data['causale'] = '';
        if ($causali !== false) {
            foreach ($causali as $c) {
                $data['causale'] .= $c->textContent . ' ';
            }
        }
        $data['causale'] = trim($data['causale']);

        // Parse righe
        $data['righe'] = $this->parseRighe($xpath, $body);

        // Parse riepilogo IVA for totals
        $data['riepilogo'] = $this->parseRiepilogo($xpath, $body);

        // Calculate totals from riepilogo
        $data['imponibile_totale'] = 0;
        $data['imposta_totale'] = 0;
        foreach ($data['riepilogo'] as $r) {
            $data['imponibile_totale'] += (float)$r['imponibile'];
            $data['imposta_totale'] += (float)$r['imposta'];
        }
        $data['importo_totale'] = $data['imponibile_totale'] + $data['imposta_totale'];

        // ImportoTotaleDocumento (if present, use it)
        $importoTotale = $this->getNodeValue($xpath, './/ImportoTotaleDocumento', $body)
            ?: $this->getNodeValue($xpath, './/p:ImportoTotaleDocumento', $body);
        if ($importoTotale) {
            $data['importo_totale'] = (float)$importoTotale;
        }

        return $data;
    }

    /**
     * Parse DettaglioLinee.
     *
     * @param DOMXPath $xpath XPath instance.
     * @param \DOMNode $body Body node.
     * @return array Righe data.
     */
    protected function parseRighe(DOMXPath $xpath, \DOMNode $body): array
    {
        $righe = [];

        $linee = $xpath->query('.//DettaglioLinee', $body);
        if ($linee === false || $linee->length === 0) {
            $linee = $xpath->query('.//p:DettaglioLinee', $body);
        }

        if ($linee === false) {
            return $righe;
        }

        foreach ($linee as $linea) {
            $riga = [
                'numero_riga' => (int)$this->getNodeValue($xpath, './/NumeroLinea', $linea),
                'descrizione' => $this->getNodeValue($xpath, './/Descrizione', $linea) ?: '',
                'quantita' => (float)($this->getNodeValue($xpath, './/Quantita', $linea) ?: 1),
                'unita_misura' => $this->getNodeValue($xpath, './/UnitaMisura', $linea) ?: 'PZ',
                'prezzo_unitario' => (float)($this->getNodeValue($xpath, './/PrezzoUnitario', $linea) ?: 0),
                'prezzo_totale' => (float)($this->getNodeValue($xpath, './/PrezzoTotale', $linea) ?: 0),
                'aliquota_iva' => (float)($this->getNodeValue($xpath, './/AliquotaIVA', $linea) ?: 0),
                'natura_iva' => $this->getNodeValue($xpath, './/Natura', $linea),
                'codice_articolo' => $this->getNodeValue($xpath, './/CodiceArticolo/CodiceValore', $linea),
            ];

            // Handle sconti
            $sconto = $this->getNodeValue($xpath, './/ScontoMaggiorazione/Percentuale', $linea);
            $riga['sconto_percentuale'] = $sconto ? (float)$sconto : 0;

            $righe[] = $riga;
        }

        return $righe;
    }

    /**
     * Parse DatiRiepilogo.
     *
     * @param DOMXPath $xpath XPath instance.
     * @param \DOMNode $body Body node.
     * @return array Riepilogo data.
     */
    protected function parseRiepilogo(DOMXPath $xpath, \DOMNode $body): array
    {
        $riepilogo = [];

        $dati = $xpath->query('.//DatiRiepilogo', $body);
        if ($dati === false || $dati->length === 0) {
            $dati = $xpath->query('.//p:DatiRiepilogo', $body);
        }

        if ($dati === false) {
            return $riepilogo;
        }

        foreach ($dati as $dato) {
            $riepilogo[] = [
                'aliquota' => (float)($this->getNodeValue($xpath, './/AliquotaIVA', $dato) ?: 0),
                'imponibile' => (float)($this->getNodeValue($xpath, './/ImponibileImporto', $dato) ?: 0),
                'imposta' => (float)($this->getNodeValue($xpath, './/Imposta', $dato) ?: 0),
                'natura' => $this->getNodeValue($xpath, './/Natura', $dato),
            ];
        }

        return $riepilogo;
    }

    /**
     * Get XPath value.
     *
     * @param DOMXPath $xpath XPath instance.
     * @param string $query XPath query.
     * @return string|null Value or null.
     */
    protected function getXPathValue(DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);
        if ($nodes !== false && $nodes->length > 0) {
            return trim($nodes->item(0)->textContent);
        }

        return null;
    }

    /**
     * Get node value relative to context.
     *
     * @param DOMXPath $xpath XPath instance.
     * @param string $query XPath query.
     * @param \DOMNode $context Context node.
     * @return string|null Value or null.
     */
    protected function getNodeValue(DOMXPath $xpath, string $query, \DOMNode $context): ?string
    {
        $nodes = $xpath->query($query, $context);
        if ($nodes !== false && $nodes->length > 0) {
            return trim($nodes->item(0)->textContent);
        }

        // Try with namespace
        $query = str_replace('//', '//p:', $query);
        $query = str_replace('//p:p:', '//p:', $query);
        $nodes = $xpath->query($query, $context);
        if ($nodes !== false && $nodes->length > 0) {
            return trim($nodes->item(0)->textContent);
        }

        return null;
    }

    /**
     * Validate parsed data.
     *
     * @return bool True if valid.
     */
    public function validate(): bool
    {
        $this->errors = [];

        foreach ($this->fatture as $index => $fattura) {
            $file = $fattura['_source_file'] ?? "Fattura #{$index}";

            if (empty($fattura['numero'])) {
                $this->errors[] = "{$file}: numero fattura mancante.";
            }

            if (empty($fattura['data_emissione'])) {
                $this->errors[] = "{$file}: data emissione mancante.";
            }

            if (empty($fattura['cedente']['partita_iva']) && empty($fattura['cedente']['codice_fiscale'])) {
                $this->errors[] = "{$file}: P.IVA o CF cedente mancante.";
            }

            if (empty($fattura['cessionario']['partita_iva']) && empty($fattura['cessionario']['codice_fiscale'])) {
                $this->errors[] = "{$file}: P.IVA o CF cessionario mancante.";
            }

            if (empty($fattura['righe'])) {
                $this->warnings[] = "{$file}: nessuna riga di dettaglio.";
            }
        }

        return empty($this->errors);
    }

    /**
     * Execute import.
     *
     * @param int $tenantId Tenant ID.
     * @param array $options Import options.
     * @return bool True if successful.
     */
    public function import(int $tenantId, array $options = []): bool
    {
        $this->tenantId = $tenantId;
        $options = array_merge([
            'create_anagrafiche' => true,
            'skip_duplicates' => true,
            'tipo_default' => 'ricevuta', // ricevuta = acquisto, emessa = vendita
        ], $options);

        // Get tenant P.IVA for auto-detect
        $tenantsTable = TableRegistry::getTableLocator()->get('Tenants');
        $tenant = $tenantsTable->get($tenantId);
        $this->tenantPiva = $tenant->partita_iva;

        $fattureTable = TableRegistry::getTableLocator()->get('Fatture');
        $righeTable = TableRegistry::getTableLocator()->get('FatturaRighe');
        $connection = $fattureTable->getConnection();

        try {
            $connection->begin();

            foreach ($this->fatture as $fatturaData) {
                // Determine tipo (attiva/passiva)
                $tipo = $this->determineTipoFattura($fatturaData, $options['tipo_default']);

                // Get the correct anagrafica (cliente for attiva, fornitore for passiva)
                if ($tipo === 'attiva') {
                    $anagraficaData = $fatturaData['cessionario'];
                    $anagraficaTipo = 'cliente';
                } else {
                    $anagraficaData = $fatturaData['cedente'];
                    $anagraficaTipo = 'fornitore';
                }

                // Check for duplicate
                if ($options['skip_duplicates']) {
                    $existing = $fattureTable->find()
                        ->where([
                            'tenant_id' => $tenantId,
                            'numero' => $fatturaData['numero'],
                            'direzione' => $tipo,
                        ])
                        ->first();

                    if ($existing) {
                        $this->stats['skipped']++;
                        $this->warnings[] = "Fattura {$fatturaData['numero']} già esistente, saltata.";
                        continue;
                    }
                }

                // Find or create anagrafica
                $anagraficaId = $this->findOrCreateAnagrafica($anagraficaData, $anagraficaTipo, $options);
                if (!$anagraficaId) {
                    $this->stats['errors']++;
                    continue;
                }

                // Create fattura
                $fattura = $fattureTable->newEntity([
                    'tenant_id' => $tenantId,
                    'anagrafica_id' => $anagraficaId,
                    'numero' => $fatturaData['numero'],
                    'data' => $fatturaData['data_emissione'],
                    'anno' => date('Y', strtotime($fatturaData['data_emissione'])),
                    'tipo_documento' => $fatturaData['tipo_documento'] ?: 'TD01',
                    'direzione' => $tipo,
                    'divisa' => $fatturaData['divisa'] ?: 'EUR',
                    'totale_documento' => $fatturaData['importo_totale'],
                    'imponibile_totale' => $fatturaData['imponibile_totale'],
                    'iva_totale' => $fatturaData['imposta_totale'],
                    'causale' => $fatturaData['causale'] ?: null,
                    'stato_sdi' => 'importato',
                ]);

                if (!$fattureTable->save($fattura)) {
                    $this->errors[] = "Errore salvataggio fattura {$fatturaData['numero']}: " .
                        json_encode($fattura->getErrors());
                    $this->stats['errors']++;
                    continue;
                }

                $this->stats['fatture_create']++;

                // Create righe
                foreach ($fatturaData['righe'] as $rigaData) {
                    $riga = $righeTable->newEntity([
                        'numero_linea' => $rigaData['numero_riga'] ?: 1,
                        'descrizione' => $rigaData['descrizione'],
                        'quantita' => $rigaData['quantita'],
                        'unita_misura' => $rigaData['unita_misura'],
                        'prezzo_unitario' => $rigaData['prezzo_unitario'],
                        'aliquota_iva' => $rigaData['aliquota_iva'],
                        'natura' => $rigaData['natura_iva'],
                        'sconto_maggiorazione_percentuale' => $rigaData['sconto_percentuale'] ?? 0,
                        'prezzo_totale' => $rigaData['prezzo_totale'],
                        'ritenuta' => false,
                        'sort_order' => 0,
                    ]);
                    // Assegna fattura_id direttamente (campo protetto nell'entity)
                    $riga->setAccess('fattura_id', true);
                    $riga->fattura_id = $fattura->id;

                    if ($righeTable->save($riga)) {
                        $this->stats['righe_create']++;
                    } else {
                        $this->warnings[] = "Riga non salvata per fattura {$fatturaData['numero']}: " .
                            json_encode($riga->getErrors());
                    }
                }
            }

            $connection->commit();

            return true;
        } catch (\Exception $e) {
            $connection->rollback();
            $this->errors[] = 'Errore import: ' . $e->getMessage();
            Log::error('XML Import error: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Determine if fattura is attiva or passiva.
     *
     * @param array $fatturaData Fattura data.
     * @param string $default Default tipo.
     * @return string 'attiva' or 'passiva'.
     */
    protected function determineTipoFattura(array $fatturaData, string $default): string
    {
        if ($this->tenantPiva) {
            // If cedente P.IVA matches tenant -> fattura emessa (vendita)
            if ($fatturaData['cedente']['partita_iva'] === $this->tenantPiva) {
                return 'emessa';
            }
            // If cessionario P.IVA matches tenant -> fattura ricevuta (acquisto)
            if ($fatturaData['cessionario']['partita_iva'] === $this->tenantPiva) {
                return 'ricevuta';
            }
        }

        return $default;
    }

    /**
     * Find or create anagrafica.
     *
     * @param array $data Anagrafica data.
     * @param string $tipo Tipo (cliente/fornitore).
     * @param array $options Import options.
     * @return int|null Anagrafica ID or null.
     */
    protected function findOrCreateAnagrafica(array $data, string $tipo, array $options): ?int
    {
        $anagraficheTable = TableRegistry::getTableLocator()->get('Anagrafiche');

        // Search by P.IVA
        if (!empty($data['partita_iva'])) {
            $existing = $anagraficheTable->find()
                ->where([
                    'tenant_id' => $this->tenantId,
                    'partita_iva' => $data['partita_iva'],
                ])
                ->first();

            if ($existing) {
                return $existing->id;
            }
        }

        // Search by CF
        if (!empty($data['codice_fiscale'])) {
            $existing = $anagraficheTable->find()
                ->where([
                    'tenant_id' => $this->tenantId,
                    'codice_fiscale' => $data['codice_fiscale'],
                ])
                ->first();

            if ($existing) {
                return $existing->id;
            }
        }

        // Create if allowed
        if (!$options['create_anagrafiche']) {
            $this->errors[] = "Anagrafica non trovata: {$data['denominazione']}";

            return null;
        }

        $anagrafica = $anagraficheTable->newEntity([
            'tenant_id' => $this->tenantId,
            'tipo' => $tipo,
            'denominazione' => $data['denominazione'] ?: 'Sconosciuto',
            'partita_iva' => $data['partita_iva'],
            'codice_fiscale' => $data['codice_fiscale'],
            'indirizzo' => $data['indirizzo'] ?: 'Non specificato',
            'cap' => $data['cap'] ?: '00000',
            'comune' => $data['citta'] ?: 'Non specificato',
            'provincia' => $data['provincia'],
            'nazione' => $data['nazione'] ?: 'IT',
            'pec' => $data['pec'],
            'email' => $data['email'],
            'codice_sdi' => '0000000',
            'regime_fiscale' => $data['regime_fiscale'] ?: 'RF01',
        ]);

        if ($anagraficheTable->save($anagrafica)) {
            $this->stats['anagrafiche_create']++;

            return $anagrafica->id;
        }

        $this->errors[] = "Impossibile creare anagrafica: {$data['denominazione']}";

        return null;
    }

    /**
     * Delete directory recursively.
     *
     * @param string $dir Directory path.
     * @return void
     */
    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Get parsed fatture.
     *
     * @return array
     */
    public function getFatture(): array
    {
        return $this->fatture;
    }

    /**
     * Get errors.
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
     * Get stats.
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Check if has errors.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get preview data.
     *
     * @return array
     */
    public function getPreviewData(): array
    {
        return [
            'fatture' => $this->fatture,
            'totals' => [
                'fatture' => count($this->fatture),
                'righe' => array_sum(array_map(fn($f) => count($f['righe'] ?? []), $this->fatture)),
            ],
            'errors' => $this->errors,
            'warnings' => $this->warnings,
        ];
    }
}
