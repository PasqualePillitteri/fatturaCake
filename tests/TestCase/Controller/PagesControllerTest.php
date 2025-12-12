<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase\Controller;

use ArrayObject;
use Authentication\Identity;
use Cake\Core\Configure;
use Cake\TestSuite\Constraint\Response\StatusCode;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * PagesControllerTest class
 */
class PagesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.Tenants',
    ];

    /**
     * Helper method to login as superadmin (bypasses subscription check).
     *
     * @return void
     */
    protected function loginAsUser(): void
    {
        $userData = new ArrayObject([
            'id' => 1,
            'tenant_id' => null,
            'role' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
        ]);

        $identity = new Identity($userData);

        $this->session([
            'Auth' => [
                'id' => 1,
                'tenant_id' => null,
                'role' => 'superadmin',
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
            ],
        ]);

        $this->configRequest([
            'attributes' => [
                'identity' => $identity,
            ],
        ]);
    }

    /**
     * testDisplay method
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->loginAsUser();
        Configure::write('debug', true);
        $this->get('/pages/home');
        $this->assertResponseOk();
        $this->assertResponseContains('<html>');
    }

    /**
     * Test that missing template renders 404 page in production
     *
     * @return void
     */
    public function testMissingTemplate()
    {
        $this->loginAsUser();
        Configure::write('debug', false);
        $this->get('/pages/not_existing');

        $this->assertResponseError();
    }

    /**
     * Test that missing template in debug mode renders missing_template error page
     *
     * @return void
     */
    public function testMissingTemplateInDebug()
    {
        $this->loginAsUser();
        Configure::write('debug', true);
        $this->get('/pages/not_existing');

        $this->assertResponseFailure();
        $this->assertResponseContains('Missing Template');
    }

    /**
     * Test directory traversal protection
     *
     * @return void
     */
    public function testDirectoryTraversalProtection()
    {
        $this->loginAsUser();
        $this->get('/pages/../Layout/ajax');
        $this->assertResponseCode(403);
    }

    /**
     * Test that unauthenticated access redirects to login
     *
     * @return void
     */
    public function testRequiresAuthentication()
    {
        $this->get('/pages/home');
        $this->assertRedirectContains('/users/login');
    }

    /**
     * Test that CSRF protection is applied to page rendering.
     *
     * @return void
     */
    public function testCsrfAppliedError()
    {
        $this->loginAsUser();
        $this->post('/pages/home', ['hello' => 'world']);

        $this->assertResponseCode(403);
    }

    /**
     * Test that CSRF protection is applied to page rendering.
     *
     * @return void
     */
    public function testCsrfAppliedOk()
    {
        $this->loginAsUser();
        $this->enableCsrfToken();
        $this->post('/pages/home', ['hello' => 'world']);

        $this->assertThat(403, $this->logicalNot(new StatusCode($this->_response)));
    }
}
