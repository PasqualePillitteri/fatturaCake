<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\FatturaXmlGenerator;
use Cake\Http\Response;
use Cake\I18n\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

/**
 * Export Controller
 *
 * Handles export of invoices to Excel and XML formats.
 */
class ExportController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        if ($this->components()->has('Crud')) {
            $this->components()->unload('Crud');
        }
    }

    /**
     * Export page - selection form.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->set('title', 'Export Fatture');
    }

    /**
     * Export fatture to Excel.
     *
     * @return \Cake\Http\Response
     */
    public function excel(): Response
    {
        $this->request->allowMethod(['post', 'get']);

        $tenantId = $this->getCurrentTenantId();
        if (!$tenantId) {
            $this->Flash->error(__('Tenant non configurato.'));

            return $this->redirect(['action' => 'index']);
        }

        // Get filters
        $filters = $this->getFilters();

        // Load fatture
        $fattureTable = $this->fetchTable('Fatture');
        $query = $fattureTable->find()
            ->contain(['Anagrafiche', 'FatturaRighe'])
            ->where(['Fatture.tenant_id' => $tenantId]);

        $this->applyFilters($query, $filters);

        $fatture = $query->orderBy(['Fatture.data' => 'DESC'])->all();

        if ($fatture->isEmpty()) {
            $this->Flash->warning(__('Nessuna fattura da esportare con i filtri selezionati.'));

            return $this->redirect(['action' => 'index']);
        }

        // Create Excel
        $spreadsheet = new Spreadsheet();

        // Fatture sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Fatture');

        // Headers
        $headers = [
            'Numero', 'Data', 'Tipo Doc', 'Tipo', 'Cliente/Fornitore', 'P.IVA', 'CF',
            'Imponibile', 'IVA', 'Totale', 'Divisa', 'Stato', 'Causale',
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->getStyle('A1:M1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2E8F0');

        // Data
        $row = 2;
        foreach ($fatture as $fattura) {
            $sheet->fromArray([
                $fattura->numero,
                $fattura->data ? $fattura->data->format('Y-m-d') : '',
                $fattura->tipo_documento,
                $fattura->direzione,
                $fattura->anagrafica->denominazione ?? '',
                $fattura->anagrafica->partita_iva ?? '',
                $fattura->anagrafica->codice_fiscale ?? '',
                (float)$fattura->imponibile_totale,
                (float)$fattura->iva_totale,
                (float)$fattura->totale_documento,
                $fattura->divisa,
                $fattura->stato_sdi,
                $fattura->causale,
            ], null, "A{$row}");
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Righe sheet
        $righeSheet = $spreadsheet->createSheet();
        $righeSheet->setTitle('Righe');

        $righeHeaders = [
            'Numero Fattura', 'N. Riga', 'Descrizione', 'Quantita', 'UM',
            'Prezzo Unit.', 'Aliquota IVA', 'Natura', 'Sconto %', 'Importo',
        ];
        $righeSheet->fromArray($righeHeaders, null, 'A1');
        $righeSheet->getStyle('A1:J1')->getFont()->setBold(true);
        $righeSheet->getStyle('A1:J1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2E8F0');

        $row = 2;
        foreach ($fatture as $fattura) {
            foreach ($fattura->fattura_righe as $riga) {
                $righeSheet->fromArray([
                    $fattura->numero,
                    $riga->numero_linea,
                    $riga->descrizione,
                    (float)$riga->quantita,
                    $riga->unita_misura,
                    (float)$riga->prezzo_unitario,
                    (float)$riga->aliquota_iva,
                    $riga->natura,
                    (float)($riga->sconto_maggiorazione_percentuale ?? 0),
                    (float)$riga->prezzo_totale,
                ], null, "A{$row}");
                $row++;
            }
        }

        foreach (range('A', 'J') as $col) {
            $righeSheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $writer = new Xlsx($spreadsheet);
        $filename = 'export_fatture_' . date('Y-m-d_His') . '.xlsx';

        $temp = TMP . $filename;
        $writer->save($temp);

        $response = $this->response
            ->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->withFile($temp, ['download' => true, 'name' => $filename]);

        // Cleanup after response
        register_shutdown_function(function () use ($temp) {
            @unlink($temp);
        });

        return $response;
    }

    /**
     * Export fatture to XML (FatturaPA format) as ZIP.
     *
     * @return \Cake\Http\Response
     */
    public function xml(): Response
    {
        $this->request->allowMethod(['post', 'get']);

        $tenantId = $this->getCurrentTenantId();
        if (!$tenantId) {
            $this->Flash->error(__('Tenant non configurato.'));

            return $this->redirect(['action' => 'index']);
        }

        // Get filters
        $filters = $this->getFilters();

        // Load fatture with all related data
        $fattureTable = $this->fetchTable('Fatture');
        $query = $fattureTable->find()
            ->contain(['Anagrafiche', 'FatturaRighe'])
            ->where([
                'Fatture.tenant_id' => $tenantId,
                'Fatture.direzione' => 'emessa', // Solo fatture emesse per XML
            ]);

        // Apply only date and stato filters, not tipo (XML is always for emesse only)
        $this->applyFilters($query, $filters, true);

        $fatture = $query->orderBy(['Fatture.data' => 'DESC'])->all();

        if ($fatture->isEmpty()) {
            $this->Flash->warning(__('Nessuna fattura emessa da esportare. L\'export XML è disponibile solo per fatture di vendita.'));

            return $this->redirect(['action' => 'index']);
        }

        // Get tenant (cedente) data
        $tenantsTable = $this->fetchTable('Tenants');
        $tenant = $tenantsTable->get($tenantId);

        // Get SDI config if exists
        $configSdiTable = $this->fetchTable('ConfigurazioniSdi');
        $configSdi = $configSdiTable->find()
            ->where(['tenant_id' => $tenantId])
            ->first();

        // Create temp directory for XMLs
        $tempDir = TMP . 'export_xml_' . uniqid() . DS;
        mkdir($tempDir, 0755, true);

        $xmlFiles = [];
        $errors = [];

        foreach ($fatture as $index => $fattura) {
            try {
                $xml = $this->generateFatturaXml($fattura, $tenant, $configSdi, $index + 1);
                $filename = $this->generateXmlFilename($tenant, $fattura, $index + 1);
                $filepath = $tempDir . $filename;
                file_put_contents($filepath, $xml);
                $xmlFiles[] = $filepath;
            } catch (\Exception $e) {
                $errors[] = "Fattura {$fattura->numero}: " . $e->getMessage();
            }
        }

        if (empty($xmlFiles)) {
            $this->deleteDirectory($tempDir);
            $this->Flash->error(__('Impossibile generare XML. ') . implode(', ', $errors));

            return $this->redirect(['action' => 'index']);
        }

        // Create ZIP
        $zipFilename = 'export_fatture_xml_' . date('Y-m-d_His') . '.zip';
        $zipPath = TMP . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            foreach ($xmlFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Cleanup temp dir
        $this->deleteDirectory($tempDir);

        if (!empty($errors)) {
            $this->Flash->warning(__('Alcuni XML non sono stati generati: ') . implode(', ', $errors));
        }

        $response = $this->response
            ->withType('application/zip')
            ->withHeader('Content-Disposition', "attachment; filename=\"{$zipFilename}\"")
            ->withFile($zipPath, ['download' => true, 'name' => $zipFilename]);

        register_shutdown_function(function () use ($zipPath) {
            @unlink($zipPath);
        });

        return $response;
    }

    /**
     * Generate FatturaPA XML for a single invoice.
     *
     * @param \App\Model\Entity\Fattura $fattura Invoice entity.
     * @param \App\Model\Entity\Tenant $tenant Tenant entity.
     * @param \App\Model\Entity\ConfigurazioniSdi|null $configSdi SDI config.
     * @param int $progressivo Progressive number.
     * @return string XML content.
     */
    protected function generateFatturaXml($fattura, $tenant, $configSdi, int $progressivo): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        // Root element
        $root = $dom->createElementNS(
            'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2',
            'p:FatturaElettronica'
        );
        $root->setAttribute('versione', 'FPR12');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $dom->appendChild($root);

        // Header
        $header = $dom->createElement('FatturaElettronicaHeader');
        $root->appendChild($header);

        // DatiTrasmissione
        $datiTrasmissione = $dom->createElement('DatiTrasmissione');
        $header->appendChild($datiTrasmissione);

        $idTrasmittente = $dom->createElement('IdTrasmittente');
        $datiTrasmissione->appendChild($idTrasmittente);
        $idTrasmittente->appendChild($dom->createElement('IdPaese', 'IT'));
        $idTrasmittente->appendChild($dom->createElement('IdCodice', $tenant->partita_iva ?? ''));

        $datiTrasmissione->appendChild($dom->createElement('ProgressivoInvio', str_pad((string)$progressivo, 5, '0', STR_PAD_LEFT)));
        $datiTrasmissione->appendChild($dom->createElement('FormatoTrasmissione', 'FPR12'));
        $datiTrasmissione->appendChild($dom->createElement('CodiceDestinatario', $fattura->anagrafica->codice_sdi ?? '0000000'));

        if (!empty($fattura->anagrafica->pec)) {
            $datiTrasmissione->appendChild($dom->createElement('PECDestinatario', $fattura->anagrafica->pec));
        }

        // CedentePrestatore
        $cedente = $dom->createElement('CedentePrestatore');
        $header->appendChild($cedente);

        $datiAnagCedente = $dom->createElement('DatiAnagrafici');
        $cedente->appendChild($datiAnagCedente);

        $idFiscaleCedente = $dom->createElement('IdFiscaleIVA');
        $datiAnagCedente->appendChild($idFiscaleCedente);
        $idFiscaleCedente->appendChild($dom->createElement('IdPaese', 'IT'));
        $idFiscaleCedente->appendChild($dom->createElement('IdCodice', $tenant->partita_iva ?? ''));

        if (!empty($tenant->codice_fiscale)) {
            $datiAnagCedente->appendChild($dom->createElement('CodiceFiscale', $tenant->codice_fiscale));
        }

        $anagraficaCedente = $dom->createElement('Anagrafica');
        $datiAnagCedente->appendChild($anagraficaCedente);
        $anagraficaCedente->appendChild($dom->createElement('Denominazione', $this->xmlEncode($tenant->nome)));

        $datiAnagCedente->appendChild($dom->createElement('RegimeFiscale', $configSdi->regime_fiscale ?? 'RF01'));

        $sedeCedente = $dom->createElement('Sede');
        $cedente->appendChild($sedeCedente);
        $sedeCedente->appendChild($dom->createElement('Indirizzo', $this->xmlEncode($tenant->indirizzo ?? 'Via non specificata')));
        $sedeCedente->appendChild($dom->createElement('CAP', $tenant->cap ?? '00000'));
        $sedeCedente->appendChild($dom->createElement('Comune', $this->xmlEncode($tenant->citta ?? $tenant->comune ?? 'Non specificato')));
        if (!empty($tenant->provincia)) {
            $sedeCedente->appendChild($dom->createElement('Provincia', $tenant->provincia));
        }
        $sedeCedente->appendChild($dom->createElement('Nazione', 'IT'));

        // CessionarioCommittente
        $cessionario = $dom->createElement('CessionarioCommittente');
        $header->appendChild($cessionario);

        $datiAnagCess = $dom->createElement('DatiAnagrafici');
        $cessionario->appendChild($datiAnagCess);

        if (!empty($fattura->anagrafica->partita_iva)) {
            $idFiscaleCess = $dom->createElement('IdFiscaleIVA');
            $datiAnagCess->appendChild($idFiscaleCess);
            $idFiscaleCess->appendChild($dom->createElement('IdPaese', $fattura->anagrafica->nazione ?? 'IT'));
            $idFiscaleCess->appendChild($dom->createElement('IdCodice', $fattura->anagrafica->partita_iva));
        }

        if (!empty($fattura->anagrafica->codice_fiscale)) {
            $datiAnagCess->appendChild($dom->createElement('CodiceFiscale', $fattura->anagrafica->codice_fiscale));
        }

        $anagraficaCess = $dom->createElement('Anagrafica');
        $datiAnagCess->appendChild($anagraficaCess);
        $anagraficaCess->appendChild($dom->createElement('Denominazione', $this->xmlEncode($fattura->anagrafica->denominazione ?? '')));

        $sedeCess = $dom->createElement('Sede');
        $cessionario->appendChild($sedeCess);
        $sedeCess->appendChild($dom->createElement('Indirizzo', $this->xmlEncode($fattura->anagrafica->indirizzo ?? 'Via non specificata')));
        $sedeCess->appendChild($dom->createElement('CAP', $fattura->anagrafica->cap ?? '00000'));
        $sedeCess->appendChild($dom->createElement('Comune', $this->xmlEncode($fattura->anagrafica->comune ?? 'Non specificato')));
        if (!empty($fattura->anagrafica->provincia)) {
            $sedeCess->appendChild($dom->createElement('Provincia', $fattura->anagrafica->provincia));
        }
        $sedeCess->appendChild($dom->createElement('Nazione', $fattura->anagrafica->nazione ?? 'IT'));

        // Body
        $body = $dom->createElement('FatturaElettronicaBody');
        $root->appendChild($body);

        // DatiGenerali
        $datiGenerali = $dom->createElement('DatiGenerali');
        $body->appendChild($datiGenerali);

        $datiGeneraliDoc = $dom->createElement('DatiGeneraliDocumento');
        $datiGenerali->appendChild($datiGeneraliDoc);
        $datiGeneraliDoc->appendChild($dom->createElement('TipoDocumento', $fattura->tipo_documento ?? 'TD01'));
        $datiGeneraliDoc->appendChild($dom->createElement('Divisa', $fattura->divisa ?? 'EUR'));
        $datiGeneraliDoc->appendChild($dom->createElement('Data', $fattura->data->format('Y-m-d')));
        $datiGeneraliDoc->appendChild($dom->createElement('Numero', $fattura->numero));

        if (!empty($fattura->causale)) {
            $datiGeneraliDoc->appendChild($dom->createElement('Causale', $this->xmlEncode(substr($fattura->causale, 0, 200))));
        }

        // DatiBeniServizi
        $datiBeniServizi = $dom->createElement('DatiBeniServizi');
        $body->appendChild($datiBeniServizi);

        // DettaglioLinee
        $riepilogoIva = [];
        foreach ($fattura->fattura_righe as $riga) {
            $dettaglio = $dom->createElement('DettaglioLinee');
            $datiBeniServizi->appendChild($dettaglio);

            $dettaglio->appendChild($dom->createElement('NumeroLinea', (string)$riga->numero_linea));
            $dettaglio->appendChild($dom->createElement('Descrizione', $this->xmlEncode(substr($riga->descrizione, 0, 1000))));
            $dettaglio->appendChild($dom->createElement('Quantita', number_format((float)$riga->quantita, 2, '.', '')));
            $dettaglio->appendChild($dom->createElement('UnitaMisura', $riga->unita_misura ?? 'PZ'));
            $dettaglio->appendChild($dom->createElement('PrezzoUnitario', number_format((float)$riga->prezzo_unitario, 2, '.', '')));
            $dettaglio->appendChild($dom->createElement('PrezzoTotale', number_format((float)$riga->prezzo_totale, 2, '.', '')));
            $dettaglio->appendChild($dom->createElement('AliquotaIVA', number_format((float)$riga->aliquota_iva, 2, '.', '')));

            if ((float)$riga->aliquota_iva == 0 && !empty($riga->natura)) {
                $dettaglio->appendChild($dom->createElement('Natura', $riga->natura));
            }

            // Accumula per riepilogo
            $aliquota = number_format((float)$riga->aliquota_iva, 2, '.', '');
            $natura = ((float)$riga->aliquota_iva == 0) ? ($riga->natura ?? 'N1') : null;
            $key = $aliquota . '_' . ($natura ?? '');

            if (!isset($riepilogoIva[$key])) {
                $riepilogoIva[$key] = [
                    'aliquota' => (float)$aliquota,
                    'imponibile' => 0,
                    'imposta' => 0,
                    'natura' => $natura,
                ];
            }
            $riepilogoIva[$key]['imponibile'] += (float)$riga->prezzo_totale;
            $riepilogoIva[$key]['imposta'] += (float)$riga->prezzo_totale * (float)$riga->aliquota_iva / 100;
        }

        // DatiRiepilogo
        foreach ($riepilogoIva as $riep) {
            $datiRiepilogo = $dom->createElement('DatiRiepilogo');
            $datiBeniServizi->appendChild($datiRiepilogo);

            $datiRiepilogo->appendChild($dom->createElement('AliquotaIVA', number_format($riep['aliquota'], 2, '.', '')));
            if ($riep['natura']) {
                $datiRiepilogo->appendChild($dom->createElement('Natura', $riep['natura']));
            }
            $datiRiepilogo->appendChild($dom->createElement('ImponibileImporto', number_format($riep['imponibile'], 2, '.', '')));
            $datiRiepilogo->appendChild($dom->createElement('Imposta', number_format($riep['imposta'], 2, '.', '')));
            $datiRiepilogo->appendChild($dom->createElement('EsigibilitaIVA', 'I'));
        }

        // DatiPagamento
        $datiPagamento = $dom->createElement('DatiPagamento');
        $body->appendChild($datiPagamento);
        $datiPagamento->appendChild($dom->createElement('CondizioniPagamento', 'TP02'));

        $dettaglioPagamento = $dom->createElement('DettaglioPagamento');
        $datiPagamento->appendChild($dettaglioPagamento);
        $dettaglioPagamento->appendChild($dom->createElement('ModalitaPagamento', $fattura->modalita_pagamento ?? 'MP05'));
        $dettaglioPagamento->appendChild($dom->createElement('ImportoPagamento', number_format((float)$fattura->totale_documento, 2, '.', '')));

        return $dom->saveXML();
    }

    /**
     * Generate XML filename.
     *
     * @param \App\Model\Entity\Tenant $tenant Tenant.
     * @param \App\Model\Entity\Fattura $fattura Invoice.
     * @param int $progressivo Progressive number.
     * @return string Filename.
     */
    protected function generateXmlFilename($tenant, $fattura, int $progressivo): string
    {
        $countryCode = 'IT';
        $piva = $tenant->partita_iva ?? '00000000000';
        $prog = str_pad((string)$progressivo, 5, '0', STR_PAD_LEFT);

        return "{$countryCode}{$piva}_{$prog}.xml";
    }

    /**
     * Encode string for XML.
     *
     * @param string|null $value Value.
     * @return string Encoded value.
     */
    protected function xmlEncode(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get filters from request.
     *
     * @return array Filters.
     */
    protected function getFilters(): array
    {
        return [
            'tipo' => $this->request->getQuery('tipo') ?: $this->request->getData('tipo'),
            'data_da' => $this->request->getQuery('data_da') ?: $this->request->getData('data_da'),
            'data_a' => $this->request->getQuery('data_a') ?: $this->request->getData('data_a'),
            'stato' => $this->request->getQuery('stato') ?: $this->request->getData('stato'),
        ];
    }

    /**
     * Apply filters to query.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query.
     * @param array $filters Filters.
     * @param bool $skipTipo Skip tipo/direzione filter (for XML export).
     * @return void
     */
    protected function applyFilters($query, array $filters, bool $skipTipo = false): void
    {
        if (!$skipTipo && !empty($filters['tipo'])) {
            // Map frontend values to database values
            $direzioneMap = [
                'attiva' => 'emessa',
                'passiva' => 'ricevuta',
                'emessa' => 'emessa',
                'ricevuta' => 'ricevuta',
            ];
            $direzione = $direzioneMap[$filters['tipo']] ?? $filters['tipo'];
            $query->where(['Fatture.direzione' => $direzione]);
        }

        if (!empty($filters['data_da'])) {
            $query->where(['Fatture.data >=' => $filters['data_da']]);
        }

        if (!empty($filters['data_a'])) {
            $query->where(['Fatture.data <=' => $filters['data_a']]);
        }

        if (!empty($filters['stato'])) {
            $query->where(['Fatture.stato_sdi' => $filters['stato']]);
        }
    }

    /**
     * Delete directory recursively.
     *
     * @param string $dir Directory.
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
     * Get current tenant ID.
     *
     * @return int|null
     */
    protected function getCurrentTenantId(): ?int
    {
        $identity = $this->Authentication->getIdentity();

        return $identity ? $identity->get('tenant_id') : null;
    }
}
