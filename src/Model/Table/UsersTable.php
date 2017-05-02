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
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Cake\Validation\Validator;
use Firebase\JWT\JWT;

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
            ->requirePresence('username', 'create')
            ->notEmpty('username');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->requirePresence('password', 'create')
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
            ->boolean('active')
            ->requirePresence('active', 'create')
            ->notEmpty('active');

        $validator
            ->boolean('is_superuser')
            ->requirePresence('is_superuser', 'create')
            ->notEmpty('is_superuser');

        $validator
            ->allowEmpty('role');

        $validator
            ->allowEmpty('secret');

        $validator
            ->boolean('secret_verified')
            ->allowEmpty('secret_verified');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }

    /**
     * Api finder and endpoint formatter.
     *
     * @param \Cake\ORM\Query  $query
     * @param array $options
     * @return \Cake\ORM\Query The query builder.
     */
    public function findApiFormat(Query $query, array $options)
    {
        if (!empty($options['currentUser'])) {
            $query
                ->leftJoin(['Follows' => 'follows'], ['Users.id = Follows.followable_id'])
                ->where(['OR' => [
                    ['Follows.follower_id' => $options['currentUser']],
                    ['Follows.follower_id IS' => null],
                ]])
                ->select('Follows.id');
        }

        return $query
            ->select($this->getSchema()->columns())
            ->formatResults(function ($results) {
                return $results->map(function ($row) {
                    return $this->rowFormatter($row);
                });
            });
    }

    public function apiFormatter() {
        return function ($results) {
            return $results->map(function ($row) {
                return $this->rowFormatter($row);
            });
        };
    }

    public function rowFormatter($row) {
        if ($row === null) {
            return $row;
        }
        $row = Formatter::dateFormat($row);

        if ($row['Follows']) {
            $row['following'] = !empty($row['Follows']['id']);
        } else {
            $row['following'] = false;
        }

        return [
            'id' => Hash::get($row, 'id'),
            'username' => Hash::get($row, 'username'),
            'email' => Hash::get($row, 'email'),
            'bio' => Hash::get($row, 'bio'),
            'image' => Hash::get($row, 'image'),
            'following' => $row['following'],
            'createdAt' => Hash::get($row, 'createdAt'),
            'updatedAt' => Hash::get($row, 'updatedAt'),
        ];
    }

    /**
     * Find userby id and return it with token.
     *
     * @param string $id
     * @return \Cake\ORM\Query The query builder
     */
    public function loginFormat($userId)
    {
        if ($userId === null) {
            return null;
        }
        $user = $this->find('apiFormat')->where(['id' => $userId])->first();
        $user['token'] = $this->_getToken($user);

        return $user;
    }

    /**
     * Builds jwt token.
     *
     * @param EntityInterface $user User entity .
     * @return string
     */
    protected function _getToken($user)
    {
        $content = [
            'id' => $user['id'],
            'exp' => time() + 60 * DAY
        ];
        return JWT::encode($content, Security::salt());
    }
}
