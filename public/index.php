<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

error_reporting(E_STRICT);

chdir(dirname(__DIR__));

defined('APP_ENV') || define(
    'APP_ENV',
    getenv('APP_ENV') ? getenv('APP_ENV') : 'DEV'
);

require 'vendor/autoload.php';

$applicationConfig = require 'config/application.config.php';
if (APP_ENV === 'DEV' && is_file('config/development.config.php') === true) {
    $applicationConfig = \Zend\Stdlib\ArrayUtils::merge(
        $applicationConfig,
        require 'config/development.config.php'
    );
}

\Zend\Mvc\Application::init($applicationConfig)->run();

?>