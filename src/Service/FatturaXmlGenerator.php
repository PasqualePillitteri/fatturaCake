<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\AnagraficaFattura;
use App\Dto\FatturaData;
use App\Dto\LineaFattura;
use DOMDocument;
use DOMElement;
use InvalidArgumentException;

/**
 * Generatore XML per Fattura Elettronica (FatturaPA v1.2)
 */
class FatturaXmlGenerator
{
    private const NAMESPACE_P = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2';
    private const NAMESPACE_DS = 'http://www.w3.org/2000/09/xmldsig#';
    private const NAMESPACE_XSI = 'http://www.w3.org/2001/XMLSchema-instance';
    private const SCHEMA_LOCATION = 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd';

    private DOMDocument $dom;
    private DOMElement $root;

    /**
     * Genera l'XML della fattura elettronica
     */
    public function generate(FatturaData $fattura): string
    {
        $this->validate($fattura);

        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;

        $this->createRoot($fattura);
        $this->addHeader($fattura);
        $this->addBody($fattura);

        return $this->dom->saveXML();
    }

    /**
     * Salva l'XML su file
     */
    public function saveToFile(FatturaData $fattura, string $directory): string
    {
        $xml = $this->generate($fattura);
        $filename = $fattura->getNomeFile();
        $filepath = rtrim($directory, '/') . '/' . $filename;

        if (file_put_contents($filepath, $xml) === false) {
            throw new InvalidArgumentException("Impossibile salvare il file: {$filepath}");
        }

        return $filepath;
    }

    /**
     * Valida i dati obbligatori
     */
    private function validate(FatturaData $fattura): void
    {
        if ($fattura->getCedente() === null) {
            throw new InvalidArgumentException('Cedente/Prestatore obbligatorio');
        }

        if ($fattura->getCessionario() === null) {
            throw new InvalidArgumentException('Cessionario/Committente obbligatorio');
        }

        if (empty($fattura->getNumero())) {
            throw new InvalidArgumentException('Numero fattura obbligatorio');
        }

        if ($fattura->getData() === null) {
            throw new InvalidArgumentException('Data fattura obbligatoria');
        }

        if (empty($fattura->getLinee())) {
            throw new InvalidArgumentException('Almeno una linea di dettaglio obbligatoria');
        }

        $cedente = $fattura->getCedente();
        if (!$cedente->hasPartitaIva()) {
            throw new InvalidArgumentException('Partita IVA cedente obbligatoria');
        }
    }

    /**
     * Crea l'elemento root
     */
    private function createRoot(FatturaData $fattura): void
    {
        $this->root = $this->dom->createElementNS(self::NAMESPACE_P, 'p:FatturaElettronica');
        $this->root->setAttribute('versione', $fattura->getFormatoTrasmissione());
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ds', self::NAMESPACE_DS);
        $this->root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', self::NAMESPACE_XSI);
        $this->root->setAttributeNS(self::NAMESPACE_XSI, 'xsi:schemaLocation', self::SCHEMA_LOCATION);

        $this->dom->appendChild($this->root);
    }

    /**
     * Aggiunge FatturaElettronicaHeader
     */
    private function addHeader(FatturaData $fattura): void
    {
        $header = $this->addElement($this->root, 'FatturaElettronicaHeader');

        $this->addDatiTrasmissione($header, $fattura);
        $this->addCedentePrestatore($header, $fattura->getCedente());
        $this->addCessionarioCommittente($header, $fattura->getCessionario());
    }

    /**
     * Aggiunge DatiTrasmissione
     */
    private function addDatiTrasmissione(DOMElement $parent, FatturaData $fattura): void
    {
        $datiTrasmissione = $this->addElement($parent, 'DatiTrasmissione');

        $idTrasmittente = $this->addElement($datiTrasmissione, 'IdTrasmittente');
        $this->addElement($idTrasmittente, 'IdPaese', $fattura->getCedente()->getIdPaese());
        $this->addElement($idTrasmittente, 'IdCodice', $fattura->getCedente()->getIdCodice());

        $this->addElement($datiTrasmissione, 'ProgressivoInvio', $fattura->getProgressivoInvio());
        $this->addElement($datiTrasmissione, 'FormatoTrasmissione', $fattura->getFormatoTrasmissione());
        $this->addElement($datiTrasmissione, 'CodiceDestinatario', $fattura->getCodiceDestinatario());

        if ($fattura->getPecDestinatario() !== null) {
            $this->addElement($datiTrasmissione, 'PECDestinatario', $fattura->getPecDestinatario());
        }
    }

    /**
     * Aggiunge CedentePrestatore
     */
    private function addCedentePrestatore(DOMElement $parent, AnagraficaFattura $cedente): void
    {
        $cedentePrestatore = $this->addElement($parent, 'CedentePrestatore');

        $datiAnagrafici = $this->addElement($cedentePrestatore, 'DatiAnagrafici');

        if ($cedente->hasPartitaIva()) {
            $idFiscaleIva = $this->addElement($datiAnagrafici, 'IdFiscaleIVA');
            $this->addElement($idFiscaleIva, 'IdPaese', $cedente->getIdPaese());
            $this->addElement($idFiscaleIva, 'IdCodice', $cedente->getIdCodice());
        }

        if ($cedente->getCodiceFiscale() !== null) {
            $this->addElement($datiAnagrafici, 'CodiceFiscale', $cedente->getCodiceFiscale());
        }

        $anagrafica = $this->addElement($datiAnagrafici, 'Anagrafica');
        if ($cedente->isPersonaFisica()) {
            $this->addElement($anagrafica, 'Nome', $cedente->getNome());
            $this->addElement($anagrafica, 'Cognome', $cedente->getCognome());
        } else {
            $this->addElement($anagrafica, 'Denominazione', $cedente->getDenominazione());
        }

        $this->addElement($datiAnagrafici, 'RegimeFiscale', $cedente->getRegimeFiscale());

        $this->addSede($cedentePrestatore, $cedente);
    }

    /**
     * Aggiunge CessionarioCommittente
     */
    private function addCessionarioCommittente(DOMElement $parent, AnagraficaFattura $cessionario): void
    {
        $cessionarioCommittente = $this->addElement($parent, 'CessionarioCommittente');

        $datiAnagrafici = $this->addElement($cessionarioCommittente, 'DatiAnagrafici');

        if ($cessionario->hasPartitaIva()) {
            $idFiscaleIva = $this->addElement($datiAnagrafici, 'IdFiscaleIVA');
            $this->addElement($idFiscaleIva, 'IdPaese', $cessionario->getIdPaese());
            $this->addElement($idFiscaleIva, 'IdCodice', $cessionario->getIdCodice());
        }

        if ($cessionario->getCodiceFiscale() !== null) {
            $this->addElement($datiAnagrafici, 'CodiceFiscale', $cessionario->getCodiceFiscale());
        }

        $anagrafica = $this->addElement($datiAnagrafici, 'Anagrafica');
        if ($cessionario->isPersonaFisica()) {
            $this->addElement($anagrafica, 'Nome', $cessionario->getNome());
            $this->addElement($anagrafica, 'Cognome', $cessionario->getCognome());
        } else {
            $this->addElement($anagrafica, 'Denominazione', $cessionario->getDenominazione());
        }

        $this->addSede($cessionarioCommittente, $cessionario);
    }

    /**
     * Aggiunge Sede
     */
    private function addSede(DOMElement $parent, AnagraficaFattura $anagrafica): void
    {
        $sede = $this->addElement($parent, 'Sede');
        $this->addElement($sede, 'Indirizzo', $anagrafica->getIndirizzo());
        $this->addElement($sede, 'CAP', $anagrafica->getCap());
        $this->addElement($sede, 'Comune', $anagrafica->getComune());

        if ($anagrafica->getProvincia() !== null) {
            $this->addElement($sede, 'Provincia', $anagrafica->getProvincia());
        }

        $this->addElement($sede, 'Nazione', $anagrafica->getNazione());
    }

    /**
     * Aggiunge FatturaElettronicaBody
     */
    private function addBody(FatturaData $fattura): void
    {
        $body = $this->addElement($this->root, 'FatturaElettronicaBody');

        $this->addDatiGenerali($body, $fattura);
        $this->addDatiBeniServizi($body, $fattura);
        $this->addDatiPagamento($body, $fattura);
    }

    /**
     * Aggiunge DatiGenerali
     */
    private function addDatiGenerali(DOMElement $parent, FatturaData $fattura): void
    {
        $datiGenerali = $this->addElement($parent, 'DatiGenerali');

        $datiGeneraliDocumento = $this->addElement($datiGenerali, 'DatiGeneraliDocumento');
        $this->addElement($datiGeneraliDocumento, 'TipoDocumento', $fattura->getTipoDocumento());
        $this->addElement($datiGeneraliDocumento, 'Divisa', $fattura->getDivisa());
        $this->addElement($datiGeneraliDocumento, 'Data', $fattura->getData()->format('Y-m-d'));
        $this->addElement($datiGeneraliDocumento, 'Numero', $fattura->getNumero());

        foreach ($fattura->getCausali() as $causale) {
            $this->addElement($datiGeneraliDocumento, 'Causale', $causale);
        }

        // Dati ordine acquisto se presenti
        if ($fattura->getIdDocumentoOrdine() !== null) {
            $datiOrdine = $this->addElement($datiGenerali, 'DatiOrdineAcquisto');
            $this->addElement($datiOrdine, 'IdDocumento', $fattura->getIdDocumentoOrdine());

            if ($fattura->getCodiceCup() !== null) {
                $this->addElement($datiOrdine, 'CodiceCUP', $fattura->getCodiceCup());
            }
            if ($fattura->getCodiceCig() !== null) {
                $this->addElement($datiOrdine, 'CodiceCIG', $fattura->getCodiceCig());
            }
        }
    }

    /**
     * Aggiunge DatiBeniServizi
     */
    private function addDatiBeniServizi(DOMElement $parent, FatturaData $fattura): void
    {
        $datiBeniServizi = $this->addElement($parent, 'DatiBeniServizi');

        // Dettaglio linee
        foreach ($fattura->getLinee() as $linea) {
            $this->addDettaglioLinea($datiBeniServizi, $linea);
        }

        // Riepilogo IVA
        foreach ($fattura->getRiepilogoIva() as $riepilogo) {
            $this->addDatiRiepilogo($datiBeniServizi, $riepilogo, $fattura->getEsigibilitaIva());
        }
    }

    /**
     * Aggiunge DettaglioLinee
     */
    private function addDettaglioLinea(DOMElement $parent, LineaFattura $linea): void
    {
        $dettaglio = $this->addElement($parent, 'DettaglioLinee');

        $this->addElement($dettaglio, 'NumeroLinea', (string)$linea->getNumeroLinea());

        if ($linea->getCodiceArticolo() !== null) {
            $codiceArticolo = $this->addElement($dettaglio, 'CodiceArticolo');
            $this->addElement($codiceArticolo, 'CodiceTipo', $linea->getTipoCodiceArticolo());
            $this->addElement($codiceArticolo, 'CodiceValore', $linea->getCodiceArticolo());
        }

        $this->addElement($dettaglio, 'Descrizione', $linea->getDescrizione());

        if ($linea->getQuantita() !== null) {
            $this->addElement($dettaglio, 'Quantita', $this->formatNumber($linea->getQuantita()));
        }

        if ($linea->getUnitaMisura() !== null) {
            $this->addElement($dettaglio, 'UnitaMisura', $linea->getUnitaMisura());
        }

        $this->addElement($dettaglio, 'PrezzoUnitario', $this->formatNumber($linea->getPrezzoUnitario()));

        // Sconti/Maggiorazioni
        foreach ($linea->getScontiMaggiorazioni() as $sm) {
            $scontoMagg = $this->addElement($dettaglio, 'ScontoMaggiorazione');
            $this->addElement($scontoMagg, 'Tipo', $sm['tipo']);
            $this->addElement($scontoMagg, 'Percentuale', $this->formatNumber($sm['percentuale']));
            if ($sm['importo'] !== null) {
                $this->addElement($scontoMagg, 'Importo', $this->formatNumber($sm['importo']));
            }
        }

        $this->addElement($dettaglio, 'PrezzoTotale', $this->formatNumber($linea->getPrezzoTotale()));
        $this->addElement($dettaglio, 'AliquotaIVA', $this->formatNumber($linea->getAliquotaIva()));

        if ($linea->getNatura() !== null) {
            $this->addElement($dettaglio, 'Natura', $linea->getNatura());
        }

        if ($linea->getRiferimentoAmministrazione() !== null) {
            $this->addElement($dettaglio, 'RiferimentoAmministrazione', $linea->getRiferimentoAmministrazione());
        }
    }

    /**
     * Aggiunge DatiRiepilogo
     * @param array{aliquota: float, imponibile: float, imposta: float, natura: ?string} $riepilogo
     */
    private function addDatiRiepilogo(DOMElement $parent, array $riepilogo, string $esigibilita): void
    {
        $datiRiepilogo = $this->addElement($parent, 'DatiRiepilogo');

        $this->addElement($datiRiepilogo, 'AliquotaIVA', $this->formatNumber($riepilogo['aliquota']));

        if ($riepilogo['natura'] !== null) {
            $this->addElement($datiRiepilogo, 'Natura', $riepilogo['natura']);
        }

        $this->addElement($datiRiepilogo, 'ImponibileImporto', $this->formatNumber($riepilogo['imponibile']));
        $this->addElement($datiRiepilogo, 'Imposta', $this->formatNumber($riepilogo['imposta']));
        $this->addElement($datiRiepilogo, 'EsigibilitaIVA', $esigibilita);
    }

    /**
     * Aggiunge DatiPagamento
     */
    private function addDatiPagamento(DOMElement $parent, FatturaData $fattura): void
    {
        $datiPagamento = $this->addElement($parent, 'DatiPagamento');

        $this->addElement($datiPagamento, 'CondizioniPagamento', $fattura->getCondizioniPagamento());

        $dettaglio = $this->addElement($datiPagamento, 'DettaglioPagamento');
        $this->addElement($dettaglio, 'ModalitaPagamento', $fattura->getModalitaPagamento());

        if ($fattura->getDataScadenzaPagamento() !== null) {
            $this->addElement($dettaglio, 'DataScadenzaPagamento', $fattura->getDataScadenzaPagamento()->format('Y-m-d'));
        }

        $this->addElement($dettaglio, 'ImportoPagamento', $this->formatNumber($fattura->getTotaleDocumento()));

        if ($fattura->getIbanPagamento() !== null) {
            $this->addElement($dettaglio, 'IBAN', $fattura->getIbanPagamento());
        }
    }

    /**
     * Helper per aggiungere elementi
     */
    private function addElement(DOMElement $parent, string $name, ?string $value = null): DOMElement
    {
        $element = $this->dom->createElement($name, $value !== null ? htmlspecialchars($value, ENT_XML1) : '');
        $parent->appendChild($element);
        return $element;
    }

    /**
     * Formatta un numero per l'XML (2 decimali, punto come separatore)
     */
    private function formatNumber(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
