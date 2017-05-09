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

namespace App\Service;

use App\Service\Action\Article\ArticleIndexAction;
use CakeDC\Api\Routing\ApiRouter;
use CakeDC\Api\Service\FallbackService;
use Cake\ORM\TableRegistry;
use Cake\Routing\RouteBuilder;
use Cake\Utility\Inflector;

class TagsService extends FallbackService
{

    /**
     * Actions classes map.
     *
     * @var array
     */
    protected $_actionsClassMap = [
        'index' => '\App\Service\Action\Tag\TagIndexAction',
    ];
}
