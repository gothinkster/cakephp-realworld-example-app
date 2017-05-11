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

return [
    'Api' => [
        'ServiceFallback' => '\\CakeDC\\Api\\Service\\FallbackService',
        'renderer' => 'AppJson',
        'parser' => 'CakeDC/Api.Form',

        'useVersioning' => false,
        'versionPrefix' => 'v',

        'Auth' => [
            'Crud' => [
                'default' => 'auth'
            ],
        ],

        'Service' => [
            'default' => [
                'options' => [
                    'Extension' => [
                        'CakeDC/Api.OptionsHandler'
                    ],
                ],
                'Action' => [
                    'default' => [
                        'Auth' => [
                            'authenticate' => [
                                'CakeDC/Api.Psr7'
                            ],
                        ],
                        'Extension' => [
                            'CakeDC/Api.Cors',
                        ]
                    ],
                ],
            ],
        ],
    ]
];
