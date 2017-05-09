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

use CakeDC\Api\Service\Action\Action;

class DummyAction extends Action
{

    /**
     * Apply validation process.
     *
     * @return bool
     */
    public function validates()
    {
        return true;
    }

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        return [];
    }
}
