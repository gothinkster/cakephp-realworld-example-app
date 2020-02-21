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

namespace App\Service\Action\Article;

use CakeDC\Api\Exception\ValidationException;

class ArticleAddAction extends ArticleEditAction
{

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        $validator = $this->getTable()->getValidator();
        $data = $this->getData();
        if (!array_key_exists('article', $data)) {
            throw new ValidationException(__('Validation failed'), 0, null, ['article root does not exists']);
        }
        $errors = $validator->errors($data['article']);
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
        $data = $this->_articleData();
        $data['author_id'] = $this->Auth->user('id');
        $entity = $this->_patchEntity($entity, $data);

        $result = $this->_save($entity);
        if ($result) {
            return ['article' => $this->_getEntity($result['slug'])];
        }

        return null;
    }
}
