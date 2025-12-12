<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * CategorieProdotti Controller
 *
 * @property \App\Model\Table\CategorieProdottiTable $CategorieProdotti
 */
class CategorieProdottiController extends AppController
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

        // Include ParentCategorieProdotti nell'index
        $this->Crud->on('beforePaginate', function (\Cake\Event\EventInterface $event) {
            /** @var \Cake\ORM\Query\SelectQuery $query */
            $query = $event->getSubject()->query;
            $query->contain(['ParentCategorieProdotti']);
        });

        // Include associations for view action
        $this->Crud->on('beforeFind', function (\Cake\Event\EventInterface $event) {
            if ($this->request->getParam('action') === 'view') {
                /** @var \Cake\ORM\Query\SelectQuery $query */
                $query = $event->getSubject()->query;
                $query->contain(['ParentCategorieProdotti', 'ChildCategorieProdotti', 'Tenants']);
            }
        });

        // Pass parent categories list for add/edit forms
        $this->Crud->on('beforeRender', function (\Cake\Event\EventInterface $event) {
            $action = $this->request->getParam('action');

            if ($action === 'add') {
                // Per add, mostra tutte le categorie
                $parentCategorieProdotti = $this->CategorieProdotti->find('treeList')->toArray();
                $this->set('parentCategorieProdotti', $parentCategorieProdotti);
            } elseif ($action === 'edit') {
                $subject = $event->getSubject();
                $entity = $subject->entity ?? null;

                if ($entity && $entity->id) {
                    // Ottieni tutti i discendenti della categoria corrente
                    $descendants = $this->CategorieProdotti->find('children', for: $entity->id)
                        ->select(['id'])
                        ->all()
                        ->extract('id')
                        ->toArray();

                    // Escludi la categoria corrente e i suoi discendenti
                    $excludeIds = array_merge([$entity->id], $descendants);

                    $parentCategorieProdotti = $this->CategorieProdotti->find('treeList')
                        ->where(['id NOT IN' => $excludeIds])
                        ->toArray();

                    $this->set('parentCategorieProdotti', $parentCategorieProdotti);
                } else {
                    // Fallback: mostra tutte le categorie
                    $parentCategorieProdotti = $this->CategorieProdotti->find('treeList')->toArray();
                    $this->set('parentCategorieProdotti', $parentCategorieProdotti);
                }
            }
        });
    }
}
