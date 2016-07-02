<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form\Factory;

use Application\Form\JobServiceSearchForm;
use Backend\Service\AccountService;
use Backend\Service\CustomerCharacteristicService;
use Backend\Service\JobServiceTypeService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class JobServiceSearchFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();
     
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Récupère les données de l'utilisateur
        /* @var $accountService AccountService */
        $accountService = $serviceManager->get('Backend\Service\Account');
        if (is_a($sessionContainer->account, 'Backend\Entity\Account')) {
            $account = $accountService->findByIdAccount($sessionContainer->account->getIdAccount());
        }
        else {
            $account = new \Backend\Entity\Account();
        }
    
        
        // Récupère les types de prestation
        /* @var $jobServiceTypeService JobServiceTypeService */
        $jobServiceTypeService = $serviceManager->get('Backend\Service\JobServiceType');
        $jobServiceTypeCollection = $jobServiceTypeService->listAll();
        
        // Récupère les caractéristiques utilisateur
        /* @var $customerCharacteristicService CustomerCharacteristicService */
        $customerCharacteristicService = $serviceManager->get('Backend\Service\CustomerCharacteristic');
        $customerCharacteristicCollection = $customerCharacteristicService->listAll();
        
        return new JobServiceSearchForm(
            $account,
            $jobServiceTypeCollection,
            $customerCharacteristicCollection
        );
    }
}
