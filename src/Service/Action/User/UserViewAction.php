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

use CakeDC\Api\Service\Action\Action;
use CakeDC\Users\Exception\UserNotFoundException;
use Cake\ORM\TableRegistry;

/**
 * Class LoginAction
 *
 * @package CakeDC\Api\Service\Action
 */
class UserViewAction extends Action
{

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        return true;
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $user = TableRegistry::getTableLocator()->get('Users')->loginFormat($this->Auth->user('id'));
        if (empty($user)) {
            throw new UserNotFoundException(__d('CakeDC/Api', 'User not found'));
        } else {
            return ['user' => $user];
        }
    }
}
