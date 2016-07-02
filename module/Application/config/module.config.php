<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'router' => [
        'routes' => [
            'application-home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'application-contact' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/contact',
                    'defaults' => [
                        'controller' => 'Application\Controller\Contact',
                        'action'     => 'index',
                    ],
                ],
            ],
            'application-legal' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/mentions-legales',
                    'defaults' => [
                        'controller' => 'Application\Controller\CMS',
                        'action'     => 'legal',
                    ],
                ],
            ],
            'application-professional-landing-page' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/inscription-professionnel',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'professional-landing-page',
                    ],
                ],
            ],
            'application-login' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/log-in',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'log-in',
                    ],
                ],
            ],
            'application-facebook-login' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/facebook-log-in',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'facebook-log-in',
                    ],
                ],
            ],
            'application-logout' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/log-out',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'log-out',
                    ],
                ],
            ],
            'application-professionnal-sign-up' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/professional-signup',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'professionnal-sign-up',
                    ],
                ],
            ],
            'application-search-job-service' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/recherche-prestation',
                    'defaults' => [
                        'controller' => 'Application\Controller\JobService',
                        'action'     => 'search',
                    ],
                ],
            ],
            'application-search-professional' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/recherche-coiffeur',
                    'defaults' => [
                        'controller' => 'Application\Controller\JobService',
                        'action'     => 'search-professional',
                    ],
                ],
            ],
            'application-job-service' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/prestation/:idJobService',
                    'defaults' => [
                        'controller' => 'Application\Controller\JobService',
                        'action'     => 'job-service-page',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'booking' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/reserver',
                            'defaults' => [
                                'controller' => 'Application\Controller\JobService',
                                'action' => 'booking',
                            ],
                        ],
                    ],
                    'date-changed' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/date-changed',
                            'defaults' => [
                                'controller' => 'Application\Controller\JobService',
                                'action' => 'job-service-date-changed',
                            ],
                        ],
                    ],
                ],
            ],
            'application-booking' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/reservation/:bookingId',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'comment' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/laisser-commentaire',
                            'defaults' => [
                                'controller' => 'Application\Controller\BookingComment',
                                'action' => 'create',
                            ],
                        ],
                    ],
                    'generate-invoice' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/generer-facture',
                            'defaults' => [
                                'controller' => 'Application\Controller\BookingComment',
                                'action' => 'generate-invoice',
                            ],
                        ],
                    ],
                ],
            ],
            'application-professional' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/professionnel/:idProfessional',
                    'defaults' => [
                        'controller' => 'Application\Controller\Professional',
                        'action'     => 'professional-page',
                    ],
                ],
            ],
            'application-salon' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/salon/:salonId',
                    'defaults' => [
                        'controller' => 'Application\Controller\Salon',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'professional' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/coiffeurs',
                            'defaults' => [
                                'controller' => 'Application\Controller\Salon',
                                'action' => 'professional',
                            ],
                        ],
                    ],
                    'job-service' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/prestations',
                            'defaults' => [
                                'controller' => 'Application\Controller\Salon',
                                'action' => 'job-service',
                            ],
                        ],
                    ],
                    'booking-comment' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/avis',
                            'defaults' => [
                                'controller' => 'Application\Controller\Salon',
                                'action' => 'booking-comment',
                            ],
                        ],
                    ],
                ],
            ],
            'application-load-image' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/image/:category/:idImage[/:width:x:height][@:quality][$:align]',
                    'constraints' => array(
                        'width' => '[0-9]+',
                        'height' => '[0-9]+',
                        'quality' => '[0-9]+',
                        'align' => '[a-zA-Z]+',
                    ),
                    'defaults' => [
                        'controller' => 'Application\Controller\Image',
                        'action'     => 'image',
                    ],
                ],
            ],
            'application-ipn' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/ipn',
                    'defaults' => [
                        'controller' => 'Application\Controller\Payment',
                        'action'     => 'ipn',
                    ],
                ],
            ],
            'application-return-payment' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/retour-paiement',
                    'defaults' => [
                        'controller' => 'Application\Controller\Payment',
                        'action'     => 'return-payment',
                    ],
                ],
            ],
            'application-booking-list' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/mes-reservations',
                    'defaults' => [
                        'controller' => 'Application\Controller\Booking',
                        'action'     => 'booking-list',
                    ],
                ],
            ],
            'application-liked-professional-list' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/mes-lovs',
                    'defaults' => [
                        'controller' => 'Application\Controller\Professional',
                        'action'     => 'liked-professional-list',
                    ],
                ],
            ],
            'application-canceled-payment' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/annulation-paiement',
                    'defaults' => [
                        'controller' => 'Application\Controller\Payment',
                        'action'     => 'canceled-payment',
                    ],
                ],
            ],
            'application-switch-like' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/switch-lov',
                    'defaults' => [
                        'controller' => 'Application\Controller\Professional',
                        'action'     => 'switch-like',
                    ],
                ],
            ],
            'application-load-more-fashion-images' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/load-more-fashion-images',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'load-more-fashion-images',
                    ],
                ],
            ],
            'application-password-lost' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/mot-de-passe-oublie',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'password-lost',
                    ],
                ],
            ],
            'application-new-password' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/nouveau-mot-de-passe',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'new-password',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Contact' => 'Application\Controller\ContactController',
            'Application\Controller\CMS' => 'Application\Controller\CMSController',
            'Application\Controller\Image' => 'Application\Controller\ImageController',
            'Application\Controller\JobService' => 'Application\Controller\JobServiceController',
            'Application\Controller\Professional' => 'Application\Controller\ProfessionalController',
            'Application\Controller\Salon' => 'Application\Controller\SalonController',
            'Application\Controller\Payment' => 'Application\Controller\PaymentController',
            'Application\Controller\Booking' => 'Application\Controller\BookingController',
            'Application\Controller\BookingComment' => 'Application\Controller\BookingCommentController',
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'getAccountHomepageByPermissions' => 'Application\Controller\Plugin\GetAccountHomepageByPermissions',
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'Application\Form\LogIn' => 'Application\Form\LogInForm',
        ],
        'factories' => [
            'Application\Form\JobServiceSearch' => 'Application\Form\Factory\JobServiceSearchFormFactory',
            'Application\Form\ProfessionalSearch' => 'Application\Form\Factory\ProfessionalSearchFormFactory',
            'Application\Form\AddBookingInformations' => 'Application\Form\Factory\AddBookingInformationsFromFactory',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Application\Service\Authorization' => 'Application\Service\Factory\AuthorizationServiceFactory',
            'Application\Service\Email' => 'Application\Service\Factory\EmailServiceFactory',
            'Application\Form\ProfessionnalSignUpFormFactory' => 'Application\Form\Factory\ProfessionnalSignUpFormFactoryFactory',
            'Application\Form\AddBookingInformationsFormFactory' => 'Application\Form\Factory\AddBookingInformationsFormFactoryFactory',
            'Application\Form\CreateBookingCommentFormFactory' => 'Application\Form\Factory\CreateBookingCommentFormFactoryFactory',
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
        'aliases' => [
            'db' => 'Zend\Db\Adapter\Adapter',
        ],
    ],
    'view_manager' => [
        'doctype'            => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/application.phtml',
            'error/404'     => __DIR__ . '/../view/error/404.phtml',
            'error/index'   => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'displayAlert'             => 'Application\View\Helper\DisplayAlert',
            'displayFlashMessages'     => 'Application\View\Helper\DisplayFlashMessages',
            'displayFormElementErrors' => 'Application\View\Helper\DisplayFormElementErrors',
            'isAllowed'                => 'Application\View\Helper\IsAllowed',
            'slugify'                  => 'Application\View\Helper\Slugify',
        ],
    ]
];
