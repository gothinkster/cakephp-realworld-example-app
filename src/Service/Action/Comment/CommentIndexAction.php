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
use Cake\ORM\TableRegistry;
use CakeDC\Api\Service\Action\CrudAction;

class CommentIndexAction extends ChildArticleAction
{

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $entities = $this->_getEntities()->toArray();
		
		return [
			'comments' => $entities,
			'commentsCount' => count($entities),
		];
    }
}
