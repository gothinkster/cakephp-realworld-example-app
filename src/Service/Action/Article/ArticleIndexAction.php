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

namespace App\Service\Action\Article;

use Cake\ORM\Query;

class ArticleIndexAction extends ArticleIndexBase
{

    /**
     * Initialize an action instance
     *
     * @param array $config Configuration options passed to the constructor
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->Auth->allow($this->getName());
    }

    /**
     * Returns query object
     *
     * @return Query
     */
    protected function _getQuery()
    {
        $options = $this->getData();
        $user = $this->Auth->identify();
        if ($user) {
            $options['currentUser'] = $user['id'];
        }

        return $this->getTable()->find('apiFormat', $options);
    }
}
