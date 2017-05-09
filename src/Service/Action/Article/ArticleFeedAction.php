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

use Cake\ORM\Query;

class ArticleFeedAction extends ArticleIndexBase
{

    /**
     * Returns query object
     *
     * @return Query
     */
    protected function _getQuery()
    {
        $user = $this->Auth->identify();

        return $this->getTable()->find('apiFormat', [
            'feed_by' => $this->Auth->user('id'),
            'currentUser' => $this->Auth->user('id'),
        ]);
    }
}
