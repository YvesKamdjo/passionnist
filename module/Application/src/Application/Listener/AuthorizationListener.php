<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */


namespace Application\Listener;

use Application\Authentication\Identity;
use Application\Service\AuthorizationService;
use Backend\Collection\ArrayCollection;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\Stdlib\ResponseInterface;

class AuthorizationListener extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'checkAccessToRoute']
        );
    }

    /**
     * Vérifie que le compte utilisateur authentifié a accès à la route demandée
     * 
     * @param MvcEvent $event
     * @return null|ResponseInterface
     */
    public function checkAccessToRoute(MvcEvent $event)
    {
        // Check que la route demandée existe
        if ( ! $event->getRouteMatch() instanceof RouteMatch) {
            return;
        }
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $event->getApplication()
            ->getServiceManager();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Determine les permissions de l'utilisateur connecté
        /* @var $authorizationService AuthorizationService */
        $authorizationService = $serviceManager->get(
            'Application\Service\Authorization'
        );
        
        $accountPermissionList = [];        
        // Si l'utilisateur existe dans la session
        if (is_a($sessionContainer->account, '\Backend\Entity\Account')) {
            // Récupération des permissions de l'utilisateur
            $accountPermissionList = $authorizationService->getAccountPermissions($sessionContainer->account);
        }
        else {
            // Authorisation d'accéder aux pages publiques
            $accountPermissionList = ['access-public-pages'];
        }
        
        // Regarde si l'utilisateur a accès à la route en fonction de son rôle
        $isAllowed = $authorizationService->isAllowed(
            $accountPermissionList,
            $event->getRouteMatch()->getMatchedRouteName()
        );
        
        // Si l'utilisateur ne dispose pas du droit, on le redirige vers l'accueil
        if ($isAllowed === false) {
            $event->getResponse()
                ->getHeaders()
                ->addHeaderLine('Location', '/');
            $event->getResponse()->setStatusCode(302);
            return $event->getResponse();
        }
    }
}