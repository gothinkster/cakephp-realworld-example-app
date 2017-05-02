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

class ArticleDeleteAction extends ArticleViewAction
{

    /**
     * Execute action.
     *
     * @return mixed
     */
    public function execute()
    {
        $record = $this->_getEntity($this->_id);
        if ($record) {
            $result = $this->getTable()->delete($record);
        }

        return !empty($result);
    }

    protected function _getEntity($id)
    {
        return $this->getTable()
          ->find('apiFormat')
          ->where(['Articles.slug' => $id])
          ->firstOrFail();
    }
}
