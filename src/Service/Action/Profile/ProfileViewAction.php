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

use CakeDC\Api\Service\Action\CrudAction;
use Cake\ORM\TableRegistry;

class ProfileViewAction extends CrudAction
{

    public $extensions = [];

    public $isPublic = true;

    /**
     * Initialize an action instance
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_table = TableRegistry::getTableLocator()->get('Users');
        if ($this->isPublic) {
            $this->Auth->allow($this->getName());
        }
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        return $this->_viewProfile();
    }

    /**
     * Builds profile view data.
     *
     * @return array
     */
    protected function _viewProfile()
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
