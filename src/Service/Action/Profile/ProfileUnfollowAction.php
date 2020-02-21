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

class ProfileUnfollowAction extends ProfileViewAction
{

    public $isPublic = false;

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
    }

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
        $record = $this->getTable()
           ->find()
           ->where(['Users.username' => $this->_id])
           ->firstOrFail();

        if ($record) {
            $Follows = TableRegistry::getTableLocator()->get('Follows');
            $current = $Follows->find()->where([
                'follower_id' => $this->Auth->user('id'),
                'followable_id' => $record['id'],
            ])->first();
            if ($current) {
                $Follows->delete($current);
            }
        }
        TableRegistry::getTableLocator()->get('Follows')->unfollow($this->Auth->user('id'), $record['id']);

        return $this->_viewProfile();
    }
}
