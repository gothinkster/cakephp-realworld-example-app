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

class ArticleIndexAction extends CrudAction
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
        $options = $this->data();
        $user = $this->Auth->identify();
        if ($user) {
            $options['user_id'] = $user['id'];
        }
        $entities = $this
            ->getTable()
            ->find('apiFormat', $options)
            ->all()
            ->toArray();

        return [
            'articles' => $entities,
            'articlesCount' => count($entities),
        ];
    }
}
