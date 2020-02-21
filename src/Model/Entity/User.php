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

namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * User Entity
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $token
 * @property \Cake\I18n\Time $token_expires
 * @property string $api_token
 * @property \Cake\I18n\Time $activation_date
 * @property \Cake\I18n\Time $tos_date
 * @property bool $active
 * @property bool $is_superuser
 * @property string $role
 * @property string $secret
 * @property bool $secret_verified
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 *
 * @property \App\Model\Entity\SocialAccount[] $social_accounts
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
        'token'
    ];

    /**
     * @param string $password password that will be set.
     * @return bool|string
     */
    protected function _setPassword($password)
    {
        return $this->hashPassword($password);
    }

    /**
     * Hash a password using the configured password hasher,
     * use DefaultPasswordHasher if no one was configured
     *
     * @param string $password password to be hashed
     * @return mixed
     */
    public function hashPassword($password)
    {
        $PasswordHasher = $this->getPasswordHasher();

        return $PasswordHasher->hash($password);
    }

    /**
     * Return the configured Password Hasher
     *
     * @return mixed
     */
    public function getPasswordHasher()
    {
        $passwordHasher = Configure::read('Users.passwordHasher');
        if (!class_exists($passwordHasher)) {
            $passwordHasher = '\Cake\Auth\DefaultPasswordHasher';
        }

        return new $passwordHasher;
    }

    /**
     * Builds jwt token.
     *
     * @return string
     */
// @codingStandardsIgnoreStart
    public function _getToken()
    {
        $content = [
            'id' => $this->get('id'),
            'sub' => $this->get('id'),
            'exp' => time() + 60 * DAY
        ];
        return JWT::encode($content, Security::getSalt());
    }
// @codingStandardsIgnoreEnd
}
