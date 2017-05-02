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
        // if service class is not defined we use crud fallback service
        'ServiceFallback' => '\\CakeDC\\Api\\Service\\FallbackService',
        // response rendered as JSend
        'renderer' => 'CakeDC/Api.Json',
        // Data parse from cakephp request object
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
                            // 'allow' => '*',
                            'authorize' => [
                                'CakeDC/Api.Crud' => []
                            ],
                            'authenticate' => [
                                'CakeDC/Api.Psr7'
                            ],
                        ],
                        // default app extensions
                        'Extension' => [
                            'CakeDC/Api.Cors',
                            'CakeDC/Api.Sort',
                        ]
                    ],
                    // all index actions configuration
                    'Index' => [
                        'Extension' => [
                            // enable pagination for index actions
                            // 'CakeDC/Api.Paginate',
                            'CakeDC/Api.Cors',
                        ],
                    ],
					'Describe' => [
                        'Auth' => [
                            'allow' => '*',
						]
					],
					'DescribeId' => [
                        'Auth' => [
                            'allow' => '*',
						]
					],
                ],
            ],
        ],
    ]
];
