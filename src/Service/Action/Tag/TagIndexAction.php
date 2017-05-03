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

namespace App\Service\Action\Tag;

use CakeDC\Api\Service\Action\CrudAction;

class TagIndexAction extends CrudAction
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
        $this->_finder = 'apiFormat';
        $entities = $this->_getEntities()->map(function($item) {
            return $item->tag;
        })->toArray();

        return [
            'tags' => $entities,
            'tagsCount' => count($entities),
        ];
    }
}
