<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use CakephpFactoryMuffin\FactoryLoader;

class ArticlesServiceTest extends IntegrationTestCase
{
    use FixturesTrait;

    public function testUnauthenticatedErrorOnUpdateIfNotLoggedIn()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->headers = [];
        $this->sendRequest("/articles/{$article->slug}", 'PUT', ['article' => ['body' => 'new text']]);
        $this->assertStatus(401);
        $record = TableRegistry::get('Articles')->find()->where(['slug' => $article->slug])->first();
        $this->assertEquals($article->body, $record->body);
    }

    public function testUpdateNotExistsArticles()
    {
        $this->sendRequest("/articles/unknown", 'PUT', ['article' => ['body' => 'new text']]);
        $this->assertStatus(404);
    }

    public function testForbiddenErrorIfUpdateOtherUserArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->sendRequest("/articles/{$article->slug}", 'PUT', ['article' => ['body' => 'new text']]);
        $this->assertStatus(403);
        $this->assertEquals(1, TableRegistry::get('Articles')->find()->where(['id' => $article->id])->count());
    }

    public function testUnauthenticatedErrorOnDeleteIfNotLoggedIn()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->headers = [];
        $this->sendRequest("/articles/{$article->slug}", 'DELETE', []);
        $this->assertStatus(401);

        $this->assertEquals(1, TableRegistry::get('Articles')->find()->where(['id' => $article->id])->count());
    }

    public function testDeleteNotExistsArticles()
    {
        $this->sendRequest("/articles/unknown", 'DELETE', []);
        $this->assertStatus(404);
    }

    public function testViewNonExistsArticle()
    {
        $this->sendRequest("/articles/unknown", 'GET', []);
        $this->assertStatus(404);
    }

    public function testForbiddenErrorIfDeleteOtherUserArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->sendRequest("/articles/{$article->slug}", 'DELETE', []);
        $this->assertStatus(403);
        $this->assertEquals(1, TableRegistry::get('Articles')->find()->where(['id' => $article->id])->count());
    }

    public function testSuccessEmptyFeed()
    {
        $this->sendRequest("/articles/feed", 'GET');
        $this->assertStatus(200);
        $this->assertArraySubset([
			'articles' => [],
			'articlesCount' => 0
        ], $this->responseJson());
    }

    public function testReturnFollowedUserArticles()
    {
        $articles = FactoryLoader::seed(2, 'Articles', ['author_id' => $this->user->id]);

		TableRegistry::get('Follows')->follow($this->loggedInUser->id, $this->user->id);

        $this->sendRequest("/articles/feed", 'GET');
        $this->assertStatus(200);
        $response = $this->responseJson();
        $this->assertArraySubset([
            'articlesCount' => 2
        ], $response);

        $articles = TableRegistry::get('Articles')->find()
                 ->where(['author_id' => $this->user->id])
                 ->select(['slug'])
                 ->order(['created' => 'desc'])
                 ->all()
                 ->extract('slug')
                 ->toArray();
        $this->assertEquals(array_values($articles), array_column($response['articles'], 'slug'));
    }

    public function testReturnFollowedUserArticlesForHugeDataset()
    {
        $articles = FactoryLoader::seed(25, 'Articles', ['author_id' => $this->user->id]);

		TableRegistry::get('Follows')->follow($this->loggedInUser->id, $this->user->id);

        $this->sendRequest("/articles/feed", 'GET');
        $this->assertStatus(200);
        $response = $this->responseJson();
        $this->assertArraySubset([
            'articlesCount' => 25
        ], $response);
        $this->assertCount(20, $response['articles'], 'Expected feed to set default limit to 20');

        $this->sendRequest("/articles/feed?limit=10&offset=5", 'GET');
        $this->assertStatus(200);
        $response = $this->responseJson();
        $this->assertArraySubset([
            'articlesCount' => 25
        ], $response);
        $this->assertCount(10, $response['articles'], 'Expected feed to set limit to 10');
        $articles = TableRegistry::get('Articles')->find()
                 ->where(['author_id' => $this->user->id])
                 ->select(['slug'])
                 ->order(['created' => 'desc'])
                 ->all()
                 ->skip(5)
                 ->take(10)
                 ->extract('slug')
                 ->toArray();
        $this->assertEquals(array_values($articles),
            array_column($response['articles'], 'slug'),
            'Expected latest 10 feed articles with 5 offset'
        );
    }

    public function testReturnFeedWithFavoriteAndFollowingFilled()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);

		TableRegistry::get('Follows')->follow($this->loggedInUser->id, $this->user->id);

        $this->sendRequest("/articles/feed", 'GET');
        $this->assertStatus(200);
        $response = $this->responseJson();
        $this->assertArraySubset([
            'articles' => [
                [
                    'slug' => $article->slug,
                    'favorited' => false,
                    'favoritesCount' => 0,
                    'author' => [
                        'username' => $this->user->username,
                        'following' => true,
                    ]
                ],
            ]
        ], $response);

        TableRegistry::get('Articles')->favorite($article->id, $this->loggedInUser->id);

        $this->sendRequest("/articles/feed", 'GET');
        $this->assertStatus(200);
        $response = $this->responseJson();
        $this->assertArraySubset([
            'articles' => [
                [
                    'slug' => $article->slug,
                    'favorited' => true,
                    'favoritesCount' => 1,
                    'author' => [
                        'username' => $this->user->username,
                        'following' => true,
                    ]
                ],
            ]
        ], $response);
    }

    public function testUnauthenticatedErrorIfNotLoggedIn()
    {
        $this->headers = [];
        $this->sendRequest("/articles/feed", 'GET', []);
        $this->assertStatus(401);
    }
}
