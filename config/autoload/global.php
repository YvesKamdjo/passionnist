<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'log' => [
        'Logger\Error' => [
            'writers' => [
                [
                    'name'     => 'stream',
                    'priority' => 1,
                    'options'  => [
                        'stream' => 'logs/error.log',
                        'filters' => [
                            [
                                'name'    => 'Priority',
                                'options' => [
                                    'priority' => \Zend\Log\Logger::ERR,
                                    'operator' => '<='
                                ],
                            ],
                        ],
                        'formatter' => [
                            'name' => 'simple',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
