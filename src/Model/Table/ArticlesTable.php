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
            ->allowEmpty('description');

        $validator
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
     * beforeSave, starts a time before a save is initiated.
     *
     * @param \Cake\Event\Event $event The afterSave event that was fired.
     * @param \Cake\ORM\Entity $entity The entity that was saved.
     * @param \ArrayObject $options Options.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity['tagList']) {

        }
    }

    /**
     * Api finder and endpoint formatter.
     * Method additionally apply search filters to query.
     *
     * @param \Cake\ORM\Query  $query
     * @param array $options
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
        if (!empty($options['user_id'])) {
            $query
                ->leftJoin(['Favorites' => 'favorites'], [
                    'Articles.id = Favorites.article_id',
                    [
                        'OR' => [
                            ['Favorites.user_id' => $options['user_id']],
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
            ->contain(['Authors', 'Tags'])
            ->select($this->getSchema()->columns())
            ->select(collection($this->Authors->getSchema()->columns())->map(function ($i) {return "Authors.$i";})->toArray())
            ->order(['Articles.created' => 'desc'])
            ->formatResults(function ($results) use ($options) {
                return $results->map(function ($row) {
                    if ($row === null) {
                        return $row;
                    }
                    $row = Formatter::dateFormat($row);
					if ($row['author']) {
                        $row['author'] = $this->Authors->rowFormatter($row['author']);
                    }
                    if ($row['tags']) {
					    $tags = collection($row['tags'])->map(function ($tag) {
					        return $tag['tag'];
                        })->toArray();
					    unset($row['tags']);
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

					return $row;
				});
            });
    }
}
