<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'service_manager' => [
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
    'translator' => [
        'locale' => 'fr_FR',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
            [
                'type'     => 'phpArray',
                'base_dir' => __DIR__ . '/../language/validator',
                'pattern'  => '%s.php'
            ],
        ],
    ],
];
