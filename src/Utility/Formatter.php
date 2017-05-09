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

namespace App\Utility;

use Cake\Datasource\EntityInterface;

class Formatter
{

    /**
     * Formats dates in entity response.
     *
     * @param EntityInterface|array $row Entity record.
     * @return EntityInterface|array
     */
    public static function dateFormat($row)
    {
        if (isset($row['created']) && $row['created'] instanceof \DateTimeInterface) {
            $row['createdAt'] = $row['created']->format('Y-m-d\TH:i:s.000\Z');
            unset($row['created']);
        }
        if (isset($row['modified']) && $row['modified'] instanceof \DateTimeInterface) {
            $row['updatedAt'] = $row['modified']->format('Y-m-d\TH:i:s.000\Z');
            unset($row['modified']);
        }

        return $row;
    }
}
