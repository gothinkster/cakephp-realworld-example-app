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

use Cake\ORM\TableRegistry;
use CakeDC\Api\Service\Action\CrudAction;

class ArticleUnfavoriteAction extends CrudAction
{

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

        if ($article) {
            $current = $this->getTable()->Favorites->find()
               ->where([
                   'user_id' => $this->Auth->user('id'),
                   'article_id' => $article['id'],
               ])
               ->first();
            if ($current) {
                $result = $this->getTable()->Favorites->delete($current);
            }
        }
        return !empty($result);
    }

    protected function _buildViewCondition($id) {
        return ['Articles.slug' => $this->_id];
    }
}
