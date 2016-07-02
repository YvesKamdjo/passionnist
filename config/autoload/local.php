<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'db' => [
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=passionist-xyq;host=127.0.0.1',
        'username' => 'root',
        'password' => '',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND=> 'SET NAMES \'UTF8\'',
        ],
    ],
    'service_manager'=>[
        'factories'=> [
            'Zend\Db\Adapter\AdapterServiceFactory'
        ]
    ],
    'log' => [
        'Logger\Error' => [
            'writers' => [
                [
                    'name'     => 'FirePhp',
                    'options'  => [
                        'filters' => [
                            [
                                'name'    => 'Priority',
                                'options' => [
                                    'priority' => \Zend\Log\Logger::DEBUG,
                                    'operator' => '<='
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'mailer' => [
        'fromEmailAddress' => '<send@host.tld>',
        'fromName' => '<Sender name>',
        'replyToEmailAddress' => '<reply@host.tld>',
        'replyToName' => '<Reply name>',
        'urlDomain' => '<http://stackoverflow.com/>',
        'toEmailAddress' => [
            'Si présente, tous les e-mails iront à cette adresse <receive@host.tld>',
        ],
    ],
    'payment' => [
        'securityKey' => '<clé de sécurité>',
        'notificationUrl' => '<ipn url>',
        'returnUrl' => '<url return>',
        'cancelUrl' => '<url cancel>',
    ],
    'pinterest' => [
        'board' => '<pinterest board>',
        'access_token' => '<pinterest access token>',
    ],
    'facebook' => [
        'applicationId' => '<facebook application id>',
        'applicationSecret' => '<facebook application secret key>',
    ],
];