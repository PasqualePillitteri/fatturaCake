<?php
declare(strict_types=1);

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Excel Template Service
 *
 * Generates Excel template for invoice import.
 */
class ExcelTemplateService
{
    /**
     * Tipo documento options for FatturaPA.
     */
    protected const TIPO_DOCUMENTO = [
        'TD01' => 'Fattura',
        'TD02' => 'Acconto/Anticipo su fattura',
        'TD03' => 'Acconto/Anticipo su parcella',
        'TD04' => 'Nota di credito',
        'TD05' => 'Nota di debito',
        'TD06' => 'Parcella',
        'TD24' => 'Fattura differita',
        'TD25' => 'Fattura differita (art. 21 c. 6 lett. a)',
    ];

    /**
     * Aliquote IVA comuni.
     */
    protected const ALIQUOTE_IVA = ['22', '10', '5', '4', '0'];

    /**
     * Nature IVA per operazioni esenti/escluse.
     */
    protected const NATURE_IVA = [
        'N1' => 'Escluse ex art. 15',
        'N2.1' => 'Non soggette - artt. 7-7septies',
        'N2.2' => 'Non soggette - altri casi',
        'N3.1' => 'Non imponibili - esportazioni',
        'N3.2' => 'Non imponibili - cessioni intracomunitarie',
        'N3.3' => 'Non imponibili - cessioni San Marino',
        'N3.4' => 'Non imponibili - operazioni assimilate',
        'N3.5' => 'Non imponibili - a seguito dichiarazioni intento',
        'N3.6' => 'Non imponibili - altre operazioni',
        'N4' => 'Esenti',
        'N5' => 'Regime del margine',
        'N6.1' => 'Inversione contabile - rottami',
        'N6.2' => 'Inversione contabile - oro e argento',
        'N6.3' => 'Inversione contabile - subappalto edilizia',
        'N6.4' => 'Inversione contabile - cessione fabbricati',
        'N6.5' => 'Inversione contabile - telefoni cellulari',
        'N6.6' => 'Inversione contabile - prodotti elettronici',
        'N6.7' => 'Inversione contabile - prestazioni settore edile',
        'N6.8' => 'Inversione contabile - settore energetico',
        'N6.9' => 'Inversione contabile - altri casi',
        'N7' => 'IVA assolta in altro stato UE',
    ];

    /**
     * Generate the import template spreadsheet.
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function generateTemplate(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // Foglio Fatture
        $this->createFattureSheet($spreadsheet);

        // Foglio Righe
        $this->createRigheSheet($spreadsheet);

        // Foglio Istruzioni
        $this->createIstruzioniSheet($spreadsheet);

        // Attiva il foglio Fatture
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Save template to file.
     *
     * @param string $filepath Path to save the file.
     * @return void
     */
    public function saveTemplate(string $filepath): void
    {
        $spreadsheet = $this->generateTemplate();
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);
    }

    /**
     * Get template as stream for download.
     *
     * @return string Binary content.
     */
    public function getTemplateContent(): string
    {
        $spreadsheet = $this->generateTemplate();
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }

    /**
     * Create the Fatture sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet Spreadsheet.
     * @return void
     */
    protected function createFattureSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Fatture');

        // Headers
        $headers = [
            'A' => ['title' => 'numero*', 'width' => 15],
            'B' => ['title' => 'data_emissione*', 'width' => 15],
            'C' => ['title' => 'tipo_documento*', 'width' => 18],
            'D' => ['title' => 'cliente_piva', 'width' => 15],
            'E' => ['title' => 'cliente_cf', 'width' => 18],
            'F' => ['title' => 'cliente_denominazione*', 'width' => 35],
            'G' => ['title' => 'cliente_indirizzo', 'width' => 30],
            'H' => ['title' => 'cliente_cap', 'width' => 10],
            'I' => ['title' => 'cliente_citta', 'width' => 20],
            'J' => ['title' => 'cliente_provincia', 'width' => 12],
            'K' => ['title' => 'cliente_nazione', 'width' => 12],
            'L' => ['title' => 'cliente_pec', 'width' => 30],
            'M' => ['title' => 'cliente_codice_sdi', 'width' => 15],
            'N' => ['title' => 'importo_totale*', 'width' => 15],
            'O' => ['title' => 'imponibile_totale*', 'width' => 18],
            'P' => ['title' => 'imposta_totale*', 'width' => 15],
            'Q' => ['title' => 'divisa', 'width' => 10],
            'R' => ['title' => 'causale', 'width' => 40],
            'S' => ['title' => 'note', 'width' => 40],
        ];

        $this->setHeaders($sheet, $headers);

        // Data validation for tipo_documento (C2:C1000)
        $tipoDocValues = implode(',', array_keys(self::TIPO_DOCUMENTO));
        $this->addDropdownValidation($sheet, 'C2:C1000', $tipoDocValues);

        // Example row
        $sheet->setCellValue('A2', 'FT-2025-001');
        $sheet->setCellValue('B2', date('Y-m-d'));
        $sheet->setCellValue('C2', 'TD01');
        $sheet->setCellValue('D2', '12345678901');
        $sheet->setCellValue('E2', '');
        $sheet->setCellValue('F2', 'Azienda Cliente Srl');
        $sheet->setCellValue('G2', 'Via Roma 123');
        $sheet->setCellValue('H2', '00100');
        $sheet->setCellValue('I2', 'Roma');
        $sheet->setCellValue('J2', 'RM');
        $sheet->setCellValue('K2', 'IT');
        $sheet->setCellValue('L2', 'cliente@pec.it');
        $sheet->setCellValue('M2', '0000000');
        $sheet->setCellValue('N2', 122.00);
        $sheet->setCellValue('O2', 100.00);
        $sheet->setCellValue('P2', 22.00);
        $sheet->setCellValue('Q2', 'EUR');
        $sheet->setCellValue('R2', 'Vendita prodotti');
        $sheet->setCellValue('S2', '');

        // Format example row
        $sheet->getStyle('A2:S2')->getFont()->setItalic(true);
        $sheet->getStyle('A2:S2')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFF3CD');
    }

    /**
     * Create the Righe sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet Spreadsheet.
     * @return void
     */
    protected function createRigheSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Righe');

        // Headers
        $headers = [
            'A' => ['title' => 'numero_fattura*', 'width' => 18],
            'B' => ['title' => 'numero_riga*', 'width' => 12],
            'C' => ['title' => 'prodotto_codice', 'width' => 18],
            'D' => ['title' => 'descrizione*', 'width' => 45],
            'E' => ['title' => 'quantita*', 'width' => 12],
            'F' => ['title' => 'unita_misura', 'width' => 14],
            'G' => ['title' => 'prezzo_unitario*', 'width' => 16],
            'H' => ['title' => 'aliquota_iva*', 'width' => 14],
            'I' => ['title' => 'natura_iva', 'width' => 12],
            'J' => ['title' => 'sconto_percentuale', 'width' => 18],
            'K' => ['title' => 'importo_riga*', 'width' => 15],
        ];

        $this->setHeaders($sheet, $headers);

        // Data validation for aliquota_iva
        $aliquoteValues = implode(',', self::ALIQUOTE_IVA);
        $this->addDropdownValidation($sheet, 'H2:H1000', $aliquoteValues);

        // Data validation for natura_iva
        $natureValues = implode(',', array_keys(self::NATURE_IVA));
        $this->addDropdownValidation($sheet, 'I2:I1000', $natureValues);

        // Example rows
        $sheet->setCellValue('A2', 'FT-2025-001');
        $sheet->setCellValue('B2', 1);
        $sheet->setCellValue('C2', 'PROD001');
        $sheet->setCellValue('D2', 'Prodotto di esempio');
        $sheet->setCellValue('E2', 2);
        $sheet->setCellValue('F2', 'PZ');
        $sheet->setCellValue('G2', 50.00);
        $sheet->setCellValue('H2', 22);
        $sheet->setCellValue('I2', '');
        $sheet->setCellValue('J2', 0);
        $sheet->setCellValue('K2', 100.00);

        // Format example row
        $sheet->getStyle('A2:K2')->getFont()->setItalic(true);
        $sheet->getStyle('A2:K2')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFF3CD');
    }

    /**
     * Create the Istruzioni sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet Spreadsheet.
     * @return void
     */
    protected function createIstruzioniSheet(Spreadsheet $spreadsheet): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Istruzioni');

        $sheet->getColumnDimension('A')->setWidth(100);

        $instructions = [
            'ISTRUZIONI PER LA COMPILAZIONE DEL TEMPLATE IMPORT FATTURE',
            '',
            '=== FOGLIO "FATTURE" ===',
            '',
            'Campi obbligatori (contrassegnati con *):',
            '- numero: Numero univoco della fattura (es: FT-2025-001)',
            '- data_emissione: Data in formato YYYY-MM-DD (es: 2025-12-11)',
            '- tipo_documento: Selezionare dal menu a tendina (TD01=Fattura, TD04=Nota credito, etc.)',
            '- cliente_denominazione: Ragione sociale o nome del cliente',
            '- importo_totale: Totale documento IVA inclusa',
            '- imponibile_totale: Totale imponibile (senza IVA)',
            '- imposta_totale: Totale IVA',
            '',
            'Identificazione cliente (almeno uno obbligatorio):',
            '- cliente_piva: Partita IVA (11 cifre per Italia)',
            '- cliente_cf: Codice Fiscale (16 caratteri per persone fisiche)',
            '',
            'Se il cliente non esiste nel sistema, verra creato automaticamente.',
            '',
            '=== FOGLIO "RIGHE" ===',
            '',
            'Ogni riga deve essere collegata a una fattura tramite numero_fattura.',
            '',
            'Campi obbligatori:',
            '- numero_fattura: Deve corrispondere al campo "numero" nel foglio Fatture',
            '- numero_riga: Progressivo (1, 2, 3...)',
            '- descrizione: Descrizione del bene/servizio',
            '- quantita: Quantita (numeri decimali con punto, es: 1.5)',
            '- prezzo_unitario: Prezzo unitario senza IVA',
            '- aliquota_iva: Aliquota IVA (22, 10, 5, 4, 0)',
            '- importo_riga: Importo totale riga (quantita x prezzo - sconto)',
            '',
            'Se aliquota_iva = 0, specificare natura_iva (N1, N2.1, N4, etc.)',
            '',
            'Se prodotto_codice e specificato e non esiste, verra creato automaticamente.',
            '',
            '=== TIPI DOCUMENTO ===',
            '',
        ];

        foreach (self::TIPO_DOCUMENTO as $code => $desc) {
            $instructions[] = "{$code}: {$desc}";
        }

        $instructions[] = '';
        $instructions[] = '=== NATURE IVA ===';
        $instructions[] = '';

        foreach (self::NATURE_IVA as $code => $desc) {
            $instructions[] = "{$code}: {$desc}";
        }

        $instructions[] = '';
        $instructions[] = '=== NOTE ===';
        $instructions[] = '';
        $instructions[] = '- La riga 2 contiene un esempio precompilato. Eliminala o sovrascrivila.';
        $instructions[] = '- I campi numerici usano il punto come separatore decimale (es: 100.50)';
        $instructions[] = '- Le date devono essere in formato YYYY-MM-DD';
        $instructions[] = '- Non modificare i nomi delle colonne nella riga 1';

        foreach ($instructions as $row => $text) {
            $sheet->setCellValue('A' . ($row + 1), $text);
        }

        // Style title
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A19')->getFont()->setBold(true);
        $sheet->getStyle('A33')->getFont()->setBold(true);
        $sheet->getStyle('A44')->getFont()->setBold(true);
    }

    /**
     * Set headers on a sheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet.
     * @param array $headers Headers configuration.
     * @return void
     */
    protected function setHeaders($sheet, array $headers): void
    {
        foreach ($headers as $col => $config) {
            $sheet->setCellValue($col . '1', $config['title']);
            $sheet->getColumnDimension($col)->setWidth($config['width']);
        }

        // Style headers
        $lastCol = array_key_last($headers);
        $headerRange = "A1:{$lastCol}1";

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');
    }

    /**
     * Add dropdown validation to a range.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet Worksheet.
     * @param string $range Cell range (e.g., "C2:C1000").
     * @param string $values Comma-separated values.
     * @return void
     */
    protected function addDropdownValidation($sheet, string $range, string $values): void
    {
        $validation = $sheet->getCell(explode(':', $range)[0])->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"' . $values . '"');

        // Apply to range
        $sheet->setDataValidation($range, $validation);
    }
}
