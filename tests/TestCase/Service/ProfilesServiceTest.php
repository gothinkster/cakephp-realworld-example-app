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
        $this->sendAuthJsonRequest("/profiles/{$this->user->username}", 'GET');
        $this->assertResponseOk();

        $this->assertEquals([
            'profile' => [
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
                'following' => false,
            ]
        ], $this->getJsonResponse());
    }

    public function testNotFoundInvalidProfile()
    {
        $this->sendAuthJsonRequest("/profiles/unknown", 'GET');
        $this->assertStatus(404);
    }

    public function testFollowAndUnfollow()
    {
        $this->sendAuthJsonRequest("/profiles/{$this->user->username}/follow", 'POST');
        $this->assertResponseSuccess();
        $this->assertEquals([
            'profile' => [
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
                'following' => true,
            ]
        ], $this->getJsonResponse());
        $Follows = TableRegistry::get('Follows');
        $this->assertTrue($Follows->following($this->loggedInUser->id, $this->user->id), 'Failed to follow user');

        $this->sendAuthJsonRequest("/profiles/{$this->user->username}/follow", 'DELETE');
        $this->assertResponseSuccess();
        $this->assertEquals([
            'profile' => [
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
                'following' => false,
            ]
        ], $this->getJsonResponse());
        $this->assertFalse($Follows->following($this->loggedInUser->id, $this->user->id), 'Failed to unfollow user');
    }

    public function testFollowAndUnfollowNotExistsProfiles()
    {
        $this->sendAuthJsonRequest("/profiles/unknown/follow", 'POST');
        $this->assertStatus(404);

        $this->sendAuthJsonRequest("/profiles/unknown/follow", 'DELETE');
        $this->assertStatus(404);
    }

    public function testFollowAndUnfollowUnauthorized()
    {
        $this->sendJsonRequest("/profiles/{$this->user->username}/follow", 'POST');
        $this->assertStatus(401);

        $this->sendJsonRequest("/profiles/{$this->user->username}/follow", 'DELETE');
        $this->assertStatus(401);
    }
}
