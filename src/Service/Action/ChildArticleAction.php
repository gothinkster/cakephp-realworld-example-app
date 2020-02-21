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

namespace App\Service\Action;

use CakeDC\Api\Service\Action\CrudAction;
use Cake\ORM\TableRegistry;

abstract class ChildArticleAction extends CrudAction
{

    public $extensions = [];

    /**
     * Initialize an action instance
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $article = TableRegistry::getTableLocator()->get('Articles')->find()->where(['slug' => $this->_parentId])->firstOrFail();
        if ($article) {
            $this->_parentId = $article->id;
        }
    }
}
