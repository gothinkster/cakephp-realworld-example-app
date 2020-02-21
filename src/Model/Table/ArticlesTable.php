<?php
/**
 * Copyright 2017, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2017, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace App\Model\Table;

use App\Utility\Formatter;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Articles Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Authors
 * @property \Cake\ORM\Association\HasMany $Favorites
 * @property \Cake\ORM\Association\HasMany $Comments
 * @property \Cake\ORM\Association\BelongsToMany $Tags
 *
 * @method \App\Model\Entity\Article get($primaryKey, $options = [])
 * @method \App\Model\Entity\Article newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Article[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Article|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Article patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Article[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Article findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ArticlesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('articles');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Slug.Slug');
        $this->addBehavior('Muffin/Tags.Tag', [
            'delimiter' => ' ',
            'tagsAssoc' => [
                'className' => 'Tags',
                'propertyName' => 'tagList',
            ],
        ]);

        $this->belongsTo('Authors', [
            'className' => 'Users',
            'foreignKey' => 'author_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'article_id',
            'dependent' => true,
        ]);
        $this->belongsTo('Favorites', [
            'foreignKey' => 'article_id',
            'dependent' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmpty('title');

        $validator
            ->allowEmpty('slug');

        $validator
            ->requirePresence('description')
            ->allowEmpty('description');

        $validator
            ->requirePresence('body')
            ->allowEmpty('body');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['author_id'], 'Authors'));

        return $rules;
    }

    /**
     * Api finder and endpoint formatter.
     * Method additionally apply search filters to query.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Query options.
     * @return \Cake\ORM\Query The query builder.
     */
    public function findApiFormat(Query $query, array $options)
    {
        if (array_key_exists('author', $options)) {
            $author = $this->Authors->find()->where(['username' => $options['author']])->first();
            $authorId = null;
            if ($author) {
                $authorId = $author->id;
            }
            $query->where(['author_id' => $authorId]);
        }
        if (array_key_exists('favorited', $options)) {
            $author = $this->Authors->find()->where(['username' => $options['favorited']])->first();
            $authorId = null;
            if ($author) {
                $authorId = $author->id;
            }
            $query
                ->innerJoin(['Favorites2' => 'favorites'], ['Articles.id = Favorites2.article_id'])
                ->where(['Favorites2.user_id' => $authorId]);
        }
        if (array_key_exists('tag', $options)) {
            $query
                ->innerJoin(['Tagged' => 'tags_tagged'], ['Articles.id = Tagged.fk_id'])
                ->innerJoin(['Tags' => 'tags_tags'], ['Tags.id = Tagged.tag_id'])
                ->where(['Tags.label' => $options['tag']])
                ->select('Tags.label');
        }
        if (!empty($options['currentUser'])) {
            $query
                ->leftJoin(['Favorites' => 'favorites'], [
                    'Articles.id = Favorites.article_id',
                    [
                        'OR' => [
                            ['Favorites.user_id' => $options['currentUser']],
                            ['Favorites.user_id IS' => null],
                        ]
                    ]
                ])
                ->select('Favorites.id');
        }
        if (!empty($options['feed_by'])) {
            $query
                ->leftJoin(['Follows' => 'follows'], ['Articles.author_id = Follows.followable_id'])
                ->where(['Follows.follower_id' => $options['feed_by']]);
        }

        return $query
            ->contain(['Tags'])
            ->select(['id', 'title', 'slug', 'description', 'body', 'created', 'modified', 'author_id', 'favorites_count'])
            ->order(['Articles.created' => 'desc'])
            ->formatResults(function ($results) use ($options) {
                return $results->map(function ($row) use ($options) {
                    if ($row === null) {
                        return $row;
                    }
                    $row = Formatter::dateFormat($row);
                    $row['author'] = TableRegistry::getTableLocator()->get('Users')->getFormatted($row['author_id'], $options);
                    if ($row['tagList']) {
                        $tags = collection($row['tagList'])
                            ->map(function ($tag) {
                                return $tag['label'];
                            })
                            ->toArray();
                        unset($row['tagList']);
                        $row['tagList'] = $tags;
                    } else {
                        $row['tagList'] = [];
                    }
                    if ($row['Favorites']) {
                        $row['favorited'] = !empty($row['Favorites']['id']);
                    } else {
                        $row['favorited'] = 0;
                    }
                    $row['favoritesCount'] = $row['favorites_count'];
                    unset($row['id']);
                    unset($row['author_id']);
                    unset($row['favorites_count']);
                    unset($row['Favorites']);

                    return $row;
                });
            });
    }

    /**
     * Favorite article by user.
     *
     * @param string $id An article id.
     * @param string $userId A user id
     * @return bool|EntityInterface
     */
    public function favorite($id, $userId)
    {
        $exists = $this->Favorites->find()
           ->where([
               'user_id' => $userId,
               'article_id' => $id,
           ])
           ->first();
        if (!$exists) {
            $entity = $this->Favorites->newEntity([
                'user_id' => $userId,
                'article_id' => $id,
            ]);

            return $this->Favorites->save($entity);
        }

        return $exists;
    }

    /**
     * Favorite article by user.
     *
     * @param string $id An article id.
     * @param string $userId A user id
     * @return bool|EntityInterface
     */
    public function unfavorite($id, $userId)
    {
        $entity = $this->Favorites->find()
           ->where([
               'user_id' => $userId,
               'article_id' => $id,
           ])
           ->first();
        if ($entity) {
            return $this->Favorites->delete($entity);
        }

        return true;
    }
}
