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

class ArticleFeedAction extends CrudAction
{

    public $extensions = [];

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $entities = $this->getTable()
            ->find('apiFormat', [
                'feed_by' => $this->Auth->user('id')
            ])
            ->all()
            ->toArray();

		return [
			'articles' => $entities,
			'articlesCount' => count($entities),
		];
    }
}
