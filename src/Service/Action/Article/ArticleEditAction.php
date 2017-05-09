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
use Cake\Network\Exception\ForbiddenException;
use Cake\Utility\Hash;

class ArticleEditAction extends ArticleViewAction
{

    public $isPublic = false;

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        $data = $this->data();
        if (!array_key_exists('article', $data)) {
            throw new ValidationException(__('Validation failed'), 0, null, ['article root does not exists']);
        }
        $data = Hash::get($data, 'article');
        unset($data['author']);
        $entity = $this->_patchEntity($this->_getEntityBySlug(), $data);

        if ($entity['author_id'] != $this->Auth->user('id')) {
            throw new ForbiddenException();
        }

        $errors = $entity->errors();
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
        $data = Hash::get($this->data(), 'article');
        unset($data['author']);
        $entity = $this->_patchEntity($this->_getEntityBySlug(), $data);

        if ($this->_save($entity)) {
            return ['article' => $this->_getEntity($this->_id)];
        }

        return false;
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

    /**
     * @return mixed
     */
    protected function _getEntityBySlug()
    {
        return $this->getTable()->find()->where(['Articles.slug' => $this->_id])->firstOrFail();
    }
}
