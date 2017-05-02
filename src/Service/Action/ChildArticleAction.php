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

use Cake\ORM\TableRegistry;
use CakeDC\Api\Service\Action\CrudAction;

abstract class ChildArticleAction extends CrudAction
{

    public $extensions = [];

    public function initialize(array $config)
    {
        parent::initialize($config);
        $article = TableRegistry::get('Articles')->find()->where(['slug' => $this->_parentId])->first();
        if ($article) {
            $this->_parentId = $article->id;
        }
    }

}
