<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Service\CustomerCharacteristicService;
use Backend\Service\JobServiceTypeService;
use Professionnal\Form\FreelanceCreateJobServiceForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FreelanceCreateJobServiceFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();
        
        // Récupère les types de prestation
        /* @var $jobServiceTypeService JobServiceTypeService */
        $jobServiceTypeService = $serviceManager->get('Backend\Service\JobServiceType');
        $jobServiceTypeCollection = $jobServiceTypeService->listAll();
        
        // Récupère les caractéristiques utilisateur
        /* @var $customerCharacteristicService CustomerCharacteristicService */
        $customerCharacteristicService = $serviceManager->get('Backend\Service\CustomerCharacteristic');
        $customerCharacteristicCollection = $customerCharacteristicService->listAll();
        
        return new FreelanceCreateJobServiceForm(
            $jobServiceTypeCollection,
            $customerCharacteristicCollection
        );
    }
}
