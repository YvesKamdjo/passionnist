<?php

/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */
namespace Application\View\Helper;

use Application\Service\AuthorizationService;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class IsAllowed extends AbstractHelper
{
    /**
     * Vérifie si un utilisateur dispose de la permission d'accéder à la route
     * demandée
     * 
     * @param string $expectedRoute
     * @return bool
     */
    public function __invoke($expectedRoute)
    {
        // Récupération du service manager
        $serviceManager = $this->getView()->getHelperPluginManager()->getServiceLocator();
        
        // Réccupération du service d'autorisation
        /* @var $authorizationService AuthorizationService */
        $authorizationService = $serviceManager->get(
            'Application\Service\Authorization'
        );
        
        // Vérifie la permission de l'utilisateur
        return $authorizationService->isAllowed(
                $this->getView()->layout()->accountPermissionList,
                $expectedRoute
            );
    }
}
