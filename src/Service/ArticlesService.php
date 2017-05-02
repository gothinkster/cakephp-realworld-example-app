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

use App\Service\Action\Article\ArticleFavoriteAction;
use App\Service\Action\Article\ArticleFeedAction;
use App\Service\Action\Article\ArticleUnfavoriteAction;
use CakeDC\Api\Routing\ApiRouter;
use CakeDC\Api\Service\FallbackService;
use Cake\Routing\RouteBuilder;

class ArticlesService extends FallbackService 
{

    /**
     * Actions classes map.
     *
     * @var array
     */
    protected $_actionsClassMap = [
        'index' => '\App\Service\Action\Article\ArticleIndexAction',
        'view' => '\App\Service\Action\Article\ArticleViewAction',
        'add' => '\App\Service\Action\Article\ArticleAddAction',
        'edit' => '\App\Service\Action\Article\ArticleEditAction',
        'delete' => '\App\Service\Action\Article\ArticleDeleteAction',
    ];


    /**
     * @inheritdoc
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->mapAction('feed', ArticleFeedAction::class, ['method' => ['GET'], 'mapCors' => true]);
        $this->mapAction('favorite', ArticleFavoriteAction::class, [
            'method' => ['POST'],
            'mapCors' => true,
            'path' => ':id/favorite'
        ]);
        $this->mapAction('unfavorite', ArticleUnfavoriteAction::class, [
            'method' => ['DELETE'],
            'mapCors' => true,
            'path' => ':id/favorite'
        ]);
        $this->_innerServices = [
            'comments'
        ];
    }

    /**
     * Initialize service level routes
     *
     * @return void
     */
    public function loadRoutes()
    {
        $defaultOptions = $this->routerDefaultOptions();
        $defaultOptions['id'] = '[a-z0-9_-]+';
        ApiRouter::scope('/', $defaultOptions, function (RouteBuilder $routes) use ($defaultOptions) {
            $routes->extensions($this->_routeExtensions);
            $options = $defaultOptions;
            $options['map'] = array_merge($options['map'], [
                'describe' => ['action' => 'describe', 'method' => 'OPTIONS', 'path' => ''],
                'describeId' => ['action' => 'describe', 'method' => 'OPTIONS', 'path' => ':id'],
            ]);
            $routes->resources($this->getName(), $options, function ($routes) {
                if (is_array($this->_routeExtensions)) {
                    $routes->extensions($this->_routeExtensions);
                    $options = [];
                    $routes->resources('Comments', $options);
                }
            });
        });
    }

}