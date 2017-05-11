<?php

namespace App\Test\TestCase;

use CakeDC\Api\TestSuite\IntegrationTestCase as BaseTestCase;
use CakephpFactoryMuffin\FactoryLoader;
use Cake\Datasource\EntityInterface;

abstract class IntegrationTestCase extends BaseTestCase
{
    protected $loggedInUser;

    protected $user;

    protected $headers;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        FactoryLoader::loadAll();
        $this->useHttpServer(true);

        $users = FactoryLoader::seed(2, 'Users');

        $this->loggedInUser = $users[0];

        $this->user = $users[1];

        $this->headers = [
            'Authorization' => "Token {$this->loggedInUser->token}",
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Send json request.
     *
     * @param string $url Request api url.
     * @param string $method Request method.
     * @param array $data Request data.
     * @param EntityInterface $authorizeWithUser User to authorize with.
     * @return void
     */
    public function sendAuthJsonRequest($url, $method, $data = [])
    {
        $this->sendJsonRequest($url, $method, $data, $this->loggedInUser);
    }

    /**
     * Send json request.
     *
     * @param string $url Request api url.
     * @param string $method Request method.
     * @param array $data Request data.
     * @param EntityInterface $authWithUser User to authorize with.
     * @return void
     */
    public function sendJsonRequest($url, $method, $data = [], $authWithUser = null)
    {
        $headers = [];
        if ($method != 'GET' && is_array($data)) {
            $data = json_encode($data);
        }
        $headers['Content-Type'] = 'application/json';
        if ($authWithUser !== null) {
            $headers['Authorization'] = "Token {$authWithUser->token}";
        }
        $this->configRequest(['headers' => $headers]);
        $this->sendRequest($url, $method, $data);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        FactoryLoader::flush();
    }
}
