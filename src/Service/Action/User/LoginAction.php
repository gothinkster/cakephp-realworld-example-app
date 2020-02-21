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

namespace App\Service\Action\User;

use CakeDC\Api\Exception\ValidationException;
use CakeDC\Api\Service\Action\Action;
use CakeDC\Users\Controller\Component\UsersAuthComponent;
use CakeDC\Users\Controller\Traits\LoginTrait;
use CakeDC\Users\Exception\UserNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Class LoginAction
 *
 * @package CakeDC\Api\Service\Action
 */
class LoginAction extends Action
{

    use LoginTrait;

    protected $_identifiedField = 'email';

    protected $_passwordField = 'password';

    /**
     * Initialize an action instance
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->Auth->allow($this->getName());
    }

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        $validator = TableRegistry::getTableLocator()->get('Users')->getValidator();
        $data = $this->getData();
        if (!array_key_exists('user', $data)) {
            throw new ValidationException(__('Validation failed'), 0, null, ['user root does not exists']);
        }
        $data = Hash::get($this->getData(), 'user');
        $errors = $validator->errors($data);
        if (!empty($errors)) {
            throw new ValidationException(__('Validation failed'), 0, null, $errors);
        }

        return true;
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $user = $this->Auth->identify();
        if (!empty($user)) {
            $this->Auth->setUser($user);
            $this->dispatchEvent(UsersAuthComponent::EVENT_AFTER_LOGIN, ['user' => $user]);
            $user = TableRegistry::getTableLocator()->get('Users')->loginFormat($user['id']);
        } else {
            throw new UserNotFoundException(__d('CakeDC/Api', 'User not found'));
        }

        return ['user' => $user];
    }
}
