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

namespace App\Service\Action\Profile;

use Cake\ORM\TableRegistry;
use CakeDC\Api\Service\Action\CrudAction;

class ProfileViewAction extends CrudAction
{

    public $extensions = [];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_table = TableRegistry::get('Users');
        $this->Auth->allow($this->getName());
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $user = $this->Auth->identify();
        if ($user) {
            $options = [
                'currentUser' => $user['id']
            ];
        } else {
            $options = [];
        }
        $record = $this->getTable()
           ->find('apiFormat', $options)
           ->where(['Users.username' => $this->_id])
           ->firstOrFail();

        return ['profile' => $record];
    }
}
