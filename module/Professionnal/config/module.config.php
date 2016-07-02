<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'router' => [
        'routes' => [
            'professionnal-dashboard' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel',
                    'defaults' => [
                        'controller' => 'Professionnal\Controller\Index',
                        'action' => 'dashboard',
                    ],
                ],
            ],
            'professionnal-booking' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/mes-rendez-vous',
                    'defaults' => [
                        'controller' => 'Professionnal\Controller\Booking',
                        'action' => 'booking-list',
                    ],
                ],
            ],
            'professionnal-join-salon' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/rejoindre-un-salon',
                    'defaults' => [
                        'controller' => 'Professionnal\Controller\Account',
                        'action' => 'join-salon',
                    ],
                ],
            ],
            'professionnal-profile-edit' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/modification-profil',
                    'defaults' => [
                        'controller' => 'Professionnal\Controller\Account',
                        'action' => 'edit-profile',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'send-avatar' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/photo',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Account',
                                'action' => 'send-avatar',
                            ],
                        ],
                    ],
                    'send-qualification' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/diplome',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Account',
                                'action' => 'send-qualification',
                            ],
                        ],
                    ],
                ],
            ],
            'professionnal-salon' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/salon',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'create' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/creation',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Salon',
                                'action' => 'create-salon',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/modification',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Salon',
                                'action' => 'edit-salon',
                            ],
                        ],
                    ],
                    'manage-images' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/gerer-images',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Salon',
                                'action'     => 'manage-images'
                            ],
                        ],
                    ],
                    'send-image' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/photo',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Salon',
                                'action' => 'send-image',
                            ],
                        ],
                    ],
                    'delete-image' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/supprimer-photo/:imageId',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Salon',
                                'action' => 'delete-image',
                            ],
                        ],
                    ],
                    'salon-attachment-request-list' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/demande-rattachement',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Salon',
                                'action' => 'salon-attachment-request-list',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'accept' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/accepter/:idEmployee',
                                    'defaults' => [
                                        'controller' => 'Professionnal\Controller\Salon',
                                        'action' => 'accept-attachment-request',
                                    ],
                                ],
                            ],
                            'refuse' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/refuser/:idEmployee',
                                    'defaults' => [
                                        'controller' => 'Professionnal\Controller\Salon',
                                        'action' => 'refuse-attachment-request',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'send-certificate' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/certificat',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Salon',
                                'action' => 'send-certificate',
                            ],
                        ],
                    ],
                ],
            ],
            'professionnal-job-service-template' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/prestation-modele',
                    'defaults' => [
                        'controller' => 'Professionnal\Controller\JobServiceTemplate',
                        'action' => 'list-all',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'create' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/creation',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobServiceTemplate',
                                'action' => 'create',
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/supprimer/:idJobServiceTemplate',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobServiceTemplate',
                                'action' => 'delete',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/modifier/:idJobServiceTemplate',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobServiceTemplate',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                ],
            ],
            'professionnal-job-service' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/prestation',
                    'defaults' => [
                        'controller' => 'Professionnal\Controller\JobService',
                        'action' => 'list-all',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'create' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/creation',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobService',
                                'action' => 'create',
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/supprimer/:idJobService',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobService',
                                'action' => 'delete',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/modifier/:idJobService',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobService',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'manage-images' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/gerer-images/:idJobService',
                            'constraints' => [
                                'number' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobService',
                                'action'     => 'manage-images'
                            ],
                        ],
                    ],
                    'send-image' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/photo',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobService',
                                'action' => 'send-image',
                            ],
                        ],
                    ],
                    'delete-image' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/supprimer-photo/:imageId',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\JobService',
                                'action' => 'delete-image',
                            ],
                        ],
                    ],
                ],
            ],
            'professionnal-financial' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'create-transfer-request' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/creer-demande-virement',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\TransferRequest',
                                'action' => 'create',
                            ],
                        ],
                    ],
                    'list-transfer-request' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/liste-demandes-virement',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\TransferRequest',
                                'action' => 'list',
                            ],
                        ],
                    ],
                    'list-transaction' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/liste-transactions',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Transaction',
                                'action' => 'list',
                            ],
                        ],
                    ],
                ],
            ],
            'professionnal-discount' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/promotions',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'edit' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/modifier',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Discount',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                ],
            ],
            'professionnal-availabilities' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/professionnel/planning',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'edit' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/modifier',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Availabilities',
                                'action' => 'edit',
                            ],
                        ],
                    ],
                    'create-availability' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/creer-disponibilite',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Availabilities',
                                'action' => 'create-availability',
                            ],
                        ],
                    ],
                    'create-absence' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/creer-absence',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Availabilities',
                                'action' => 'create-absence',
                            ],
                        ],
                    ],
                    'delete-exception' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/supprimer-exception/:idAvailabilityException',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Availabilities',
                                'action' => 'delete-exception',
                            ],
                        ],
                    ],
                    'exception-list' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/liste-exceptions',
                            'defaults' => [
                                'controller' => 'Professionnal\Controller\Availabilities',
                                'action' => 'exception-list',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Professionnal\Controller\Account' => 'Professionnal\Controller\AccountController',
            'Professionnal\Controller\Index' => 'Professionnal\Controller\IndexController',
            'Professionnal\Controller\Salon' => 'Professionnal\Controller\SalonController',
            'Professionnal\Controller\JobService' => 'Professionnal\Controller\JobServiceController',
            'Professionnal\Controller\JobServiceTemplate' => 'Professionnal\Controller\JobServiceTemplateController',
            'Professionnal\Controller\TransferRequest' => 'Professionnal\Controller\TransferRequestController',
            'Professionnal\Controller\Availabilities' => 'Professionnal\Controller\AvailabilitiesController',
            'Professionnal\Controller\Booking' => 'Professionnal\Controller\BookingController',
            'Professionnal\Controller\Discount' => 'Professionnal\Controller\DiscountController',
            'Professionnal\Controller\Transaction' => 'Professionnal\Controller\TransactionController',
        ],
    ],
    'form_elements' => [
        'invokables' => [
            'Professionnal\Form\UploadProfessionnalAccountImage' => 'Professionnal\Form\UploadProfessionnalAccountImageForm',
            'Professionnal\Form\UploadProfessionnalQualification' => 'Professionnal\Form\UploadProfessionnalQualificationForm',
            'Professionnal\Form\ProfessionnalJoinSalon' => 'Professionnal\Form\ProfessionnalJoinSalonForm',
            'Professionnal\Form\ProfessionnalCreateSalon' => 'Professionnal\Form\ProfessionnalCreateSalonForm',
            'Professionnal\Form\DeleteJobServiceTemplateConfirm' => 'Professionnal\Form\DeleteJobServiceTemplateConfirmForm',
            'Professionnal\Form\CreateTransferRequest' => 'Professionnal\Form\CreateTransferRequestForm',
        ],
        'factories' => [
            'Professionnal\Form\EditProfessionnalProfile' => 'Professionnal\Form\Factory\EditProfessionnalProfileFormFactory',
            'Professionnal\Form\ProfessionnalEditSalon' => 'Professionnal\Form\Factory\ProfessionnalEditSalonFormFactory',
            'Professionnal\Form\UploadSalonCertificate' => 'Professionnal\Form\Factory\UploadSalonCertificateFormFactory',
            'Professionnal\Form\CreateJobServiceTemplate' => 'Professionnal\Form\Factory\CreateJobServiceTemplateFormFactory',
            'Professionnal\Form\EmployeeCreateJobService' => 'Professionnal\Form\Factory\EmployeeCreateJobServiceFormFactory',
            'Professionnal\Form\FreelanceCreateJobService' => 'Professionnal\Form\Factory\FreelanceCreateJobServiceFormFactory',
            'Professionnal\Form\EditAvailabilities' => 'Professionnal\Form\Factory\EditAvailabilitiesFormFactory',
            'Professionnal\Form\EditDiscount' => 'Professionnal\Form\Factory\EditDiscountFormFactory',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Professionnal\Form\EmployeeEditJobServiceFormFactory' => 'Professionnal\Form\Factory\EmployeeEditJobServiceFormFactoryFactory',
            'Professionnal\Form\FreelanceEditJobServiceFormFactory' => 'Professionnal\Form\Factory\FreelanceEditJobServiceFormFactoryFactory',
            'Professionnal\Form\UploadJobServiceImageFormFactory' => 'Professionnal\Form\Factory\UploadJobServiceImageFormFactoryFactory',
            'Professionnal\Form\EditJobServiceTemplateFormFactory' => 'Professionnal\Form\Factory\EditJobServiceTemplateFormFactoryFactory',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'layout/professionnal' => __DIR__ . '/../view/layout/professionnal.phtml',
        ],
    ]
];
