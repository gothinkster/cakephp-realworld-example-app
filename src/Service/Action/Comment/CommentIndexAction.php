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

namespace App\Service\Action\Comment;

use App\Service\Action\ChildArticleAction;
use Cake\Utility\Hash;

class CommentIndexAction extends ChildArticleAction
{

    public $extensions = [
        'AppPaginate'
    ];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->Auth->allow($this->getName());
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $entities = $this->_getEntities()->toArray();

        $pagination = $this->service()->getResult()->payload('pagination');
        return [
            'comments' => $entities,
            'commentsCount' => Hash::get($pagination, 'count'),
        ];
    }

    /**
     * Builds entities list
     *
     * @return \Cake\Collection\Collection
     */
    protected function _getEntities()
    {
        $options = $this->data();
        $user = $this->Auth->identify();
        if ($user) {
            $options['currentUser'] = $user['id'];
        }
        $query = $this->getTable()->find('apiFormat', $options);

        $event = $this->dispatchEvent('Action.Crud.onFindEntities', compact('query'));
        if ($event->result) {
            $query = $event->result;
        }
        $records = $query->all();
        $this->dispatchEvent('Action.Crud.afterFindEntities', compact('query', 'records'));

        return $records;
    }

}
