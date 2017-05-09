<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class ProfilesServiceTest extends IntegrationTestCase
{
    use FixturesTrait;

    public function testValidProfile()
    {
        $this->sendRequest("/profiles/{$this->user->username}", 'GET');
        $this->assertResponseOk();

        $this->assertEquals([
            'profile' => [
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
                'following' => false,
            ]
        ], $this->responseJson());
    }

    public function testNotFoundInvalidProfile()
    {
        $this->sendRequest("/profiles/unknown", 'GET');
        $this->assertStatus(404);
    }

    public function testFollowAndUnfollow()
    {
        $this->sendRequest("/profiles/{$this->user->username}/follow", 'POST');
        $this->assertStatus(200);
        $this->assertEquals([
            'profile' => [
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
                'following' => true,
            ]
        ], $this->responseJson());
        $Follows = TableRegistry::get('Follows');
        $this->assertTrue($Follows->following($this->loggedInUser->id, $this->user->id), 'Failed to follow user');

        $this->sendRequest("/profiles/{$this->user->username}/follow", 'DELETE');
        $this->assertStatus(200);
        $this->assertEquals([
            'profile' => [
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
                'following' => false,
            ]
        ], $this->responseJson());
        $this->assertFalse($Follows->following($this->loggedInUser->id, $this->user->id), 'Failed to unfollow user');
    }

    public function testFollowAndUnfollowNotExistsProfiles()
    {
        $this->sendRequest("/profiles/unknown/follow", 'POST');
        $this->assertStatus(404);

        $this->sendRequest("/profiles/unknown/follow", 'DELETE');
        $this->assertStatus(404);
    }

    public function testFollowAndUnfollowUnauthorized()
    {
        $this->headers = [];
        $this->sendRequest("/profiles/{$this->user->username}/follow", 'POST');
        $this->assertStatus(401);

        $this->sendRequest("/profiles/{$this->user->username}/follow", 'DELETE');
        $this->assertStatus(401);
    }
}
