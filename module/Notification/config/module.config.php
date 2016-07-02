<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

return [
    'console' => [
        'router' => [
            'routes' => [
                'notify-new-prospect' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'notification new-prospect',
                        'defaults' => [
                            'controller' => 'Notification\Controller\Notification',
                            'action'     => 'notify-new-prospect',
                        ],
                    ],
                ],
                'notify-professional-new-booking' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'notification professional-new-booking',
                        'defaults' => [
                            'controller' => 'Notification\Controller\Notification',
                            'action'     => 'notify-professional-new-booking',
                        ],
                    ],
                ],
                'notify-customer-new-booking' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'notification customer-new-booking',
                        'defaults' => [
                            'controller' => 'Notification\Controller\Notification',
                            'action'     => 'notify-customer-new-booking',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Notification\Controller\Notification' => 'Notification\Controller\NotificationController',
        ],
    ],
    'form_elements' => [
        'invokables' => [
        ],
        'factories' => [
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Notification\Service\Notification' => 'Notification\Service\Factory\NotificationServiceFactory',
            'Notification\Mapper\Notification' => 'Notification\Mapper\Factory\NotificationMapperFactory',
            'Notification\Finder\NewProspectsNotification' => 'Notification\Finder\Factory\NewProspectsNotificationFinderFactory',
            'Notification\Finder\NewBookingNotification' => 'Notification\Finder\Factory\NewBookingNotificationFinderFactory',
        ],
    ],
];
