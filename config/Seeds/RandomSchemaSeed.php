<?php
// @codingStandardsIgnoreStart

use Cake\ORM\TableRegistry;
use CakephpFactoryMuffin\FactoryLoader;
use Migrations\AbstractSeed;

/**
 * RandomSchema seed.
 */
class RandomSchemaSeed extends AbstractSeed
{

    /**
     * Total number of users.
     *
     * @var int
     */
    protected $totalUsers = 5;

    /**
     * Total number of tags.
     *
     * @var int
     */
    protected $totalTags = 15;

    /**
     * Percentage of users with articles.
     *
     * @var float Value should be between 0 - 1.0
     */
    protected $userWithArticleRatio = 0.8;

    /**
     * Maximum articles that can be created by a user.
     *
     * @var int
     */
    protected $maxArticlesByUser = 25;

    /**
     * Maximum tags that can be attached to an article.
     *
     * @var int
     */
    protected $maxArticleTags = 4;

    /**
     * Maximum number of comments that can be added to an article.
     *
     * @var int
     */
    protected $maxCommentsInArticle = 15;

    /**
     * Percentage of users with favorites.
     *
     * @var float Value should be between 0 - 1.0
     */
    protected $usersWithFavoritesRatio = 0.75;

    /**
     * Percentage of users with following.
     *
     * @var float Value should be between 0 - 1.0
     */
    protected $usersWithFollowingRatio = 0.75;

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        FactoryLoader::loadAll();
        $tags = FactoryLoader::seed($this->totalTags, 'Tags');
        $users = FactoryLoader::seed($this->totalUsers, 'Users');

        collection($users)->filter(function ($item) {
            return rand(1, $this->totalUsers) < $this->userWithArticleRatio * $this->totalUsers;
        })->each(function ($user) {
            $articles = FactoryLoader::seed(rand(1, $this->maxArticlesByUser), 'Articles', ['author_id' => $user->id]);
            collection($articles)->each(function ($article) {
                $comments = FactoryLoader::seed(rand(1, $this->maxCommentsInArticle), 'Comments', [
                    'article_id' => $article->id,
                ]);
            });
        });

        $articles = TableRegistry::getTableLocator()->get('Articles')->find()->select(['id'])->all();

        $favoritesCount = count($users) * $this->usersWithFavoritesRatio;
        $Favorites = TableRegistry::getTableLocator()->get('Favorites');
        for ($i = 1; $i < $favoritesCount; $i++) {
            $user = $users[$i];
            $articles->shuffle()->take(rand(1, floor($articles->count() / 2)))->each(
                function ($article) use ($Favorites, $user) {
                    $Favorites->save($Favorites->newEntity([
                        'user_id' => $user['id'],
                        'article_id' => $article['id'],
                    ]));
                }
            );
        }

        $followingCount = count($users) * $this->usersWithFollowingRatio;
        $Follows = \Cake\ORM\TableRegistry::get('Follows');
        for ($i = 1; $i < $followingCount; $i++) {
            $user = $users[$i];
            collection($users)
                ->reject(function ($item) use ($user) {
                    return $item->id === $user->id;
                })
                ->shuffle()
                ->take(rand(1, (int)(count($users) - 1) * 0.2))
                ->each(
                    function ($follow) use ($Follows, $user) {
                        $Follows->save($Follows->newEntity([
                            'follower_id' => $user['id'],
                            'followable_id' => $follow['id'],
                        ]));
                    }
                );
        }
    }
}
// @codingStandardsIgnoreEnd
