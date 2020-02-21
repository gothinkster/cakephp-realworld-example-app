<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;

class LoginTest extends IntegrationTestCase
{
    use FixturesTrait;

    public function testSuccessLogin()
    {
        $data = [
            'user' => [
                'email' => $this->user->email,
                'password' => 'passwd',
            ]
        ];

        $this->sendJsonRequest("/users/login", 'POST', $data);
        $result = $this->getJsonResponse();
        $this->assertResponseOk();
        $this->assertArraySubset([
            'user' => [
                'email' => $this->user->email,
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
            ]
        ], $result);

        $this->assertArrayHasKey('token', $result['user'], 'Token not found');
    }

    public function testNoDataReturnsValidationErrors()
    {
        $data = [
            'user' => []
        ];

        $this->sendJsonRequest("/users/login", 'POST', $data);
        $this->assertStatus(422);
        $this->assertEquals([
            'errors' => [
                'email' => ['This field is required'],
                'password' => ['This field is required'],
            ]
        ], $this->getJsonResponse());
    }

    public function testPreciseValidationErrors()
    {
        $data = [
            'user' => [
                'email' => 'invalid email',
                'password' => 'secret',
            ]
        ];

        $this->sendJsonRequest("/users/login", 'POST', $data);
        $this->assertStatus(422);
        $this->assertEquals([
            'errors' => [
                'email' => ['This field must be a valid email address.'],
            ]
        ], $this->getJsonResponse());
    }
}
