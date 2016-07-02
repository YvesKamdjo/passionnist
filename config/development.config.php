<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

return [
    'modules' => [
        'ZendDeveloperTools',
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.development.php'
        ],
        'config_cache_enabled' => false,
        'check_dependencies' => true,
    ],
];
