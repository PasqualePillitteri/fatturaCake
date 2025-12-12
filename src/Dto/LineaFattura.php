<?php
declare(strict_types=1);

namespace App\Dto;

/**
 * DTO per una riga di dettaglio della fattura
 */
class LineaFattura
{
    private int $numeroLinea = 0;
    private ?string $codiceArticolo = null;
    private ?string $tipoCodicArticolo = null; // es: TARIC, CPV, EAN, SSC
    private string $descrizione = '';
    private ?float $quantita = null;
    private ?string $unitaMisura = null;
    private float $prezzoUnitario = 0.0;
    private ?float $prezzoTotale = null;
    private float $aliquotaIva = 22.0;
    private ?string $natura = null; // N1-N7 per operazioni esenti/non imponibili
    private ?string $riferimentoAmministrazione = null;

    /** @var array<int, array{tipo: string, percentuale: float, importo: ?float}> */
    private array $scontiMaggiorazioni = [];

    public function getNumeroLinea(): int
    {
        return $this->numeroLinea;
    }

    public function setNumeroLinea(int $numeroLinea): self
    {
        $this->numeroLinea = $numeroLinea;
        return $this;
    }

    public function getCodiceArticolo(): ?string
    {
        return $this->codiceArticolo;
    }

    public function setCodiceArticolo(?string $codiceArticolo, ?string $tipo = 'PROPRIO'): self
    {
        $this->codiceArticolo = $codiceArticolo;
        $this->tipoCodicArticolo = $tipo;
        return $this;
    }

    public function getTipoCodiceArticolo(): ?string
    {
        return $this->tipoCodicArticolo;
    }

    public function getDescrizione(): string
    {
        return $this->descrizione;
    }

    public function setDescrizione(string $descrizione): self
    {
        $this->descrizione = $descrizione;
        return $this;
    }

    public function getQuantita(): ?float
    {
        return $this->quantita;
    }

    public function setQuantita(?float $quantita): self
    {
        $this->quantita = $quantita;
        return $this;
    }

    public function getUnitaMisura(): ?string
    {
        return $this->unitaMisura;
    }

    public function setUnitaMisura(?string $unitaMisura): self
    {
        $this->unitaMisura = $unitaMisura;
        return $this;
    }

    public function getPrezzoUnitario(): float
    {
        return $this->prezzoUnitario;
    }

    public function setPrezzoUnitario(float $prezzoUnitario): self
    {
        $this->prezzoUnitario = $prezzoUnitario;
        return $this;
    }

    public function getPrezzoTotale(): float
    {
        if ($this->prezzoTotale !== null) {
            return $this->prezzoTotale;
        }

        $totale = $this->prezzoUnitario * ($this->quantita ?? 1);

        // Applica sconti/maggiorazioni
        foreach ($this->scontiMaggiorazioni as $sm) {
            if ($sm['importo'] !== null) {
                $importo = $sm['importo'];
            } else {
                $importo = $totale * ($sm['percentuale'] / 100);
            }

            if ($sm['tipo'] === 'SC') {
                $totale -= $importo;
            } else {
                $totale += $importo;
            }
        }

        return round($totale, 2);
    }

    public function setPrezzoTotale(?float $prezzoTotale): self
    {
        $this->prezzoTotale = $prezzoTotale;
        return $this;
    }

    public function getAliquotaIva(): float
    {
        return $this->aliquotaIva;
    }

    public function setAliquotaIva(float $aliquotaIva): self
    {
        $this->aliquotaIva = $aliquotaIva;
        return $this;
    }

    public function getNatura(): ?string
    {
        return $this->natura;
    }

    public function setNatura(?string $natura): self
    {
        $this->natura = $natura;
        return $this;
    }

    public function getRiferimentoAmministrazione(): ?string
    {
        return $this->riferimentoAmministrazione;
    }

    public function setRiferimentoAmministrazione(?string $riferimentoAmministrazione): self
    {
        $this->riferimentoAmministrazione = $riferimentoAmministrazione;
        return $this;
    }

    public function addSconto(float $percentuale, ?float $importo = null): self
    {
        $this->scontiMaggiorazioni[] = [
            'tipo' => 'SC',
            'percentuale' => $percentuale,
            'importo' => $importo,
        ];
        return $this;
    }

    public function addMaggiorazione(float $percentuale, ?float $importo = null): self
    {
        $this->scontiMaggiorazioni[] = [
            'tipo' => 'MG',
            'percentuale' => $percentuale,
            'importo' => $importo,
        ];
        return $this;
    }

    /**
     * @return array<int, array{tipo: string, percentuale: float, importo: ?float}>
     */
    public function getScontiMaggiorazioni(): array
    {
        return $this->scontiMaggiorazioni;
    }
}
