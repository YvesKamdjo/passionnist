<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'modules' => [
        'Administration',
        'Application',
        'Automation',
        'Backend',
        'Language',
        'Notification',
        'Professionnal',
    ],
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'config_cache_enabled' => true,
        'cache_dir' => 'cache',
        'check_dependencies' => false,
    ],
];
