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

namespace App\PasswordHasher;

use Authentication\PasswordHasher\AbstractPasswordHasher;
use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Utility\Security;

class PlainPasswordHasher extends AbstractPasswordHasher
{

    /**
     * Default config for this object.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Generates password hash.
     *
     * @param string $password Plain text password to hash.
     * @return string Password hash
     */
    public function hash($password)
    {
        return $password;
    }

    /**
     * Check hash. Generate hash for user provided password and check against existing hash.
     *
     * @param string $password Plain text password to hash.
     * @param string $hashedPassword Existing hashed password.
     * @return bool True if hashes match else false.
     */
    public function check($password, $hashedPassword)
    {
        return $hashedPassword === $this->hash($password);
    }
}
