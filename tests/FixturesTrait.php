<?php
/**
 * Copyright 2016 - 2017, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2016 - 2017, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace App\Test;

/**
 * Class FixturesTrait
 *
 * @package CakeDC\Api\Test
 */
trait FixturesTrait
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Articles',
        'app.Comments',
        'app.Favorites',
        'app.Follows',
        'app.Users',
        'app.SocialAccounts',
        'app.Tagged',
        'app.Tags',
    ];
}
