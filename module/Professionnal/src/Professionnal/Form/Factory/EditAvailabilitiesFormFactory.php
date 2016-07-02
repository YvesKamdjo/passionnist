<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Professionnal\Form\EditAvailabilitiesForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class EditAvailabilitiesFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        try {
            $weekTemplate = $serviceManager->get('Backend\Service\WeekTemplate')
                ->findByAccountId($sessionContainer->account->getIdAccount());
            
            // Récupère les dispo
            $availabilityCollection = $serviceManager->get('Backend\Service\Availability')
                ->findByWeekTemplateId($weekTemplate->getIdWeekTemplate());
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        return new EditAvailabilitiesForm($availabilityCollection);
    }
}
