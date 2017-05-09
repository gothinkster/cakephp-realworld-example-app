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
use CakeDC\Api\Service\Action\CrudAction;
use Cake\Utility\Hash;

class ArticleAddAction extends CrudAction
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
        $data = Hash::get($this->data(), 'article');
        $data['author_id'] = $this->Auth->user('id');
        $entity = $this->_patchEntity($entity, $data);

        $result = $this->_save($entity);
        if ($result) {
            return ['article' => $this->_getEntity($result['slug'])];
        }

        return null;
    }

    /**
     * Returns single entity by id.
     *
     * @param mixed $primaryKey Primary key.
     * @return \Cake\Collection\Collection
     */
    protected function _getEntity($primaryKey)
    {
        return $this->getTable()
          ->find('apiFormat')
          ->where(['Articles.slug' => $primaryKey])
          ->firstOrFail();
    }
}
