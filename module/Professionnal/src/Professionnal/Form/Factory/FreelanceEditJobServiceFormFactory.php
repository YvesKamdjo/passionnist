<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobServiceType;
use Backend\Service\CustomerCharacteristicService;
use Backend\Service\JobServiceService;
use Backend\Service\JobServiceTypeService;
use Professionnal\Form\FreelanceEditJobServiceForm;

class FreelanceEditJobServiceFormFactory
{
    /* @var $jobServiceService JobServiceService */
    private $jobServiceService;
    /* @var $jobServiceTypeService JobServiceTypeService */
    private $jobServiceTypeService;
    /* @var $customerCharacteristicService CustomerCharacteristicService */
    private $customerCharacteristicService;
    
    public function __construct(
        JobServiceService $jobServiceService,
        JobServiceTypeService $jobServiceTypeService,
        CustomerCharacteristicService $customerCharacteristicService
    ) {
        $this->jobServiceService = $jobServiceService;
        $this->jobServiceTypeService = $jobServiceTypeService;
        $this->customerCharacteristicService = $customerCharacteristicService;
    }

    public function createFreelanceEditJobServiceForm($idJobService)
    {
        // Récupère les données de la prestation
        $jobService = $this->jobServiceService->findById($idJobService);
        
        // Récupère les types de prestation
        $jobServiceTypeCollection = $this->jobServiceTypeService->listAll();
        
        // Récupère les caractéristiques
        $customerCharacteristicCollection = $this->customerCharacteristicService->listAll();
        
        // Récupère les types de prestation de la prestation
        $jobServiceJobServiceTypeCollection = $this->jobServiceTypeService->findByIdJobService($jobService);
        
        // Création de la liste des types de la prestation
        $jobServiceJobServiceTypeList = [];
        /* @var $jobServiceJobServiceType JobServiceType */
        foreach ($jobServiceJobServiceTypeCollection as $jobServiceJobServiceType) {
            $jobServiceJobServiceTypeList[] = $jobServiceJobServiceType->getIdJobServiceType();
        }
        
        // Récupère les caractéristiques utilisateur
        $jobServiceCustomerCharacteristicCollection = $this->customerCharacteristicService->findByIdJobService($jobService);
        
        // Création de la liste des caractéristiques de la prestation
        $jobServiceCustomerCharacteristicList = [];
        /* @var $jobServiceCustomerCharacteristicList CustomerCharacteristic */
        foreach ($jobServiceCustomerCharacteristicCollection as $jobServiceCustomerCharacteristic) {
            $jobServiceCustomerCharacteristicList[] = $jobServiceCustomerCharacteristic->getIdCustomerCharacteristic();
        }
        
        return new FreelanceEditJobServiceForm(
            $jobService,
            $jobServiceJobServiceTypeList,
            $jobServiceCustomerCharacteristicList,
            $jobServiceTypeCollection,
            $customerCharacteristicCollection
        );
    }
}
