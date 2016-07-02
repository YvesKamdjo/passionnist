<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Entity\Account;
use Backend\Service\SalonService;
use Professionnal\Form\ProfessionnalEditSalonForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class ProfessionnalEditSalonFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Récupère les informations du salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        $salon = $salonService->findByManagerIdAccount($sessionContainer->account->getIdAccount());
        
        return new ProfessionnalEditSalonForm($salon);
    }
}
