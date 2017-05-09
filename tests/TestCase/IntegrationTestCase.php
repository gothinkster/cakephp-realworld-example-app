<?php

namespace App\Test\TestCase;

use CakeDC\Api\TestSuite\IntegrationTestCase as BaseTestCase;
use CakephpFactoryMuffin\FactoryLoader;

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
            'Authorization' => "Token {$this->loggedInUser->token}"
        ];
    }

    /**
     * @inheritdoc
     */
    public function sendRequest($url, $method, $data = [], $userId = null)
    {
        $this->configRequest(['headers' => $this->headers]);
        parent::sendRequest($url, $method, $data);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        FactoryLoader::flush();
    }
}
