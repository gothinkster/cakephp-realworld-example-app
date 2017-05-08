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

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Follows Model
 *
 * @method \App\Model\Entity\Follow get($primaryKey, $options = [])
 * @method \App\Model\Entity\Follow newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Follow[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Follow|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Follow patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Follow[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Follow findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FollowsTable extends Table
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

        $this->setTable('follows');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('Followers', [
            'className' => 'Users',
            'foreignKey' => 'follower_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Followables', [
            'className' => 'Users',
            'foreignKey' => 'followable_id',
            'joinType' => 'INNER'
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
            ->requirePresence('followable_id', 'create')
            ->notEmpty('followable_id');

        $validator
            ->requirePresence('follower_id', 'create')
            ->notEmpty('follower_id');

        $validator
            ->requirePresence('blocked', 'create');

        return $validator;
    }

    /**
     * Checks if user following another user.
     *
     * @param string $followerId Follower User id.
     * @param string $followableId Followable User id.
     * @return bool
     */
    public function following($followerId, $followableId)
    {
        return $this->find()
            ->where([
                'follower_id' => $followerId,
                'followable_id' => $followableId,
                'blocked' => false,
            ])
            ->count() > 0;
    }

    /**
     * Makes one user following another user.
     *
     * @param string $followerId Follower User id.
     * @param string $followableId Followable User id.
     * @return bool
     */
    public function follow($followerId, $followableId)
    {
        $exists = $this->find()
           ->where([
               'follower_id' => $followerId,
               'followable_id' => $followableId,
               'blocked' => false,
           ])
           ->first();
        if (!$exists) {
            return $this->save($this->newEntity([
                'follower_id' => $followerId,
                'followable_id' => $followableId,
                'blocked' => false,
            ]));
        }

        return true;
    }

    /**
     * Makes one user not following another user.
     *
     * @param string $followerId Follower User id.
     * @param string $followableId Followable User id.
     * @return bool
     */
    public function unfollow($followerId, $followableId)
    {
        $exists = $this->find()
           ->where([
               'follower_id' => $followerId,
               'followable_id' => $followableId,
               'blocked' => false,
           ])
           ->first();
        if ($exists) {
            return $this->delete($exists);
        }

        return true;
    }
}
