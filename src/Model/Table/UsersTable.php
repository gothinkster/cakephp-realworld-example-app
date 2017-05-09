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
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $SocialAccounts
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('SocialAccounts', [
            'foreignKey' => 'user_id'
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
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('email', 'create')
            ->email('email', false, 'This field must be a valid email address.')
            ->notEmpty('email');

        $validator
            ->requirePresence('password', 'create')
            ->minLength('password', 6, 'Password must be at least 6 characters.')
            ->notEmpty('password');

        $validator
            ->allowEmpty('first_name');

        $validator
            ->allowEmpty('last_name');

        $validator
            ->allowEmpty('token');

        $validator
            ->dateTime('token_expires')
            ->allowEmpty('token_expires');

        $validator
            ->allowEmpty('api_token');

        $validator
            ->dateTime('activation_date')
            ->allowEmpty('activation_date');

        $validator
            ->dateTime('tos_date')
            ->allowEmpty('tos_date');

        $validator
            ->allowEmpty('role');

        $validator
            ->allowEmpty('secret');

        $validator
            ->url('image', __('Invalid url'))
            ->allowEmpty('image');

        $validator
            ->boolean('secret_verified')
            ->allowEmpty('secret_verified');

        return $validator;
    }

    /**
     * Wrapper for all validation rules for register
     * @param Validator $validator Cake validator object.
     *
     * @return Validator
     */
    public function validationRegister(Validator $validator)
    {
        $validator = $this->validationDefault($validator);

        $validator
            ->boolean('active')
            ->requirePresence('active', 'create')
            ->notEmpty('active');

        $validator
            ->boolean('is_superuser')
            ->requirePresence('is_superuser', 'create')
            ->notEmpty('is_superuser');

        $validator
            ->requirePresence('username')
            ->alphaNumeric('username', 'Username may only contain letters and numbers.')
            ->notEmpty('username');

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
        $rules->add($rules->isUnique(['username'], __('Username has already been taken.')));
        $rules->add($rules->isUnique(['email'], __('Email has already been taken.')));

        return $rules;
    }

    /**
     * Api finder and endpoint formatter.
     *
     * @param \Cake\ORM\Query $query Query object.
     * @param array $options Query options.
     * @return \Cake\ORM\Query The query builder.
     */
    public function findApiFormat(Query $query, array $options)
    {
        if (!empty($options['currentUser'])) {
            $query
                ->leftJoin(['Follows' => 'follows'], [
                    'Users.id = Follows.followable_id',
                    [
                        'OR' => [
                            ['Follows.follower_id' => $options['currentUser']],
                            ['Follows.follower_id IS' => null],
                        ]
                    ]
                ])
                ->select('Follows.id');
        }

        return $query
            ->select($this->getSchema()->columns())
            ->formatResults(function ($results) use ($options) {
                return $results->map(function ($row) use ($options) {
                    if ($row === null) {
                        return $row;
                    }

                    if ($row['Follows']) {
                        $row['following'] = !empty($row['Follows']['id']);
                    } else {
                        $row['following'] = false;
                    }

                    if (!empty($options['includeToken'])) {
                        return [
                            'username' => Hash::get($row, 'username'),
                            'email' => Hash::get($row, 'email'),
                            'bio' => Hash::get($row, 'bio'),
                            'image' => Hash::get($row, 'image'),
                            'token' => Hash::get($row, 'token')

                        ];
                    } else {
                        return [
                            'username' => Hash::get($row, 'username'),
                            'bio' => Hash::get($row, 'bio'),
                            'image' => Hash::get($row, 'image'),
                            'following' => $row['following'],
                        ];
                    }
                });
            });
    }

    /**
     * Find user by id and return it with token.
     *
     * @param string $userId User id.
     * @return \Cake\ORM\Query The query builder
     */
    public function loginFormat($userId)
    {
        if ($userId === null) {
            return null;
        }

        return $this->find('apiFormat', ['includeToken' => true])->where(['id' => $userId])->first();
    }

    /**
     * Get formatted user response including following info.
     *
     * @param string $id User id.
     * @param array $options Query options.
     * @return mixed
     */
    public function getFormatted($id, $options)
    {
        return $this->find('apiFormat', $options)
          ->where([
              'Users.id' => $id
          ])
          ->first();
    }
}
