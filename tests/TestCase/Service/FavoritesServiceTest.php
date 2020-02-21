<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;
use CakephpFactoryMuffin\FactoryLoader;
use Cake\ORM\TableRegistry;

class FavoritesServiceTest extends IntegrationTestCase
{
    use FixturesTrait;

    protected $article;

    public function setUp()
    {
        parent::setUp();

        $this->article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
    }

    public function testSuccessAddAndRemoveFavorite()
    {
        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/favorite", 'POST');
        $this->assertResponseSuccess();
        $this->assertIsFavorited();

        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/favorite", 'DELETE');
        $this->assertResponseSuccess();
        $this->assertIsNotFavorited();
    }

    public function testFavoriteCountersIsCorrect()
    {
        $this->sendAuthJsonRequest("/articles/{$this->article->slug}", 'GET');
        $this->assertResponseSuccess();
        $this->assertIsNotFavorited();

        $Articles = TableRegistry::get('Articles');
        $Articles->favorite($this->article->id, $this->user->id);

        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/favorite", 'POST');
        $this->assertResponseSuccess();
        $this->assertIsFavorited(2);

        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/favorite", 'DELETE');
        $this->assertResponseSuccess();
        $this->assertIsNotFavorited(1);

        $Articles->unfavorite($this->article->id, $this->user->id);

        $this->sendAuthJsonRequest("/articles/{$this->article->slug}", 'GET');
        $this->assertResponseSuccess();
        $this->assertIsNotFavorited();
    }

    public function testUnauthenticatedErrorIfNotLoggedIn()
    {
        $this->sendJsonRequest("/articles/{$this->article->slug}/favorite", 'POST');
        $this->assertStatus(401);

        $this->sendJsonRequest("/articles/{$this->article->slug}/favorite", 'DELETE');
        $this->assertStatus(401);
    }

    protected function assertIsFavorited($count = 1)
    {
        $this->assertArraySubset([
            'article' => [
                'favorited' => true,
                'favoritesCount' => $count,
            ]
        ], $this->getJsonResponse());
    }

    protected function assertIsNotFavorited($count = 0)
    {
        $this->assertArraySubset([
            'article' => [
                'favorited' => false,
                'favoritesCount' => $count,
            ]
        ], $this->getJsonResponse());
    }
}
