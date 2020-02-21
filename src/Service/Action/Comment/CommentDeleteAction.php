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
use Cake\Http\Exception\ForbiddenException;

class CommentDeleteAction extends ChildArticleAction
{

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        $record = $this->getTable()->find()->where(['id' => $this->_id])->firstOrFail();

        if ($record['author_id'] != $this->Auth->user('id')) {
            throw new ForbiddenException();
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
        $record = $this->_getEntity($this->_id);
        if ($record) {
            $result = $this->getTable()->delete($record);
        }

        return !empty($result);
    }
}
