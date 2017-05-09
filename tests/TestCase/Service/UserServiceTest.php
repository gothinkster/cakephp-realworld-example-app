<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;

class UserServiceTest extends IntegrationTestCase
{
    use FixturesTrait;

    public function testValidUserIfLogged()
    {
        $this->sendRequest("/user", 'GET');
        $this->assertStatus(200);

        $this->assertArraySubset([
            'user' => [
                'email' => $this->loggedInUser->email,
                'username' => $this->loggedInUser->username,
                'bio' => $this->loggedInUser->bio,
                'image' => $this->loggedInUser->image,
            ]
        ], $this->responseJson());
    }

    public function testUnauthorizedIdNotLoggedIn()
    {
        $this->headers = [];
        $this->sendRequest("/user", 'GET');
        $this->assertStatus(401);
    }

    public function testUpdateUser()
    {
        $data = [
            'user' => [
                'username' => 'user123',
                'email' => 'user123@world.com',
                'password' => 'secretpassword',
                'bio' => 'hello',
                'image' => 'http://image.com/user.jpg',
            ]
        ];
        $this->sendRequest("/user", 'PUT', $data);
        $this->assertStatus(200);

        $this->assertArraySubset([
            'user' => [
                'username' => 'user123',
                'email' => 'user123@world.com',
                'bio' => 'hello',
                'image' => 'http://image.com/user.jpg',
            ]
        ], $this->responseJson());

        $this->sendRequest("/user", 'GET');
        $this->assertStatus(200);
        $this->assertArraySubset([
            'user' => [
                'username' => 'user123',
                'email' => 'user123@world.com',
                'bio' => 'hello',
                'image' => 'http://image.com/user.jpg',
            ]
        ], $this->responseJson());
    }

    public function testValidationErrorsOnUpdate()
    {
        $data = [
            'user' => [
                'username' => 'invalid username',
                'email' => 'invalid email',
                'password' => '1',
                'bio' => 'bio data',
                'image' => 'invalid url',
            ]
        ];

        $this->sendRequest("/user", 'PUT', $data);
        $this->assertStatus(422);

        $this->assertEquals([
            'errors' => [
                'email' => ['This field must be a valid email address.'],
                'password' => ['Password must be at least 6 characters.'],
                'image' => ['Invalid url'],
                'username' => ['Username may only contain letters and numbers.'],
            ]
        ], $this->responseJson());
    }

    public function testValidationErrorsOnUpdateToExistsUsernameOrEmail()
    {
        $data = [
            'user' => [
                'username' => $this->user->username,
                'email' => $this->user->email,
                'password' => 'passwd',
            ]
        ];

        $this->sendRequest("/user", 'PUT', $data);
        $this->assertStatus(422);
        $this->assertEquals([
            'errors' => [
                'username' => ['Username has already been taken.'],
                'email' => ['Email has already been taken.'],
            ]
        ], $this->responseJson());
    }
}
