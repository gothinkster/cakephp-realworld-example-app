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

    public $isPublic = true;

    /**
     * Initialize an action instance
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        if ($this->isPublic) {
            $this->Auth->allow($this->getName());
        }
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        return $this->_viewArticle();
    }

    /**
     * Returns current article.
     *
     * @return array
     */
    protected function _viewArticle()
    {
        $record = $this->getTable()
              ->find('apiFormat', [
                  'currentUser' => $this->Auth->user('id')
              ])
              ->where(['Articles.slug' => $this->_id])
              ->firstOrFail();

        return ['article' => $record];
    }

    /**
     * Build condition for get entity method.
     *
     * @param string $primaryKey Record id.
     * @return array
     */
    protected function _buildViewCondition($primaryKey)
    {
        return ['Articles.slug' => $this->_id];
    }
}
