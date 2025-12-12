<?php
declare(strict_types=1);

namespace App\Dto;

/**
 * DTO per i dati anagrafici di cedente/cessionario
 */
class AnagraficaFattura
{
    private ?string $idPaese = 'IT';
    private ?string $idCodice = null; // Partita IVA
    private ?string $codiceFiscale = null;
    private ?string $denominazione = null;
    private ?string $nome = null;
    private ?string $cognome = null;
    private ?string $regimeFiscale = 'RF01'; // Ordinario
    private string $indirizzo = '';
    private string $cap = '';
    private string $comune = '';
    private ?string $provincia = null;
    private string $nazione = 'IT';
    private ?string $pec = null;

    public function getIdPaese(): ?string
    {
        return $this->idPaese;
    }

    public function setIdPaese(?string $idPaese): self
    {
        $this->idPaese = $idPaese;
        return $this;
    }

    public function getIdCodice(): ?string
    {
        return $this->idCodice;
    }

    public function setIdCodice(?string $idCodice): self
    {
        $this->idCodice = $idCodice;
        return $this;
    }

    public function getCodiceFiscale(): ?string
    {
        return $this->codiceFiscale;
    }

    public function setCodiceFiscale(?string $codiceFiscale): self
    {
        $this->codiceFiscale = $codiceFiscale;
        return $this;
    }

    public function getDenominazione(): ?string
    {
        return $this->denominazione;
    }

    public function setDenominazione(?string $denominazione): self
    {
        $this->denominazione = $denominazione;
        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getCognome(): ?string
    {
        return $this->cognome;
    }

    public function setCognome(?string $cognome): self
    {
        $this->cognome = $cognome;
        return $this;
    }

    public function getRegimeFiscale(): ?string
    {
        return $this->regimeFiscale;
    }

    public function setRegimeFiscale(?string $regimeFiscale): self
    {
        $this->regimeFiscale = $regimeFiscale;
        return $this;
    }

    public function getIndirizzo(): string
    {
        return $this->indirizzo;
    }

    public function setIndirizzo(string $indirizzo): self
    {
        $this->indirizzo = $indirizzo;
        return $this;
    }

    public function getCap(): string
    {
        return $this->cap;
    }

    public function setCap(string $cap): self
    {
        $this->cap = $cap;
        return $this;
    }

    public function getComune(): string
    {
        return $this->comune;
    }

    public function setComune(string $comune): self
    {
        $this->comune = $comune;
        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(?string $provincia): self
    {
        $this->provincia = $provincia;
        return $this;
    }

    public function getNazione(): string
    {
        return $this->nazione;
    }

    public function setNazione(string $nazione): self
    {
        $this->nazione = $nazione;
        return $this;
    }

    public function getPec(): ?string
    {
        return $this->pec;
    }

    public function setPec(?string $pec): self
    {
        $this->pec = $pec;
        return $this;
    }

    public function hasPartitaIva(): bool
    {
        return $this->idCodice !== null && $this->idCodice !== '';
    }

    public function isPersonaFisica(): bool
    {
        return $this->nome !== null && $this->cognome !== null;
    }
}
