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

use App\Service\Action\Profile\ProfileFollowAction;
use App\Service\Action\Profile\ProfileUnfollowAction;
use CakeDC\Api\Routing\ApiRouter;
use CakeDC\Api\Service\FallbackService;
use Cake\Routing\RouteBuilder;

class ProfilesService extends FallbackService
{

    /**
     * Actions classes map.
     *
     * @var array
     */
    protected $_actionsClassMap = [
        'view' => '\App\Service\Action\Profile\ProfileViewAction',
    ];

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->mapAction('follow', ProfileFollowAction::class, ['method' => ['POST'], 'mapCors' => true, 'path' => ':id/follow']);
        $this->mapAction('unfollow', ProfileUnfollowAction::class, ['method' => ['DELETE'], 'mapCors' => true, 'path' => ':id/follow']);
    }

    /**
     * Initialize service level routes
     *
     * @return void
     */
    public function loadRoutes()
    {
        $defaultOptions = $this->routerDefaultOptions();
        $defaultOptions['id'] = '[A-Za-z0-9_-]+';
        ApiRouter::scope('/', $defaultOptions, function (RouteBuilder $routes) use ($defaultOptions) {
            $routes->setExtensions($this->_routeExtensions);
            $routes->resources($this->getName(), $defaultOptions);
        });
    }
}
