<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Service\AccountService;
use Professionnal\Form\EditProfessionnalProfileForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class EditProfessionnalProfileFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Récupère les informations du compte
        /* @var $accountService AccountService */
        $accountService = $serviceManager->get('Backend\Service\Account');
        $account = $accountService->findByIdAccount($sessionContainer->account->getIdAccount());
        
        return new EditProfessionnalProfileForm($account);
    }
}
