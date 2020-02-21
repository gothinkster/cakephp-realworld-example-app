<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;
use CakephpFactoryMuffin\FactoryLoader;
use Cake\ORM\TableRegistry;

class CommentsServiceTest extends IntegrationTestCase
{
    use FixturesTrait;

    protected $article;

    public function setUp()
    {
        parent::setUp();

        $this->article = FactoryLoader::create('Articles', ['author_id' => $this->user->id]);
    }

    public function testSuccessAddComment()
    {
        $data = [
            'comment' => [
                'body' => 'This is a comment'
            ]
        ];

        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/comments", 'POST', $data);
        $this->assertResponseSuccess();

        $this->assertArraySubset([
            'comment' => [
                'body' => 'This is a comment',
                'author' => [
                    'username' => $this->loggedInUser->username
                ],
            ]
        ], $this->getJsonResponse());
    }

    public function testRemoveExistsComment()
    {
        $comment = $this->_generateComment($this->loggedInUser->id);
        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/comments/{$comment->id}", 'DELETE');
        $this->assertResponseSuccess();

        $this->assertEquals(0, TableRegistry::get('Comments')->find()->where(['article_id' => $this->article->id])->count());
    }

    public function testReturnAllArticleComments()
    {
        $comments = FactoryLoader::seed(2, 'Comments', [
            'author_id' => $this->user->id,
            'article_id' => $this->article->id,
        ]);
        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/comments", 'GET');
        $this->assertResponseSuccess();
        $response = $this->getJsonResponse();

        $comments = collection($comments)->sortBy(function ($comment) {
            return $comment->created->format('Y-m-d H:i:s');
        }, SORT_DESC, SORT_STRING)->toList();

        $this->assertArraySubset([
            'comments' => [
                [
                    'id' => $comments[0]['id'],
                    'body' => $comments[0]['body'],
                    'author' => [
                        'username' => $this->user->username
                    ],
                ],
                [
                    'id' => $comments[1]['id'],
                    'body' => $comments[1]['body'],
                    'author' => [
                        'username' => $this->user->username
                    ],
                ],
            ]
        ], $response);
    }

    public function testUnauthenticatedErrorOnDeleteIfNotLoggedIn()
    {
        $comment = $this->_generateComment($this->user->id);
        $this->sendJsonRequest("/articles/{$this->article->slug}/comments/{$comment->id}", 'DELETE');
        $this->assertStatus(401);

        $this->assertEquals(1, TableRegistry::get('Comments')->find()->where(['article_id' => $this->article->id])->count());
    }

    public function testDeleteNotExistsComment()
    {
        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/comments/b66bfa63-460f-4652-add3-c039b0620b4e", 'DELETE');
        $this->assertStatus(404);

        $this->sendAuthJsonRequest("/articles/unknown/comments/b66bfa63-460f-4652-add3-c039b0620b4e", 'DELETE');
        $this->assertStatus(404);
    }

    public function testListCommentsForNonExistsArticle()
    {
        $this->sendAuthJsonRequest("/articles/unknown/comments", 'GET');
        $this->assertStatus(404);
    }

    public function testForbiddenErrorIfDeleteOtherUserComment()
    {
        $comment = $this->_generateComment($this->user->id);
        $this->sendAuthJsonRequest("/articles/{$this->article->slug}/comments/{$comment->id}", 'DELETE');
        $this->assertStatus(403);
        $this->assertEquals(1, TableRegistry::get('Comments')->find()->where(['article_id' => $this->article->id])->count());
    }

    /**
     * @param $userId
     * @return \Cake\Datasource\EntityInterface
     */
    protected function _generateComment($userId)
    {
        $comment = FactoryLoader::create('Comments', [
            'author_id' => $userId,
            'article_id' => $this->article->id,
        ]);

        return $comment;
    }
}
