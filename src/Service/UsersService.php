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

use App\Service\Action\User\LoginAction;
use App\Service\Action\User\RegisterAction;
use CakeDC\Api\Service\FallbackService;

/**
 * Class AuthService
 *
 * @package CakeDC\Api\Service
 */
class UsersService extends FallbackService
{

    /**
     * Actions classes map.
     *
     * @var array
     */
    protected $_actionsClassMap = [
        'add' => RegisterAction::class,
    ];

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->mapAction('login', LoginAction::class, ['method' => ['POST'], 'mapCors' => true, 'path' => 'login']);
    }
}
