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

use CakeDC\Api\Service\Action\CrudAction;

class ArticleViewAction extends CrudAction
{

    public $extensions = [];

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
        $record = $this->getTable()
              ->find('apiFormat', [
                  'user_id' => $this->Auth->user('id')
              ])
              ->where(['Articles.slug' => $this->_id])
              ->firstOrFail();

		return ['article' => $record];
    }
}
