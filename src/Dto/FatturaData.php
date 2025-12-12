<?php
declare(strict_types=1);

namespace App\Dto;

use DateTimeInterface;

/**
 * DTO principale per i dati della fattura elettronica
 *
 * Nota: Accetta sia DateTimeInterface standard che Cake\I18n\Date/DateTime
 */
class FatturaData
{
    // Tipi documento
    public const TD01 = 'TD01'; // Fattura
    public const TD02 = 'TD02'; // Acconto/Anticipo su fattura
    public const TD03 = 'TD03'; // Acconto/Anticipo su parcella
    public const TD04 = 'TD04'; // Nota di credito
    public const TD05 = 'TD05'; // Nota di debito
    public const TD06 = 'TD06'; // Parcella
    public const TD24 = 'TD24'; // Fattura differita
    public const TD25 = 'TD25'; // Fattura differita (triangolazione)

    // Formati trasmissione
    public const FPA12 = 'FPA12'; // Pubblica Amministrazione
    public const FPR12 = 'FPR12'; // Privati (B2B/B2C)

    // Condizioni pagamento
    public const TP01 = 'TP01'; // Pagamento a rate
    public const TP02 = 'TP02'; // Pagamento completo
    public const TP03 = 'TP03'; // Anticipo

    // Modalita pagamento
    public const MP01 = 'MP01'; // Contanti
    public const MP02 = 'MP02'; // Assegno
    public const MP05 = 'MP05'; // Bonifico
    public const MP08 = 'MP08'; // Carta di pagamento
    public const MP12 = 'MP12'; // RIBA
    public const MP19 = 'MP19'; // SEPA Direct Debit
    public const MP21 = 'MP21'; // PagoPA

    // Esigibilita IVA
    public const ESIGIBILITA_IMMEDIATA = 'I';
    public const ESIGIBILITA_DIFFERITA = 'D';
    public const ESIGIBILITA_SPLIT = 'S';

    private string $formatoTrasmissione = self::FPR12;
    private string $progressivoInvio = '00001';
    private string $codiceDestinatario = '0000000'; // 7 zeri per PEC
    private ?string $pecDestinatario = null;

    private ?AnagraficaFattura $cedente = null;
    private ?AnagraficaFattura $cessionario = null;

    private string $tipoDocumento = self::TD01;
    private string $divisa = 'EUR';
    /** @var DateTimeInterface|object|null */
    private mixed $data = null;
    private string $numero = '';
    private array $causali = [];

    /** @var LineaFattura[] */
    private array $linee = [];

    private string $condizioniPagamento = self::TP02;
    private string $modalitaPagamento = self::MP05;
    /** @var DateTimeInterface|object|null */
    private mixed $dataScadenzaPagamento = null;
    private ?string $ibanPagamento = null;
    private string $esigibilitaIva = self::ESIGIBILITA_IMMEDIATA;

    // Dati opzionali
    private ?string $idDocumentoOrdine = null;
    private ?string $codiceCommessaConvenzione = null;
    private ?string $codiceCup = null;
    private ?string $codiceCig = null;

    public function getFormatoTrasmissione(): string
    {
        return $this->formatoTrasmissione;
    }

    public function setFormatoTrasmissione(string $formatoTrasmissione): self
    {
        $this->formatoTrasmissione = $formatoTrasmissione;
        return $this;
    }

    public function isPubblicaAmministrazione(): bool
    {
        return $this->formatoTrasmissione === self::FPA12;
    }

    public function getProgressivoInvio(): string
    {
        return $this->progressivoInvio;
    }

    public function setProgressivoInvio(string $progressivoInvio): self
    {
        $this->progressivoInvio = $progressivoInvio;
        return $this;
    }

    public function getCodiceDestinatario(): string
    {
        return $this->codiceDestinatario;
    }

    public function setCodiceDestinatario(string $codiceDestinatario): self
    {
        $this->codiceDestinatario = $codiceDestinatario;
        return $this;
    }

    public function getPecDestinatario(): ?string
    {
        return $this->pecDestinatario;
    }

    public function setPecDestinatario(?string $pecDestinatario): self
    {
        $this->pecDestinatario = $pecDestinatario;
        return $this;
    }

    public function getCedente(): ?AnagraficaFattura
    {
        return $this->cedente;
    }

    public function setCedente(AnagraficaFattura $cedente): self
    {
        $this->cedente = $cedente;
        return $this;
    }

    public function getCessionario(): ?AnagraficaFattura
    {
        return $this->cessionario;
    }

    public function setCessionario(AnagraficaFattura $cessionario): self
    {
        $this->cessionario = $cessionario;
        return $this;
    }

    public function getTipoDocumento(): string
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(string $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;
        return $this;
    }

    public function getDivisa(): string
    {
        return $this->divisa;
    }

    public function setDivisa(string $divisa): self
    {
        $this->divisa = $divisa;
        return $this;
    }

    /**
     * @return DateTimeInterface|object|null
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param DateTimeInterface|object $data Oggetto data con metodo format()
     */
    public function setData(object $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getNumero(): string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;
        return $this;
    }

    public function getCausali(): array
    {
        return $this->causali;
    }

    public function addCausale(string $causale): self
    {
        // Dividi in blocchi da 200 caratteri se necessario
        while (strlen($causale) > 0) {
            $this->causali[] = substr($causale, 0, 200);
            $causale = substr($causale, 200);
        }
        return $this;
    }

    /**
     * @return LineaFattura[]
     */
    public function getLinee(): array
    {
        return $this->linee;
    }

    public function addLinea(LineaFattura $linea): self
    {
        $linea->setNumeroLinea(count($this->linee) + 1);
        $this->linee[] = $linea;
        return $this;
    }

    public function getCondizioniPagamento(): string
    {
        return $this->condizioniPagamento;
    }

    public function setCondizioniPagamento(string $condizioniPagamento): self
    {
        $this->condizioniPagamento = $condizioniPagamento;
        return $this;
    }

    public function getModalitaPagamento(): string
    {
        return $this->modalitaPagamento;
    }

    public function setModalitaPagamento(string $modalitaPagamento): self
    {
        $this->modalitaPagamento = $modalitaPagamento;
        return $this;
    }

    /**
     * @return DateTimeInterface|object|null
     */
    public function getDataScadenzaPagamento(): mixed
    {
        return $this->dataScadenzaPagamento;
    }

    /**
     * @param DateTimeInterface|object|null $dataScadenzaPagamento
     */
    public function setDataScadenzaPagamento(?object $dataScadenzaPagamento): self
    {
        $this->dataScadenzaPagamento = $dataScadenzaPagamento;
        return $this;
    }

    public function getIbanPagamento(): ?string
    {
        return $this->ibanPagamento;
    }

    public function setIbanPagamento(?string $ibanPagamento): self
    {
        $this->ibanPagamento = $ibanPagamento;
        return $this;
    }

    public function getEsigibilitaIva(): string
    {
        return $this->esigibilitaIva;
    }

    public function setEsigibilitaIva(string $esigibilitaIva): self
    {
        $this->esigibilitaIva = $esigibilitaIva;
        return $this;
    }

    public function getIdDocumentoOrdine(): ?string
    {
        return $this->idDocumentoOrdine;
    }

    public function setIdDocumentoOrdine(?string $idDocumentoOrdine): self
    {
        $this->idDocumentoOrdine = $idDocumentoOrdine;
        return $this;
    }

    public function getCodiceCommessaConvenzione(): ?string
    {
        return $this->codiceCommessaConvenzione;
    }

    public function setCodiceCommessaConvenzione(?string $codiceCommessaConvenzione): self
    {
        $this->codiceCommessaConvenzione = $codiceCommessaConvenzione;
        return $this;
    }

    public function getCodiceCup(): ?string
    {
        return $this->codiceCup;
    }

    public function setCodiceCup(?string $codiceCup): self
    {
        $this->codiceCup = $codiceCup;
        return $this;
    }

    public function getCodiceCig(): ?string
    {
        return $this->codiceCig;
    }

    public function setCodiceCig(?string $codiceCig): self
    {
        $this->codiceCig = $codiceCig;
        return $this;
    }

    /**
     * Calcola il totale imponibile
     */
    public function getTotaleImponibile(): float
    {
        $totale = 0.0;
        foreach ($this->linee as $linea) {
            $totale += $linea->getPrezzoTotale();
        }
        return round($totale, 2);
    }

    /**
     * Calcola il totale IVA
     */
    public function getTotaleIva(): float
    {
        $totale = 0.0;
        foreach ($this->getRiepilogoIva() as $riepilogo) {
            $totale += $riepilogo['imposta'];
        }
        return round($totale, 2);
    }

    /**
     * Calcola il totale documento (imponibile + IVA)
     */
    public function getTotaleDocumento(): float
    {
        return round($this->getTotaleImponibile() + $this->getTotaleIva(), 2);
    }

    /**
     * Genera il riepilogo IVA raggruppato per aliquota
     * @return array<int, array{aliquota: float, imponibile: float, imposta: float, natura: ?string}>
     */
    public function getRiepilogoIva(): array
    {
        $riepilogo = [];

        foreach ($this->linee as $linea) {
            $aliquota = $linea->getAliquotaIva();
            $natura = $linea->getNatura();
            $key = $aliquota . '_' . ($natura ?? '');

            if (!isset($riepilogo[$key])) {
                $riepilogo[$key] = [
                    'aliquota' => $aliquota,
                    'imponibile' => 0.0,
                    'imposta' => 0.0,
                    'natura' => $natura,
                ];
            }

            $riepilogo[$key]['imponibile'] += $linea->getPrezzoTotale();
        }

        // Calcola l'imposta per ogni aliquota
        foreach ($riepilogo as &$r) {
            $r['imponibile'] = round($r['imponibile'], 2);
            $r['imposta'] = round($r['imponibile'] * $r['aliquota'] / 100, 2);
        }

        return array_values($riepilogo);
    }

    /**
     * Genera il nome file secondo la convenzione FatturaPA
     */
    public function getNomeFile(): string
    {
        $idPaese = $this->cedente?->getIdPaese() ?? 'IT';
        $idCodice = $this->cedente?->getIdCodice() ?? $this->cedente?->getCodiceFiscale() ?? '00000000000';

        return sprintf('%s%s_%s.xml', $idPaese, $idCodice, $this->progressivoInvio);
    }
}
