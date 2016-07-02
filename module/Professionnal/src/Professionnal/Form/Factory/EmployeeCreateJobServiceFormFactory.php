<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Service\JobServiceTemplateService;
use Backend\Service\SalonService;
use Professionnal\Form\EmployeeCreateJobServiceForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class EmployeeCreateJobServiceFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Récupère les informations du salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');

        $salon = $salonService->findByEmployeeIdAccount($sessionContainer->account->getIdAccount());
        
        // Récupère les prestations du salon
        /* @var $jobServiceTemplateService JobServiceTemplateService */
        $jobServiceTemplateService = $serviceManager->get('Backend\Service\JobServiceTemplate');
        $jobServiceTemplateCollection = $jobServiceTemplateService->findAllActiveByIdSalon($salon->getIdSalon());
        
        return new EmployeeCreateJobServiceForm($jobServiceTemplateCollection);
    }
}
