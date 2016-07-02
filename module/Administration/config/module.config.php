<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'router' => [
        'routes' => [
            'administration-salon' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/administration/salon',
                    'defaults' => [
                        'controller' => 'Administration\Controller\Salon',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'certificate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/kbis/:idSalon',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Salon',
                                'action' => 'certificate',
                            ],
                        ],
                    ],
                    'activate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/activer/:idSalon',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Salon',
                                'action' => 'activate',
                            ],
                        ],
                    ],
                    'deactivate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/desactiver/:idSalon',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Salon',
                                'action' => 'deactivate',
                            ],
                        ],
                    ],
                ],
            ],
            'administration-account' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/administration/compte',
                    'defaults' => [
                        'controller' => 'Administration\Controller\Account',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'activate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/activer/:idAccount',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Account',
                                'action' => 'activate',
                            ],
                        ],
                    ],
                    'deactivate' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/desactiver/:idAccount',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Account',
                                'action' => 'deactivate',
                            ],
                        ],
                    ],
                    'qualification' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/diplome/:idAccount',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Account',
                                'action' => 'qualification',
                            ],
                        ],
                    ],
                    'take-over' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/prise-de-controle/:idAccount',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Account',
                                'action' => 'take-over',
                            ],
                        ],
                    ],
                    'end-take-over' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/quitter-prise-de-controle',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Account',
                                'action' => 'end-take-over',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/modifier/:idAccount',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Account',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                ],
            ],
            'administration-prospect' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/administration/prospect',
                    'defaults' => [
                        'controller' => 'Administration\Controller\Prospect',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'create' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/creer',
                            'defaults' => [
                                'controller' => 'Administration\Controller\Prospect',
                                'action' => 'create',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Administration\Controller\Salon' => 'Administration\Controller\SalonController',
            'Administration\Controller\Account' => 'Administration\Controller\AccountController',
            'Administration\Controller\Prospect' => 'Administration\Controller\ProspectController',
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'Administration\Form\CreateProspect' => 'Administration\Form\CreateProspectForm',
        ],
        'factories' => [
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Administration\Form\EditAccountFormFactory' => 'Administration\Form\Factory\EditAccountFormFactoryFactory',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'layout/administration' => __DIR__ . '/../view/layout/administration.phtml',
        ],
    ]
];
