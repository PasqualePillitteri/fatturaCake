/**
 * Gestione dinamica righe fattura - Layout Tabellare
 * Con calcolo ritenuta d'acconto e bollo
 */
(function() {
    'use strict';

    // Stato dell'applicazione
    let righeIndex = 0;

    /**
     * Inizializza il modulo quando il DOM e' pronto
     */
    document.addEventListener('DOMContentLoaded', function() {
        initRighe();
        initRitenuta();
        initBollo();
    });

    /**
     * Inizializza gli event listener e calcola i totali iniziali
     */
    function initRighe() {
        const tbody = document.getElementById('righe-tbody');
        const container = document.getElementById('righe-fattura-container');
        if (!container) return;

        // Calcola l'indice iniziale basato sulle righe esistenti
        const righeEsistenti = tbody ? tbody.querySelectorAll('.riga-row') : [];
        righeIndex = righeEsistenti.length;

        // Aggiorna contatore badge
        aggiornaContatoreBadge();

        // Event listener per aggiungere riga (header)
        const btnAdd = document.getElementById('btn-add-riga');
        if (btnAdd) {
            btnAdd.addEventListener('click', aggiungiRiga);
        }

        // Event listener per aggiungere riga (empty state)
        const btnAddEmpty = document.getElementById('btn-add-riga-empty');
        if (btnAddEmpty) {
            btnAddEmpty.addEventListener('click', aggiungiRiga);
        }

        // Event delegation per rimuovere righe
        container.addEventListener('click', function(e) {
            if (e.target.closest('.btn-remove-riga')) {
                const riga = e.target.closest('.riga-row') || e.target.closest('.riga-mobile-card');
                rimuoviRiga(riga);
            }
        });

        // Event delegation per calcolo totali e caricamento prodotto
        container.addEventListener('change', function(e) {
            const riga = e.target.closest('.riga-row') || e.target.closest('.riga-mobile-card');

            if (e.target.classList.contains('quantita-input') ||
                e.target.classList.contains('prezzo-input') ||
                e.target.classList.contains('iva-input')) {
                calcolaTotaleRiga(riga);
                calcolaTotaliFattura();
            }

            if (e.target.classList.contains('prodotto-select')) {
                caricaDatiProdotto(e.target);
            }
        });

        // Input event per calcolo in tempo reale
        container.addEventListener('input', function(e) {
            const riga = e.target.closest('.riga-row') || e.target.closest('.riga-mobile-card');

            if (e.target.classList.contains('quantita-input') ||
                e.target.classList.contains('prezzo-input')) {
                calcolaTotaleRiga(riga);
                calcolaTotaliFattura();
            }
        });

        // Calcola totali iniziali
        righeEsistenti.forEach(function(riga) {
            calcolaTotaleRiga(riga);
        });
        calcolaTotaliFattura();

        // Gestisci visibility empty state
        toggleEmptyState();

        // Pulsante Ricalcola
        const btnRicalcola = document.getElementById('btn-ricalcola');
        if (btnRicalcola) {
            btnRicalcola.addEventListener('click', function() {
                ricalcolaTutto();
            });
        }
    }

    /**
     * Ricalcola tutti i totali (chiamato dal pulsante Ricalcola)
     */
    function ricalcolaTutto() {
        const tbody = document.getElementById('righe-tbody');
        const btnRicalcola = document.getElementById('btn-ricalcola');

        if (!tbody) return;

        // Feedback visivo
        if (btnRicalcola) {
            btnRicalcola.disabled = true;
            btnRicalcola.innerHTML = '<i data-lucide="loader-2" class="spin" style="width:14px;height:14px;"></i> <span>Calcolo...</span>';
        }

        // Ricalcola ogni riga
        const righe = tbody.querySelectorAll('.riga-row');
        righe.forEach(function(riga) {
            calcolaTotaleRiga(riga);
        });

        // Ricalcola totali fattura
        calcolaTotaliFattura();

        // Ripristina pulsante con feedback di successo
        setTimeout(function() {
            if (btnRicalcola) {
                btnRicalcola.innerHTML = '<i data-lucide="check" style="width:14px;height:14px;"></i> <span>Fatto!</span>';
                btnRicalcola.classList.remove('btn-outline-secondary');
                btnRicalcola.classList.add('btn-success');

                // Renderizza icone Lucide
                if (typeof lucide !== 'undefined' && lucide.createIcons) {
                    lucide.createIcons();
                }

                // Torna allo stato normale dopo 1.5 secondi
                setTimeout(function() {
                    btnRicalcola.disabled = false;
                    btnRicalcola.innerHTML = '<i data-lucide="calculator" style="width:14px;height:14px;"></i> <span>Ricalcola</span>';
                    btnRicalcola.classList.remove('btn-success');
                    btnRicalcola.classList.add('btn-outline-secondary');
                    if (typeof lucide !== 'undefined' && lucide.createIcons) {
                        lucide.createIcons();
                    }
                }, 1500);
            }
        }, 300);
    }

    /**
     * Inizializza gestione ritenuta d'acconto
     */
    function initRitenuta() {
        const toggleRitenuta = document.getElementById('ritenuta-enabled');
        const ritenutaFields = document.getElementById('ritenuta-fields');
        const aliquotaRitenutaInput = document.getElementById('aliquota-ritenuta');
        const tipoRitenutaSelect = document.getElementById('tipo-ritenuta');

        if (!toggleRitenuta) return;

        // Toggle visibility dei campi ritenuta
        toggleRitenuta.addEventListener('change', function() {
            if (ritenutaFields) {
                ritenutaFields.style.display = this.checked ? 'block' : 'none';
            }
            calcolaTotaliFattura();
        });

        // Ricalcola quando cambia l'aliquota ritenuta
        if (aliquotaRitenutaInput) {
            aliquotaRitenutaInput.addEventListener('input', calcolaTotaliFattura);
            aliquotaRitenutaInput.addEventListener('change', calcolaTotaliFattura);
        }

        // Imposta aliquota default in base al tipo ritenuta
        if (tipoRitenutaSelect) {
            tipoRitenutaSelect.addEventListener('change', function() {
                const aliquoteDefault = {
                    'RT01': 20,  // Ritenuta persone fisiche
                    'RT02': 20,  // Ritenuta persone giuridiche
                    'RT03': 23,  // Contributo INPS
                    'RT04': 23,  // Contributo ENASARCO
                    'RT05': 23,  // Contributo ENPAM
                    'RT06': 4    // Altro contributo previdenziale
                };
                if (aliquotaRitenutaInput && aliquoteDefault[this.value]) {
                    aliquotaRitenutaInput.value = aliquoteDefault[this.value];
                    calcolaTotaliFattura();
                }
            });
        }

        // Imposta stato iniziale
        if (ritenutaFields) {
            ritenutaFields.style.display = toggleRitenuta.checked ? 'block' : 'none';
        }
    }

    /**
     * Inizializza gestione bollo virtuale
     */
    function initBollo() {
        const bolloCheckbox = document.getElementById('bollo-virtuale');
        const importoBolloInput = document.getElementById('importo-bollo');

        if (!bolloCheckbox) return;

        bolloCheckbox.addEventListener('change', function() {
            if (importoBolloInput) {
                importoBolloInput.disabled = !this.checked;
                if (!this.checked) {
                    importoBolloInput.value = '2.00';
                }
            }
            calcolaTotaliFattura();
        });

        if (importoBolloInput) {
            importoBolloInput.addEventListener('input', calcolaTotaliFattura);
            importoBolloInput.addEventListener('change', calcolaTotaliFattura);

            // Stato iniziale
            importoBolloInput.disabled = !bolloCheckbox.checked;
        }
    }

    /**
     * Aggiunge una nuova riga fattura
     */
    function aggiungiRiga() {
        const template = document.getElementById('riga-template');
        const tbody = document.getElementById('righe-tbody');

        if (!template || !tbody) return;

        // Clona il template
        const html = template.innerHTML
            .replace(/__INDEX__/g, righeIndex)
            .replace(/__NUM__/g, righeIndex + 1);

        // Inserisci la nuova riga
        tbody.insertAdjacentHTML('beforeend', html);

        // Renderizza le icone Lucide nella nuova riga
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }

        // Rimuovi classe animazione dopo l'animazione
        const newRow = tbody.lastElementChild;
        setTimeout(function() {
            if (newRow) newRow.classList.remove('riga-new');
        }, 300);

        // Focus sul primo campo della nuova riga
        const prodottoSelect = newRow.querySelector('.prodotto-select');
        if (prodottoSelect) {
            prodottoSelect.focus();
        }

        // Aggiorna indice e numeri
        righeIndex++;
        aggiornaNumeriRiga();
        aggiornaContatoreBadge();
        toggleEmptyState();
    }

    /**
     * Rimuove una riga fattura
     * @param {HTMLElement} rigaElement - Elemento riga da rimuovere
     */
    function rimuoviRiga(rigaElement) {
        if (!rigaElement) return;

        const tbody = document.getElementById('righe-tbody');
        const righe = tbody.querySelectorAll('.riga-row');

        // Animazione di rimozione
        rigaElement.classList.add('riga-removing');

        setTimeout(function() {
            rigaElement.remove();
            aggiornaNumeriRiga();
            calcolaTotaliFattura();
            aggiornaContatoreBadge();
            toggleEmptyState();
        }, 250);
    }

    /**
     * Aggiorna i numeri di riga dopo aggiunta/rimozione
     */
    function aggiornaNumeriRiga() {
        const tbody = document.getElementById('righe-tbody');
        if (!tbody) return;

        const righe = tbody.querySelectorAll('.riga-row');

        righe.forEach(function(riga, index) {
            const numeroSpan = riga.querySelector('.riga-numero');
            const numeroInput = riga.querySelector('.numero-linea-input');

            if (numeroSpan) numeroSpan.textContent = index + 1;
            if (numeroInput) numeroInput.value = index + 1;
        });
    }

    /**
     * Aggiorna il badge contatore righe
     */
    function aggiornaContatoreBadge() {
        const badge = document.getElementById('righe-count');
        const tbody = document.getElementById('righe-tbody');

        if (badge && tbody) {
            const count = tbody.querySelectorAll('.riga-row').length;
            badge.textContent = count;
        }
    }

    /**
     * Mostra/nasconde lo stato vuoto
     */
    function toggleEmptyState() {
        const emptyState = document.getElementById('righe-empty-state');
        const tbody = document.getElementById('righe-tbody');
        const table = document.getElementById('righe-table');
        const totali = document.getElementById('righe-totali');

        if (!emptyState || !tbody) return;

        const hasRighe = tbody.querySelectorAll('.riga-row').length > 0;

        emptyState.style.display = hasRighe ? 'none' : 'flex';
        if (table) table.style.display = hasRighe ? 'table' : 'none';
        if (totali) totali.style.display = hasRighe ? 'flex' : 'none';
    }

    /**
     * Calcola il totale di una singola riga
     * @param {HTMLElement} rigaElement - Elemento riga
     */
    function calcolaTotaleRiga(rigaElement) {
        if (!rigaElement) return;

        const quantita = parseFloat(rigaElement.querySelector('.quantita-input')?.value) || 0;
        const prezzo = parseFloat(rigaElement.querySelector('.prezzo-input')?.value) || 0;

        const totale = quantita * prezzo;

        // Aggiorna display e input hidden
        const totaleDisplay = rigaElement.querySelector('.totale-riga-display');
        const totaleInput = rigaElement.querySelector('.prezzo-totale-input');

        if (totaleDisplay) {
            totaleDisplay.textContent = formatNumber(totale);
        }
        if (totaleInput) {
            totaleInput.value = totale.toFixed(2);
        }
    }

    /**
     * Calcola i totali della fattura (imponibile, IVA, ritenuta, bollo, totale)
     */
    function calcolaTotaliFattura() {
        const tbody = document.getElementById('righe-tbody');
        if (!tbody) return;

        const righe = tbody.querySelectorAll('.riga-row');

        let totaleImponibile = 0;
        let totaleIva = 0;

        // Calcolo imponibile e IVA dalle righe
        righe.forEach(function(riga) {
            const quantita = parseFloat(riga.querySelector('.quantita-input')?.value) || 0;
            const prezzo = parseFloat(riga.querySelector('.prezzo-input')?.value) || 0;
            const aliquotaIva = parseFloat(riga.querySelector('.iva-input')?.value) || 0;

            const imponibileRiga = quantita * prezzo;
            const ivaRiga = imponibileRiga * (aliquotaIva / 100);

            totaleImponibile += imponibileRiga;
            totaleIva += ivaRiga;
        });

        // Calcolo ritenuta d'acconto
        let ritenutaAcconto = 0;
        const toggleRitenuta = document.getElementById('ritenuta-enabled');
        const aliquotaRitenutaInput = document.getElementById('aliquota-ritenuta');
        const ritenutaAccontoInput = document.getElementById('ritenuta-acconto');

        if (toggleRitenuta && toggleRitenuta.checked && aliquotaRitenutaInput) {
            const aliquotaRitenuta = parseFloat(aliquotaRitenutaInput.value) || 0;
            // La ritenuta si calcola sull'imponibile
            ritenutaAcconto = totaleImponibile * (aliquotaRitenuta / 100);

            // Aggiorna il campo ritenuta_acconto
            if (ritenutaAccontoInput) {
                ritenutaAccontoInput.value = ritenutaAcconto.toFixed(2);
            }
        } else if (ritenutaAccontoInput) {
            ritenutaAccontoInput.value = '0.00';
        }

        // Calcolo bollo
        let importoBollo = 0;
        const bolloCheckbox = document.getElementById('bollo-virtuale');
        const importoBolloInput = document.getElementById('importo-bollo');

        if (bolloCheckbox && bolloCheckbox.checked && importoBolloInput) {
            importoBollo = parseFloat(importoBolloInput.value) || 2.00;
        }

        // Totale documento = Imponibile + IVA + Bollo - Ritenuta
        // Nota: la ritenuta viene sottratta perche' e' trattenuta dal cliente
        const totaleDocumento = totaleImponibile + totaleIva + importoBollo - ritenutaAcconto;

        // Netto a pagare (quello che il cliente deve effettivamente pagare)
        // Se c'e' ritenuta, il cliente paga il totale meno la ritenuta che versa lui
        const nettoAPagare = totaleImponibile + totaleIva + importoBollo - ritenutaAcconto;

        // Aggiorna display riepilogo (span nel box totali)
        const displayImponibile = document.getElementById('display-imponibile');
        const displayIva = document.getElementById('display-iva');
        const displayRitenuta = document.getElementById('display-ritenuta');
        const displayBollo = document.getElementById('display-bollo');
        const displayTotale = document.getElementById('display-totale');
        const displayNetto = document.getElementById('display-netto');

        if (displayImponibile) displayImponibile.textContent = formatNumber(totaleImponibile);
        if (displayIva) displayIva.textContent = formatNumber(totaleIva);
        if (displayRitenuta) displayRitenuta.textContent = formatNumber(ritenutaAcconto);
        if (displayBollo) displayBollo.textContent = formatNumber(importoBollo);
        if (displayTotale) displayTotale.textContent = formatNumber(totaleImponibile + totaleIva + importoBollo);
        if (displayNetto) displayNetto.textContent = formatNumber(nettoAPagare);

        // Aggiorna i campi input del form principale
        const inputImponibile = document.getElementById('imponibile-totale');
        const inputIva = document.getElementById('iva-totale');
        const inputTotale = document.getElementById('totale-documento');

        if (inputImponibile) inputImponibile.value = totaleImponibile.toFixed(2);
        if (inputIva) inputIva.value = totaleIva.toFixed(2);
        // Il totale documento include il bollo ma NON la ritenuta (che viene solo visualizzata)
        if (inputTotale) inputTotale.value = (totaleImponibile + totaleIva + importoBollo).toFixed(2);

        // Mostra/nascondi righe riepilogo condizionali
        const rigaRitenuta = document.getElementById('riga-totale-ritenuta');
        const rigaBollo = document.getElementById('riga-totale-bollo');
        const rigaNetto = document.getElementById('riga-netto-pagare');

        if (rigaRitenuta) {
            rigaRitenuta.style.display = (toggleRitenuta && toggleRitenuta.checked) ? 'flex' : 'none';
        }
        if (rigaBollo) {
            rigaBollo.style.display = (bolloCheckbox && bolloCheckbox.checked) ? 'flex' : 'none';
        }
        if (rigaNetto) {
            rigaNetto.style.display = (toggleRitenuta && toggleRitenuta.checked) ? 'flex' : 'none';
        }
    }

    /**
     * Carica i dati del prodotto selezionato via AJAX
     * @param {HTMLSelectElement} selectElement - Select del prodotto
     */
    function caricaDatiProdotto(selectElement) {
        const prodottoId = selectElement.value;
        const riga = selectElement.closest('.riga-row') || selectElement.closest('.riga-mobile-card');

        if (!prodottoId || !riga) return;

        // Mostra indicatore di caricamento
        selectElement.disabled = true;
        selectElement.style.opacity = '0.6';

        fetch('/prodotti/get-data/' + prodottoId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data && !data.error) {
                // Popola i campi della riga con animazione
                const campi = [
                    { selector: '.descrizione-input', value: data.descrizione },
                    { selector: '.um-input', value: data.unita_misura },
                    { selector: '.prezzo-input', value: data.prezzo_vendita },
                    { selector: '.iva-input', value: data.aliquota_iva },
                    { selector: '.natura-select', value: data.natura }
                ];

                campi.forEach(function(campo) {
                    const input = riga.querySelector(campo.selector);
                    if (input && campo.value !== undefined && campo.value !== null) {
                        input.value = campo.value;
                        // Flash effect
                        input.style.transition = 'background-color 0.3s ease';
                        input.style.backgroundColor = 'rgba(var(--bs-success-rgb), 0.15)';
                        setTimeout(function() {
                            input.style.backgroundColor = '';
                        }, 500);
                    }
                });

                // Ricalcola totali
                calcolaTotaleRiga(riga);
                calcolaTotaliFattura();
            }
        })
        .catch(function(error) {
            // Error handled silently in production
        })
        .finally(function() {
            selectElement.disabled = false;
            selectElement.style.opacity = '1';
        });
    }

    /**
     * Formatta un numero come valuta EUR
     * @param {number} value - Valore da formattare
     * @returns {string} - Valore formattato
     */
    function formatCurrency(value) {
        return new Intl.NumberFormat('it-IT', {
            style: 'currency',
            currency: 'EUR'
        }).format(value);
    }

    /**
     * Formatta un numero con separatore decimale italiano
     * @param {number} value - Valore da formattare
     * @returns {string} - Valore formattato
     */
    function formatNumber(value) {
        return new Intl.NumberFormat('it-IT', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

})();
