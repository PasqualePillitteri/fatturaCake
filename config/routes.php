<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * This file is loaded in the context of the `Application` class.
 * So you can use `$this` to reference the application class instance
 * if required.
 */
return function (RouteBuilder $routes): void {
    /*
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `{plugin}`, `{controller}` and
     * `{action}` markers.
     */
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        /*
         * Dashboard come pagina principale
         */
        $builder->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);

        /*
         * ...and connect the rest of 'Pages' controller's URLs.
         */
        $builder->connect('/pages/*', 'Pages::display');

        // CRUD Resources routes
        $builder->resources('Tenants');
        $builder->resources('Users');
        $builder->resources('Anagrafiche');

        // Route dedicate per Clienti e Fornitori
        $builder->connect('/clienti', ['controller' => 'Anagrafiche', 'action' => 'indexClienti']);
        $builder->connect('/clienti/add', ['controller' => 'Anagrafiche', 'action' => 'addCliente']);
        $builder->connect('/fornitori', ['controller' => 'Anagrafiche', 'action' => 'indexFornitori']);
        $builder->connect('/fornitori/add', ['controller' => 'Anagrafiche', 'action' => 'addFornitore']);

        // Route dedicate per Fatture Attive e Passive
        $builder->connect('/fatture-attive', ['controller' => 'Fatture', 'action' => 'indexAttive']);
        $builder->connect('/fatture-attive/add', ['controller' => 'Fatture', 'action' => 'addAttiva']);
        $builder->connect('/fatture-passive', ['controller' => 'Fatture', 'action' => 'indexPassive']);
        $builder->connect('/fatture-passive/add', ['controller' => 'Fatture', 'action' => 'addPassiva']);

        // Import Fatture da Excel
        $builder->connect('/import/fatture', ['controller' => 'Import', 'action' => 'fatture']);
        $builder->connect('/import/fatture/template', ['controller' => 'Import', 'action' => 'downloadTemplate']);
        $builder->connect('/import/fatture/preview', ['controller' => 'Import', 'action' => 'preview']);
        $builder->connect('/import/fatture/execute', ['controller' => 'Import', 'action' => 'execute']);

        // Import Fatture da XML/ZIP
        $builder->connect('/import/fatture-xml', ['controller' => 'Import', 'action' => 'fattureXml']);
        $builder->connect('/import/fatture-xml/preview', ['controller' => 'Import', 'action' => 'previewXml']);
        $builder->connect('/import/fatture-xml/execute', ['controller' => 'Import', 'action' => 'executeXml']);

        // Export Fatture
        $builder->connect('/export', ['controller' => 'Export', 'action' => 'index']);
        $builder->connect('/export/excel', ['controller' => 'Export', 'action' => 'excel']);
        $builder->connect('/export/xml', ['controller' => 'Export', 'action' => 'xml']);

        $builder->resources('Fatture', [
            'map' => [
                'righe' => [
                    'action' => 'righe',
                    'method' => 'GET',
                ],
            ],
        ]);
        $builder->resources('Prodotti');
        $builder->resources('CategorieProdotti');
        $builder->resources('Listini');
        $builder->resources('ConfigurazioniSdi');
        $builder->resources('LogAttivita', ['only' => ['index', 'view']]);

        /*
         * Connect catchall routes for all controllers.
         *
         * The `fallbacks` method is a shortcut for
         *
         * ```
         * $builder->connect('/{controller}', ['action' => 'index']);
         * $builder->connect('/{controller}/{action}/*', []);
         * ```
         *
         * It is NOT recommended to use fallback routes after your initial prototyping phase!
         * See https://book.cakephp.org/5/en/development/routing.html#fallbacks-method for more information
         */
        $builder->fallbacks();
    });

    /*
     * API Routes - JSON/XML endpoints
     */
    $routes->scope('/api', function (RouteBuilder $builder): void {
        // Parse JSON and XML extensions
        $builder->setExtensions(['json', 'xml']);

        // API Resources
        $builder->resources('Tenants');
        $builder->resources('Users');
        $builder->resources('Anagrafiche');
        $builder->resources('Fatture');
        $builder->resources('FatturaRighe');
        $builder->resources('FatturaAllegati');
        $builder->resources('FatturaStatiSdi');
        $builder->resources('Prodotti');
        $builder->resources('CategorieProdotti');
        $builder->resources('Listini');
        $builder->resources('ListiniProdotti');
        $builder->resources('ConfigurazioniSdi');
        $builder->resources('LogAttivita', ['only' => ['index', 'view']]);

        $builder->fallbacks();
    });
};
