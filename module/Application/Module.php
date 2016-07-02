<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Application;

use Application\Listener\AuthorizationListener;
use Application\Listener\LayoutInitializerListener;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
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
        $eventManager = $event->getApplication()->getEventManager();
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'logApplicationError']);
        
        $serviceManager = $event->getApplication()->getServiceManager();
        $config = $serviceManager->get('config');
        
        // Listener permettant de gérer les droits d'accès à l'application
        $eventManager->attach(new AuthorizationListener());
        
        // Listener permettant de configurer le layout
        $eventManager->attach(
            new LayoutInitializerListener(
                $serviceManager,
                $config['application']['version']
            )
        );
    }
    
    public function logApplicationError(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        $serviceManager->get('Logger\Error')->info($event->getParam('exception'));
    }
    
    public function getConsoleBanner(AdapterInterface $console)
    {
        return 'Module de ligne de commande de l\'application HAIRLOV';
    }
    
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'notification' => 'Lance une notification',
            ['new-prospect', 'Lance l\'envoi de l\'email aux nouveaux prospects'],
            ['professional-new-booking', 'Lance l\'envoi de l\'email aux professionnels pour une nouvelle réservation'],
            ['customer-new-booking', 'Lance l\'envoi de l\'email aux clients pour une nouvelle réservation'],
            'automation' => 'Lance une tâche automatisée',
            ['fashion-image', 'Récupère les nouvelles images Pinterest pour la page d\'accueil'],
        ];
    }
}
