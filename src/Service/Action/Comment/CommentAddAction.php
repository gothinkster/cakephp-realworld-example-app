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
use App\Utility\Formatter;
use Cake\Utility\Hash;
use CakeDC\Api\Exception\ValidationException;
use CakeDC\Api\Service\Action\CrudAction;

class CommentAddAction extends ChildArticleAction
{

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        $validator = $this->getTable()->validator();
        $data = $this->data();
        if (!array_key_exists('comment', $data)) {
            throw new ValidationException(__('Validation failed'), 0, null, ['comment root does not exists']);
        }
        $errors = $validator->errors($data['comment']);
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
        $entity = $this->_newEntity();
        $data = Hash::get($this->data(), 'comment');
        $data['author_id'] = $this->Auth->user('id');
        $data['article_id'] = $this->_parentId;
        $entity = $this->_patchEntity($entity, $data);

        $result = $this->_save($entity);
        if ($result) {
            $comment = $this->getTable()->find('apiFormat')->where(['Comments.id' => $result->id])->first();
            return ['comment' => $comment];
        }

        return null;
    }
}
