<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
 
class GetAccountHomepageByPermissions extends AbstractPlugin
{
    public function __invoke($permissionList)
    {
        // Récupération du service d'autorisation
        /* @var $authorizationService AuthorizationService */
        $authorizationService = $this->getController()->getServiceLocator()
            ->get('Application\Service\Authorization');
        
        if ($authorizationService->isAllowed($permissionList, 'professionnal-dashboard')) {
            return 'professionnal-dashboard';
        }
        elseif ($authorizationService->isAllowed($permissionList, 'administration-account')) {
            return 'administration-account';
        }
        else {
            return 'application-home';
        }
    }
}