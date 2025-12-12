<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Prodotti Controller
 *
 * @property \App\Model\Table\ProdottiTable $Prodotti
 */
class ProdottiController extends AppController
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Crud->mapAction('index', 'Crud.Index');
        $this->Crud->mapAction('view', 'Crud.View');
        $this->Crud->mapAction('add', 'Crud.Add');
        $this->Crud->mapAction('edit', 'Crud.Edit');
        $this->Crud->mapAction('delete', 'Crud.Delete');

        // Include Categorias association in index query
        $this->Crud->on('beforePaginate', function (\Cake\Event\EventInterface $event) {
            /** @var \Cake\ORM\Query\SelectQuery $query */
            $query = $event->getSubject()->query;
            $query->contain(['Categorias']);
        });

        // Pass categories to index view for filter dropdown
        $this->Crud->on('beforeRender', function (\Cake\Event\EventInterface $event) {
            if ($this->request->getParam('action') === 'index') {
                $categorias = $this->Prodotti->Categorias->find('list', limit: 200)->toArray();
                $this->set(compact('categorias'));
            }
        });
    }

    /**
     * Get product data as JSON for AJAX requests
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response
     */
    public function getData($id = null)
    {
        $this->request->allowMethod(['get', 'ajax']);

        if (!$id) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'ID required']));
        }

        $prodotto = $this->Prodotti->find()
            ->where(['id' => $id, 'is_active' => true])
            ->first();

        if (!$prodotto) {
            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Product not found']));
        }

        $data = [
            'id' => $prodotto->id,
            'codice' => $prodotto->codice,
            'codice_tipo' => $prodotto->codice_tipo,
            'codice_valore' => $prodotto->codice_valore,
            'nome' => $prodotto->nome,
            'descrizione' => $prodotto->descrizione ?: $prodotto->nome,
            'unita_misura' => $prodotto->unita_misura,
            'prezzo_vendita' => $prodotto->prezzo_vendita,
            'prezzo_acquisto' => $prodotto->prezzo_acquisto,
            'aliquota_iva' => $prodotto->aliquota_iva,
            'natura' => $prodotto->natura,
            'riferimento_normativo' => $prodotto->riferimento_normativo,
            'soggetto_ritenuta' => $prodotto->soggetto_ritenuta,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($data));
    }
}
