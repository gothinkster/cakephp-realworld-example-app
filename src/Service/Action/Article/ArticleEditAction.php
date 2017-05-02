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

use Cake\Utility\Hash;
use CakeDC\Api\Exception\ValidationException;

class ArticleEditAction extends ArticleViewAction
{

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
        $entity = $this->getTable()->find()->where(['Articles.slug' => $this->_id])->firstOrFail();
        $data = Hash::get($data, 'article');
        unset($data['author']);
        $entity = $this->_patchEntity($entity, $data);
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
        $entity = $this->_patchEntity($this->_getEntity($this->_id), $data);

        if ($this->_save($entity)) {
            return ['article' => $this->_getEntity($this->_id)];
        }
        return false;
    }

    protected function _getEntity($id)
    {
        return $this->getTable()
          ->find('apiFormat')
          ->where(['Articles.slug' => $id])
          ->firstOrFail();
    }
}
