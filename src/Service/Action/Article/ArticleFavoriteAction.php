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

class ArticleFavoriteAction extends ArticleViewAction
{

    public $isPublic = false;

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        return true;
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $article = $this->_getEntity($this->_id);
        $this->getTable()->favorite($article['id'], $this->Auth->user('id'));

        return $this->_viewArticle();
    }
}
