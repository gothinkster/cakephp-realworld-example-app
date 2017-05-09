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

use App\Service\Action\User\UserEditAction;
use App\Service\Action\User\UserViewAction;
use CakeDC\Api\Service\FallbackService;

class UserService extends FallbackService
{

    /**
     * Actions classes map.
     *
     * @var array
     */
    protected $_actionsClassMap = [
        'index' => UserViewAction::class
    ];

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->mapAction('update', UserEditAction::class, [
            'method' => ['PUT', 'PATCH'],
            'path' => '',
            'mapCors' => true
        ]);
        $this->_table = 'users';
    }
}
