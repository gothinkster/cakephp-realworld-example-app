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

class ProfileFollowAction extends CrudAction
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->_table = TableRegistry::get('Users');
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
               ->find('apiFormat')
               ->where(['Users.username' => $this->_id])
               ->firstOrFail();

        if ($record) {
            $Follows = TableRegistry::get('Follows');
            $current = $Follows->find()
               ->where([
                   'follower_id' => $this->Auth->user('id'),
                   'followable_id' => $record['id'],
                   'blocked' => false,
               ])
               ->first();
            if (!$current) {
                $entity = $Follows->newEntity([
                    'follower_id' => $this->Auth->user('id'),
                    'followable_id' => $record['id'],
                    'blocked' => false,
                ]);
                $result = $Follows->save($entity);
            }
        }
        return !empty($result);
    }
}
