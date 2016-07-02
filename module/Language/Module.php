<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Language;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Configuration de la traduction des validateurs
        AbstractValidator::setDefaultTranslator(
            $event->getApplication()->getServiceManager()->get('MvcTranslator')
        );
    }
}
