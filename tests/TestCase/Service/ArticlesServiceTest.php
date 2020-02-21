<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;
use CakephpFactoryMuffin\FactoryLoader;
use Cake\ORM\TableRegistry;

class ArticlesServiceTest extends IntegrationTestCase
{
    use FixturesTrait;

    public function testSuccessEmptyArticlesListOnFavorites()
    {
        $this->sendJsonRequest("/articles", 'GET', ['favorited' => $this->user->username]);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [],
            'articlesCount' => 0
        ], $this->getJsonResponse());

        $this->sendJsonRequest("/articles", 'GET', ['favorited' => "unknown"]);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [],
            'articlesCount' => 0
        ], $this->getJsonResponse());
    }

    public function testSuccessEmptyArticlesListOnTagFilter()
    {
        $this->sendJsonRequest("/articles", 'GET', ['tag' => 'tag']);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [],
            'articlesCount' => 0
        ], $this->getJsonResponse());
    }

    public function testArticlesListOnFavorites()
    {
        $articles = FactoryLoader::seed(5, 'Articles', ['author_id' => $this->loggedInUser->id]);
        $articles = collection($articles)->sortBy(function ($comment) {
            return $comment->created->format('Y-m-d H:i:s');
        }, SORT_DESC, SORT_STRING)->toList();

        TableRegistry::get('Articles')->favorite($articles[0]->id, $this->user->id);
        TableRegistry::get('Articles')->favorite($articles[2]->id, $this->user->id);
        TableRegistry::get('Articles')->favorite($articles[4]->id, $this->user->id);

        $this->sendJsonRequest("/articles", 'GET', ['favorited' => $this->user->username]);
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
        $this->assertArraySubset([
            'articles' => [
                [
                    'slug' => $articles[0]->slug,
                    'title' => $articles[0]->title,
                    'author' => [
                        'username' => $this->loggedInUser->username
                    ]
                ],
                [
                    'slug' => $articles[2]->slug,
                    'title' => $articles[2]->title,
                    'author' => [
                        'username' => $this->loggedInUser->username
                    ]
                ],
                [
                    'slug' => $articles[4]->slug,
                    'title' => $articles[4]->title,
                    'author' => [
                        'username' => $this->loggedInUser->username
                    ]
                ],
            ],
            'articlesCount' => 3
        ], $response);
    }

    public function testSuccessEmptyArticlesListOnAuthorFilter()
    {
        $this->sendJsonRequest("/articles", 'GET', ['author' => $this->user->username]);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [],
            'articlesCount' => 0
        ], $this->getJsonResponse());

        $this->sendJsonRequest("/articles", 'GET', ['author' => "unknown"]);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [],
            'articlesCount' => 0
        ], $this->getJsonResponse());
    }

    public function testArticlesListOnAuthorFilter()
    {
        $articles = FactoryLoader::seed(3, 'Articles', ['author_id' => $this->user->id]);
        FactoryLoader::seed(5, 'Articles', ['author_id' => $this->loggedInUser->id]);
        $articles = collection($articles)->sortBy(function ($comment) {
            return $comment->created->format('Y-m-d H:i:s');
        }, SORT_DESC, SORT_STRING)->toList();

        $this->sendJsonRequest("/articles", 'GET', ['author' => $this->user->username]);
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
        $this->assertArraySubset([
            'articles' => [
                [
                    'slug' => $articles[0]->slug,
                    'title' => $articles[0]->title,
                    'author' => [
                        'username' => $this->user->username
                    ]
                ],
                [
                    'slug' => $articles[1]->slug,
                    'title' => $articles[1]->title,
                    'author' => [
                        'username' => $this->user->username
                    ]
                ],
                [
                    'slug' => $articles[2]->slug,
                    'title' => $articles[2]->title,
                    'author' => [
                        'username' => $this->user->username
                    ]
                ],
            ],
            'articlesCount' => 3
        ], $response);
    }

    public function testSuccessEmptyArticlesList()
    {
        $this->sendJsonRequest("/articles", 'GET');
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [],
            'articlesCount' => 0
        ], $this->getJsonResponse());
    }

    public function testArticlesList()
    {
        $articles = FactoryLoader::seed(2, 'Articles', ['author_id' => $this->user->id]);

        $articles = collection($articles)->sortBy(function ($comment) {
            return $comment->created->format('Y-m-d H:i:s');
        }, SORT_DESC, SORT_STRING)->toList();

        $this->sendJsonRequest("/articles", 'GET');
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
        $this->assertArraySubset([
            'articles' => [
                [
                    'title' => $articles[0]->title,
                    'slug' => $articles[0]->slug,
                    'description' => $articles[0]->description,
                    'body' => $articles[0]->body,
                    'favorited' => false,
                    'favoritesCount' => 0,
                    'author' => [
                        'username' => $this->user->username,
                        'bio' => $this->user->bio,
                        'image' => $this->user->image,
                        'following' => false,
                    ]
                ],
                [
                    'title' => $articles[1]->title,
                    'slug' => $articles[1]->slug,
                ]
            ],
            'articlesCount' => 2
        ], $response);
    }

    public function testArticlesListPaginated()
    {
        $articles = FactoryLoader::seed(25, 'Articles', ['author_id' => $this->user->id]);

        $this->sendAuthJsonRequest("/articles", 'GET');
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
        $this->assertArraySubset([
            'articlesCount' => 25
        ], $response);
        $this->assertCount(20, $response['articles'], 'Expected articles to set default limit to 20');

        $this->sendAuthJsonRequest("/articles", 'GET', ['limit' => 10, 'offset' => 5]);
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
        $this->assertArraySubset([
            'articlesCount' => 25
        ], $response);
        $this->assertCount(10, $response['articles'], 'Expected articles to set limit to 10');
        $articles = TableRegistry::get('Articles')->find()
                 ->where(['author_id' => $this->user->id])
                 ->select(['slug'])
                 ->order(['created' => 'desc'])
                 ->all()
                 ->skip(5)
                 ->take(10)
                 ->extract('slug')
                 ->toArray();
        $this->assertEquals(array_values($articles), array_column($response['articles'], 'slug'), 'Expected latest 10 articles with 5 offset');
    }

    public function testArticlesListReturnFollowedUser()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);

        TableRegistry::get('Follows')->follow($this->loggedInUser->id, $this->user->id);
        TableRegistry::get('Articles')->favorite($article->id, $this->loggedInUser->id);

        $this->sendAuthJsonRequest("/articles", 'GET');
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [
                [
                    'slug' => $article->slug,
                    'title' => $article->title,
                    'favorited' => true,
                    'favoritesCount' => 1,
                    'author' => [
                        'username' => $this->user->username,
                        'following' => true,
                    ]
                ]
            ],
            'articlesCount' => 1
        ], $this->getJsonResponse());

        $this->sendJsonRequest("/articles", 'GET');
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [
                [
                    'slug' => $article->slug,
                    'title' => $article->title,
                    'favorited' => false,
                    'favoritesCount' => 1,
                    'author' => [
                        'username' => $this->user->username,
                        'following' => false,
                    ]
                ]
            ],
            'articlesCount' => 1
        ], $this->getJsonResponse());
    }

    public function testSuccessAddArticle()
    {
        $data = [
            'article' => [
                'title' => 'my article title',
                'description' => 'article description',
                'body' => 'article body text',
            ]
        ];

        $this->sendAuthJsonRequest("/articles", 'POST', $data);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'article' => [
                'slug' => 'my-article-title',
                'title' => 'my article title',
                'description' => 'article description',
                'body' => 'article body text',
                'tagList' => [],
                'favorited' => false,
                'favoritesCount' => 0,
                'author' => [
                    'username' => $this->loggedInUser->username,
                    'bio' => $this->loggedInUser->bio,
                    'image' => $this->loggedInUser->image,
                    'following' => false,
                ]
            ]
        ], $this->getJsonResponse());

        $data['article']['tagList'] = ['mytag'];

        $this->sendAuthJsonRequest("/articles", 'POST', $data);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
                'article' => [
                    'title' => 'my article title',
                    'slug' => 'my-article-title-1',
                    'tagList' => ['mytag'],
                    'author' => [
                        'username' => $this->loggedInUser->username,
                    ]
                ]
        ], $this->getJsonResponse());
    }

    public function testValidationErrorsOnAddArticle()
    {
        $data = [
            'article' => [
                'title' => '',
            ]
        ];

        $this->sendAuthJsonRequest("/articles", 'POST', $data);
        $this->assertStatus(422);
        $this->assertArraySubset([
            'errors' => [
                'title' => ['This field cannot be left empty'],
                'description' => ['This field is required'],
                'body' => ['This field is required'],
            ]
        ], $this->getJsonResponse());
    }

    public function testUnauthenticatedErrorOnAddIfNotLoggedIn()
    {
        $this->sendJsonRequest("/articles", 'POST', ['article' => ['body' => 'new text']]);
        $this->assertStatus(401);
    }

    public function testUpdateArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->loggedInUser->id]);

        $data = [
            'article' => [
                'title' => 'new title',
                'description' => 'new description',
                'body' => 'new body message',
            ]
        ];

        $this->sendAuthJsonRequest("/articles/{$article->slug}", 'PUT', $data);
        $this->assertResponseSuccess();
        $this->assertArraySubset([
                'article' => [
                    'title' => 'new title',
                    'slug' => $article->slug,
                    'description' => 'new description',
                    'body' => 'new body message',
                ]
        ], $this->getJsonResponse());
    }

    public function testValidationErrorsOnUpdateArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->loggedInUser->id]);

        $data = [
            'article' => [
                'title' => '',
            ]
        ];

        $this->sendAuthJsonRequest("/articles/{$article->slug}", 'PUT', $data);
        $this->assertStatus(422);
        $this->assertEquals([
            'errors' => [
                'title' => ['This field cannot be left empty'],
                'description' => ['This field is required'],
                'body' => ['This field is required'],
            ]
        ], $this->getJsonResponse());
    }

    public function testUnauthenticatedErrorOnUpdateIfNotLoggedIn()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->sendJsonRequest("/articles/{$article->slug}", 'PUT', ['article' => ['body' => 'new text']]);
        $this->assertStatus(401);
        $record = TableRegistry::get('Articles')->find()->where(['slug' => $article->slug])->first();
        $this->assertEquals($article->body, $record->body);
    }

    public function testUpdateNotExistsArticle()
    {
        $this->sendAuthJsonRequest("/articles/unknown", 'PUT', ['article' => ['body' => 'new text']]);
        $this->assertStatus(404);
    }

    public function testForbiddenErrorIfUpdateOtherUserArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->sendAuthJsonRequest("/articles/{$article->slug}", 'PUT', ['article' => ['body' => 'new text']]);
        $this->assertStatus(403);
        $this->assertEquals(1, TableRegistry::get('Articles')->find()->where(['id' => $article->id])->count());
    }

    public function testUnauthenticatedErrorOnDeleteIfNotLoggedIn()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->sendJsonRequest("/articles/{$article->slug}", 'DELETE', []);
        $this->assertStatus(401);

        $this->assertEquals(1, TableRegistry::get('Articles')->find()->where(['id' => $article->id])->count());
    }

    public function testDeleteExistsArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->loggedInUser->id]);

        $this->sendAuthJsonRequest("/articles/{$article->slug}", 'GET', []);
        $this->assertResponseSuccess();

        $this->sendAuthJsonRequest("/articles/{$article->slug}", 'DELETE', []);
        $this->assertResponseSuccess();

        $this->sendAuthJsonRequest("/articles/{$article->slug}", 'GET', []);
        $this->assertStatus(404);
    }

    public function testDeleteNotExistsArticle()
    {
        $this->sendAuthJsonRequest("/articles/unknown", 'DELETE', []);
        $this->assertStatus(404);
    }

    public function testViewArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);

        $this->sendJsonRequest("/articles/{$article->slug}", 'GET');
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'article' => [
                'title' => $article->title,
                'slug' => $article->slug,
                'description' => $article->description,
                'body' => $article->body,
                'favorited' => false,
                'favoritesCount' => 0,
                'author' => [
                    'username' => $this->user->username,
                    'bio' => $this->user->bio,
                    'image' => $this->user->image,
                    'following' => false,
                ]
            ],
        ], $this->getJsonResponse());
    }

    public function testViewNonExistsArticle()
    {
        $this->sendAuthJsonRequest("/articles/unknown", 'GET');
        $this->assertStatus(404);
    }

    public function testForbiddenErrorIfDeleteOtherUserArticle()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
        $this->sendAuthJsonRequest("/articles/{$article->slug}", 'DELETE');
        $this->assertStatus(403);
        $this->assertEquals(1, TableRegistry::get('Articles')->find()->where(['id' => $article->id])->count());
    }

    public function testSuccessEmptyFeed()
    {
        $this->sendAuthJsonRequest("/articles/feed", 'GET');
        $this->assertResponseSuccess();
        $this->assertArraySubset([
            'articles' => [],
            'articlesCount' => 0
        ], $this->getJsonResponse());
    }

    public function testFeedReturnFollowedUserArticles()
    {
        $articles = FactoryLoader::seed(2, 'Articles', ['author_id' => $this->user->id]);

        TableRegistry::get('Follows')->follow($this->loggedInUser->id, $this->user->id);

        $this->sendAuthJsonRequest("/articles/feed", 'GET');
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
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

    public function testReturnFollowedUserArticlesPaginated()
    {
        $articles = FactoryLoader::seed(25, 'Articles', ['author_id' => $this->user->id]);

        TableRegistry::get('Follows')->follow($this->loggedInUser->id, $this->user->id);

        $this->sendAuthJsonRequest("/articles/feed", 'GET');
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
        $this->assertArraySubset([
            'articlesCount' => 25
        ], $response);
        $this->assertCount(20, $response['articles'], 'Expected feed to set default limit to 20');

        $this->sendAuthJsonRequest("/articles/feed?limit=10&offset=5", 'GET');
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
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
        $this->assertEquals(array_values($articles), array_column($response['articles'], 'slug'), 'Expected latest 10 feed articles with 5 offset');
    }

    public function testReturnFeedWithFavoriteAndFollowingFilled()
    {
        $article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);

        TableRegistry::get('Follows')->follow($this->loggedInUser->id, $this->user->id);

        $this->sendAuthJsonRequest("/articles/feed", 'GET');
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();
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

        $this->sendAuthJsonRequest("/articles/feed", 'GET');
        $this->assertResponseSuccess();
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
        ], $this->getJsonResponse());
    }

    public function testUnauthenticatedErrorOnFeedIfNotLoggedIn()
    {
        $this->sendJsonRequest("/articles/feed", 'GET');
        $this->assertStatus(401);
    }
}
