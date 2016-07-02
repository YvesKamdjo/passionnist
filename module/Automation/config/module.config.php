<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

return [
    'console' => [
        'router' => [
            'routes' => [
                'get-new-fashion-images' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'automation fashion-image',
                        'defaults' => [
                            'controller' => 'Automation\Controller\Automation',
                            'action'     => 'get-new-fashion-images',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Automation\Controller\Automation' => 'Automation\Controller\AutomationController',
        ],
    ],
];
