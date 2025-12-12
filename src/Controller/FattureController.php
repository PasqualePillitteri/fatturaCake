<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\AnagraficaFattura;
use App\Dto\FatturaData;
use App\Dto\LineaFattura;
use App\Service\FatturaXmlGenerator;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;

/**
 * Fatture Controller
 *
 * @property \App\Model\Table\FattureTable $Fatture
 */
class FattureController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->mapAction('delete', 'Crud.Delete');
    }

    /**
     * Index method - tutte le fatture con filtri search
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $query = $this->Fatture->find('search', search: $this->request->getQueryParams())
            ->contain(['Anagrafiche']);

        $fatture = $this->paginate($query);

        $this->set(compact('fatture'));
    }

    /**
     * Index Attive - Fatture emesse (vendita)
     *
     * @return \Cake\Http\Response|null|void
     */
    public function indexAttive()
    {
        $query = $this->Fatture->find('search', search: $this->request->getQueryParams())
            ->where(['Fatture.direzione' => 'emessa'])
            ->contain(['Anagrafiche']);

        $fatture = $this->paginate($query);

        $this->set(compact('fatture'));
    }

    /**
     * Index Passive - Fatture ricevute (acquisto)
     *
     * @return \Cake\Http\Response|null|void
     */
    public function indexPassive()
    {
        $query = $this->Fatture->find('search', search: $this->request->getQueryParams())
            ->where(['Fatture.direzione' => 'ricevuta'])
            ->contain(['Anagrafiche']);

        $fatture = $this->paginate($query);

        $this->set(compact('fatture'));
    }

    /**
     * Add Attiva - Nuova fattura emessa
     *
     * @return \Cake\Http\Response|null|void
     */
    public function addAttiva()
    {
        $fatture = $this->Fatture->newEmptyEntity();
        $fatture->direzione = 'emessa';

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['direzione'] = 'emessa';
            $fatture = $this->Fatture->patchEntity($fatture, $data, [
                'associated' => ['FatturaRighe'],
            ]);

            if ($this->Fatture->save($fatture)) {
                $this->Flash->success(__('La fattura attiva è stata salvata.'));
                return $this->redirect(['action' => 'indexAttive']);
            }
            $this->Flash->error(__('Impossibile salvare la fattura. Riprova.'));
        }

        if (empty($fatture->fattura_righe)) {
            $fatture->fattura_righe = [$this->Fatture->FatturaRighe->newEmptyEntity()];
        }

        // Solo clienti per fatture attive
        $anagrafiche = $this->Fatture->Anagrafiche->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return $entity->denominazione ?: $entity->nome . ' ' . $entity->cognome;
            }
        ])->where([
            'is_active' => true,
            'tipo IN' => ['cliente', 'entrambi']
        ])->order(['denominazione' => 'ASC']);

        $prodotti = $this->Fatture->FatturaRighe->Prodotti->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return ($entity->codice ? $entity->codice . ' - ' : '') . $entity->nome;
            }
        ])->where(['is_active' => true])->order(['nome' => 'ASC']);

        $this->set(compact('fatture', 'anagrafiche', 'prodotti'));
    }

    /**
     * Add Passiva - Nuova fattura ricevuta
     *
     * @return \Cake\Http\Response|null|void
     */
    public function addPassiva()
    {
        $fatture = $this->Fatture->newEmptyEntity();
        $fatture->direzione = 'ricevuta';

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['direzione'] = 'ricevuta';
            $fatture = $this->Fatture->patchEntity($fatture, $data, [
                'associated' => ['FatturaRighe'],
            ]);

            if ($this->Fatture->save($fatture)) {
                $this->Flash->success(__('La fattura passiva è stata salvata.'));
                return $this->redirect(['action' => 'indexPassive']);
            }
            $this->Flash->error(__('Impossibile salvare la fattura. Riprova.'));
        }

        if (empty($fatture->fattura_righe)) {
            $fatture->fattura_righe = [$this->Fatture->FatturaRighe->newEmptyEntity()];
        }

        // Solo fornitori per fatture passive
        $anagrafiche = $this->Fatture->Anagrafiche->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return $entity->denominazione ?: $entity->nome . ' ' . $entity->cognome;
            }
        ])->where([
            'is_active' => true,
            'tipo IN' => ['fornitore', 'entrambi']
        ])->order(['denominazione' => 'ASC']);

        $prodotti = $this->Fatture->FatturaRighe->Prodotti->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return ($entity->codice ? $entity->codice . ' - ' : '') . $entity->nome;
            }
        ])->where(['is_active' => true])->order(['nome' => 'ASC']);

        $this->set(compact('fatture', 'anagrafiche', 'prodotti'));
    }

    /**
     * View method - visualizza fattura con layout stile FE
     *
     * @param string|null $id Fattura id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function view($id = null)
    {
        $fattura = $this->Fatture->get($id, contain: [
            'Tenants',
            'Anagrafiche',
            'FatturaRighe' => ['sort' => ['FatturaRighe.numero_linea' => 'ASC']],
            'FatturaRighe.Prodotti',
            'FatturaAllegati',
            'FatturaStatiSdi' => ['sort' => ['FatturaStatiSdi.created' => 'DESC']],
            'CreatedByUsers',
            'ModifiedByUsers',
        ]);

        $this->set(compact('fattura'));
    }

    /**
     * Add method - gestisce creazione fattura con righe
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $fatture = $this->Fatture->newEmptyEntity();

        if ($this->request->is('post')) {
            $fatture = $this->Fatture->patchEntity($fatture, $this->request->getData(), [
                'associated' => ['FatturaRighe'],
            ]);

            if ($this->Fatture->save($fatture)) {
                $this->Flash->success(__('La fattura è stata salvata.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Impossibile salvare la fattura. Riprova.'));
        }

        // Prepara una riga vuota per il template
        if (empty($fatture->fattura_righe)) {
            $fatture->fattura_righe = [$this->Fatture->FatturaRighe->newEmptyEntity()];
        }

        $anagrafiche = $this->Fatture->Anagrafiche->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return $entity->denominazione ?: $entity->nome . ' ' . $entity->cognome;
            }
        ])->where(['is_active' => true])->order(['denominazione' => 'ASC']);

        $prodotti = $this->Fatture->FatturaRighe->Prodotti->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return ($entity->codice ? $entity->codice . ' - ' : '') . $entity->nome;
            }
        ])->where(['is_active' => true])->order(['nome' => 'ASC']);

        $this->set(compact('fatture', 'anagrafiche', 'prodotti'));
    }

    /**
     * Download XML method - scarica il file XML della fattura
     *
     * @param string|null $id Fattura id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function downloadXml($id = null)
    {
        $fattura = $this->Fatture->get($id, [
            'fields' => ['id', 'nome_file', 'xml_content'],
        ]);

        if (empty($fattura->xml_content)) {
            $this->Flash->error(__('XML non disponibile per questa fattura.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        // Il campo LONGBLOB viene restituito come resource, convertilo in stringa
        $xmlContent = $fattura->xml_content;
        if (is_resource($xmlContent)) {
            $xmlContent = stream_get_contents($xmlContent);
        }

        $filename = $fattura->nome_file ?: 'fattura_' . $id . '.xml';

        return $this->response
            ->withStringBody($xmlContent)
            ->withType('xml')
            ->withDownload($filename);
    }

    /**
     * Generate XML method - genera il file XML FatturaPA
     *
     * @param string|null $id Fattura id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function generateXml($id = null)
    {
        $fattura = $this->Fatture->get($id, contain: [
            'Tenants',
            'Anagrafiche',
            'FatturaRighe' => ['sort' => ['FatturaRighe.numero_linea' => 'ASC']],
            'FatturaRighe.Prodotti',
        ]);

        try {
            // Prepara dati cedente (tenant)
            $cedente = new AnagraficaFattura();
            $cedente->setIdPaese('IT')
                ->setIdCodice($fattura->tenant->partita_iva)
                ->setCodiceFiscale($fattura->tenant->codice_fiscale)
                ->setDenominazione($fattura->tenant->nome)
                ->setRegimeFiscale('RF01') // Default ordinario
                ->setIndirizzo($fattura->tenant->indirizzo)
                ->setCap($fattura->tenant->cap)
                ->setComune($fattura->tenant->citta)
                ->setProvincia($fattura->tenant->provincia)
                ->setNazione('IT')
                ->setPec($fattura->tenant->pec);

            // Prepara dati cessionario (cliente)
            $cessionario = new AnagraficaFattura();
            $cliente = $fattura->anagrafiche;

            if ($cliente->partita_iva) {
                $cessionario->setIdPaese($cliente->nazione === 'IT' ? 'IT' : $cliente->nazione)
                    ->setIdCodice($cliente->partita_iva);
            }
            if ($cliente->codice_fiscale) {
                $cessionario->setCodiceFiscale($cliente->codice_fiscale);
            }

            if ($cliente->denominazione) {
                $cessionario->setDenominazione($cliente->denominazione);
            } else {
                $cessionario->setNome($cliente->nome)
                    ->setCognome($cliente->cognome);
            }

            $cessionario->setIndirizzo($cliente->indirizzo . ($cliente->numero_civico ? ' ' . $cliente->numero_civico : ''))
                ->setCap($cliente->cap)
                ->setComune($cliente->comune)
                ->setProvincia($cliente->provincia)
                ->setNazione($cliente->nazione ?: 'IT')
                ->setPec($cliente->pec);

            // Determina codice destinatario
            $codiceDestinatario = $cliente->codice_sdi ?: '0000000';
            $pecDestinatario = null;
            if ($codiceDestinatario === '0000000' && $cliente->pec) {
                $pecDestinatario = $cliente->pec;
            }

            // Crea FatturaData
            $fatturaData = new FatturaData();
            $fatturaData->setFormatoTrasmissione(FatturaData::FPR12) // B2B/B2C
                ->setProgressivoInvio(str_pad((string)$fattura->id, 5, '0', STR_PAD_LEFT))
                ->setCodiceDestinatario($codiceDestinatario)
                ->setCedente($cedente)
                ->setCessionario($cessionario)
                ->setTipoDocumento($fattura->tipo_documento)
                ->setDivisa($fattura->divisa)
                ->setData($fattura->data)
                ->setNumero($fattura->numero)
                ->setCondizioniPagamento($fattura->condizioni_pagamento)
                ->setModalitaPagamento($fattura->modalita_pagamento)
                ->setEsigibilitaIva($fattura->esigibilita_iva);

            if ($pecDestinatario) {
                $fatturaData->setPecDestinatario($pecDestinatario);
            }

            if ($fattura->causale) {
                $fatturaData->addCausale($fattura->causale);
            }

            if ($fattura->data_scadenza_pagamento) {
                $fatturaData->setDataScadenzaPagamento($fattura->data_scadenza_pagamento);
            }

            if ($fattura->iban) {
                $fatturaData->setIbanPagamento($fattura->iban);
            }

            // Aggiungi righe
            foreach ($fattura->fattura_righe as $riga) {
                $lineaDto = new LineaFattura();
                $lineaDto->setDescrizione($riga->descrizione)
                    ->setQuantita((float)$riga->quantita)
                    ->setPrezzoUnitario((float)$riga->prezzo_unitario)
                    ->setAliquotaIva((float)$riga->aliquota_iva);

                if ($riga->unita_misura) {
                    $lineaDto->setUnitaMisura($riga->unita_misura);
                }

                if ($riga->prodotto && $riga->prodotto->codice) {
                    $lineaDto->setCodiceArticolo($riga->prodotto->codice, 'PROPRIO');
                }

                if ($riga->natura) {
                    $lineaDto->setNatura($riga->natura);
                }

                if ($riga->sconto_maggiorazione_percentuale) {
                    if ($riga->sconto_maggiorazione_tipo === 'SC') {
                        $lineaDto->addSconto((float)$riga->sconto_maggiorazione_percentuale);
                    } else {
                        $lineaDto->addMaggiorazione((float)$riga->sconto_maggiorazione_percentuale);
                    }
                }

                $fatturaData->addLinea($lineaDto);
            }

            // Genera XML
            $generator = new FatturaXmlGenerator();
            $xmlContent = $generator->generate($fatturaData);
            $nomeFile = $fatturaData->getNomeFile();

            // Salva nel database
            $fattura->xml_content = $xmlContent;
            $fattura->nome_file = $nomeFile;
            $fattura->xml_generato_at = DateTime::now();

            if ($this->Fatture->save($fattura)) {
                $this->Flash->success(__('XML generato con successo: {0}', $nomeFile));
            } else {
                $this->Flash->warning(__('XML generato ma non salvato nel database.'));
            }

        } catch (\Exception $e) {
            $this->Flash->error(__('Errore nella generazione XML: {0}', $e->getMessage()));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Edit method - gestisce modifica fattura con righe
     *
     * @param string|null $id Fattura id.
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function edit($id = null)
    {
        $fatture = $this->Fatture->get($id, contain: ['FatturaRighe']);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $fatture = $this->Fatture->patchEntity($fatture, $this->request->getData(), [
                'associated' => ['FatturaRighe'],
            ]);

            if ($this->Fatture->save($fatture)) {
                $this->Flash->success(__('La fattura è stata salvata.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Impossibile salvare la fattura. Riprova.'));
        }

        // Se non ci sono righe, ne prepara una vuota
        if (empty($fatture->fattura_righe)) {
            $fatture->fattura_righe = [$this->Fatture->FatturaRighe->newEmptyEntity()];
        }

        $anagrafiche = $this->Fatture->Anagrafiche->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return $entity->denominazione ?: $entity->nome . ' ' . $entity->cognome;
            }
        ])->where(['is_active' => true])->order(['denominazione' => 'ASC']);

        $prodotti = $this->Fatture->FatturaRighe->Prodotti->find('list', [
            'keyField' => 'id',
            'valueField' => function ($entity) {
                return ($entity->codice ? $entity->codice . ' - ' : '') . $entity->nome;
            }
        ])->where(['is_active' => true])->order(['nome' => 'ASC']);

        $this->set(compact('fatture', 'anagrafiche', 'prodotti'));
    }

    /**
     * Invia fattura a SDI (simulazione)
     *
     * @param string|null $id Fattura id.
     * @return \Cake\Http\Response|null
     */
    public function inviaSDI(?string $id = null)
    {
        $this->request->allowMethod(['post']);

        $fattura = $this->Fatture->get($id, contain: ['Anagrafiche', 'FatturaRighe']);

        // Check if already sent
        if (in_array($fattura->stato_sdi, ['inviata', 'consegnata', 'accettata'])) {
            $this->Flash->warning(__('Questa fattura è già stata inviata allo SDI.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        // Check if it's an outgoing invoice
        if ($fattura->direzione !== 'emessa') {
            $this->Flash->error(__('Solo le fatture emesse possono essere inviate allo SDI.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        // SIMULAZIONE: Genera un ID SDI fittizio e aggiorna lo stato
        $sdiIdentificativo = 'SIM' . date('YmdHis') . rand(1000, 9999);

        $fattura->stato_sdi = 'inviata';
        $fattura->sdi_identificativo = $sdiIdentificativo;
        $fattura->sdi_data_ricezione = DateTime::now();
        $fattura->sdi_messaggio = 'SIMULAZIONE: Fattura inviata con successo (ambiente di test)';

        if ($this->Fatture->save($fattura)) {
            // Log dello stato SDI
            $statiSdiTable = $this->fetchTable('FatturaStatiSdi');
            $statoSdi = $statiSdiTable->newEntity([
                'fattura_id' => $fattura->id,
                'stato' => 'inviata',
                'identificativo_sdi' => $sdiIdentificativo,
                'data_ora_ricezione' => DateTime::now(),
                'messaggio' => 'SIMULAZIONE: Invio fattura allo SDI completato (ambiente di test)',
            ]);
            $statiSdiTable->save($statoSdi);

            $this->Flash->success(__(
                'SIMULAZIONE: La fattura è stata inviata allo SDI. ID: {0}. ' .
                'Nota: Questa è una simulazione, la fattura NON è stata realmente trasmessa.',
                $sdiIdentificativo
            ));
        } else {
            $this->Flash->error(__('Errore durante l\'invio simulato allo SDI.'));
        }

        return $this->redirect(['action' => 'view', $id]);
    }
}
