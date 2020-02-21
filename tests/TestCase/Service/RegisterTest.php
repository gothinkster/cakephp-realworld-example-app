<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;

class RegisterTest extends IntegrationTestCase
{
    use FixturesTrait;

    public function testSuccessRegistrationReturnsToken()
    {
        $data = [
            'user' => [
                'username' => 'test',
                'email' => 'test@test.com',
                'password' => 'secret',
            ]
        ];
        $this->sendAuthJsonRequest("/users", 'POST', ($data));
        $result = $this->getJsonResponse();
        $this->assertResponseOk();
        $this->assertArraySubset([
            'user' => [
                'email' => 'test@test.com',
                'username' => 'test',
                'bio' => null,
                'image' => null,
            ]
        ], $result);
        $this->assertArrayHasKey('token', $result['user'], 'Token not found');
    }

    public function testNoDataReturnsValidationErrors()
    {
        $this->sendAuthJsonRequest("/users", 'POST', []);
        $this->assertStatus(422);
        $this->assertEquals([
            'errors' => [
                'username' => ['This field is required'],
                'email' => ['This field is required'],
                'password' => ['This field is required'],
            ]
        ], $this->getJsonResponse());
    }

    public function testPreciseValidationErrors()
    {
        $data = [
            'user' => [
                'username' => 'invalid username',
                'email' => 'invalid email',
                'password' => '1',
            ]
        ];
        $this->sendAuthJsonRequest("/users", 'POST', $data);
        $this->assertStatus(422, "Status invalid");
        $this->assertEquals([
            'errors' => [
                'username' => ['Username may only contain letters and numbers.'],
                'email' => ['This field must be a valid email address.'],
                'password' => ['Password must be at least 6 characters.'],
            ]
        ], $this->getJsonResponse());
    }

    public function testDuplicationValidationErrors()
    {
        $data = [
            'user' => [
                'username' => $this->user->username,
                'email' => $this->user->email,
                'password' => 'secret',
            ]
        ];
        $this->sendAuthJsonRequest("/users", 'POST', $data);
        $this->assertStatus(422);
        $this->assertEquals([
            'errors' => [
                'username' => ['Username has already been taken.'],
                'email' => ['Email has already been taken.'],
            ]
        ], $this->getJsonResponse());
    }
}
