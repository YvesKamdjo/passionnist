<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobServiceType;
use Backend\Service\CustomerCharacteristicService;
use Backend\Service\JobServiceTemplateService;
use Backend\Service\JobServiceTypeService;
use Professionnal\Form\EditJobServiceTemplateForm;

class EditJobServiceTemplateFormFactory
{
    /* @var $jobServiceTemplateService JobServiceTemplateService */
    private $jobServiceTemplateService;
    /* @var $jobServiceTypeService JobServiceTypeService */
    private $jobServiceTypeService;
    /* @var $customerCharacteristicService CustomerCharacteristicService */
    private $customerCharacteristicService;
    
    public function __construct(
        JobServiceTemplateService $jobServiceTemplateService,
        JobServiceTypeService $jobServiceTypeService,
        CustomerCharacteristicService $customerCharacteristicService
    ) {
        $this->jobServiceTemplateService = $jobServiceTemplateService;
        $this->jobServiceTypeService = $jobServiceTypeService;
        $this->customerCharacteristicService = $customerCharacteristicService;
    }

    public function createEditJobServiceTemplateForm($idJobServiceTemplate)
    {
        // Récupère les données de la prestation
        $jobServiceTemplate = $this->jobServiceTemplateService->findById($idJobServiceTemplate);
        
        // Récupère les types de prestation
        $jobServiceTypeCollection = $this->jobServiceTypeService->listAll();
        
        // Récupère les caractéristiques
        $customerCharacteristicCollection = $this->customerCharacteristicService->listAll();
        
        // Récupère les types de prestation de la prestation
        $jobServiceTemplateJobServiceTypeCollection = $this->jobServiceTypeService->findByIdJobServiceTemplate($jobServiceTemplate);
        
        // Création de la liste des types de la prestation
        $jobServiceTemplateJobServiceTypeList = [];
        /* @var $jobServiceTemplateJobServiceType JobServiceType */
        foreach ($jobServiceTemplateJobServiceTypeCollection as $jobServiceTemplateJobServiceType) {
            $jobServiceTemplateJobServiceTypeList[] = $jobServiceTemplateJobServiceType->getIdJobServiceType();
        }
        
        // Récupère les caractéristiques utilisateur
        $jobServiceTemplateCustomerCharacteristicCollection = $this->customerCharacteristicService->findByIdJobServiceTemplate($jobServiceTemplate);
        
        // Création de la liste des caractéristiques de la prestation
        $jobServiceTemplateCustomerCharacteristicList = [];
        /* @var $jobServiceTemplateCustomerCharacteristicList CustomerCharacteristic */
        foreach ($jobServiceTemplateCustomerCharacteristicCollection as $jobServiceTemplateCustomerCharacteristic) {
            $jobServiceTemplateCustomerCharacteristicList[] = $jobServiceTemplateCustomerCharacteristic->getIdCustomerCharacteristic();
        }
        
        return new EditJobServiceTemplateForm(
            $jobServiceTemplate,
            $jobServiceTemplateJobServiceTypeList,
            $jobServiceTemplateCustomerCharacteristicList,
            $jobServiceTypeCollection,
            $customerCharacteristicCollection
        );
    }
}
