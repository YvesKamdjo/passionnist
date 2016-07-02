<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Service\JobServiceService;
use Backend\Service\JobServiceTemplateService;
use Backend\Service\SalonService;
use Professionnal\Form\EmployeeEditJobServiceForm;
use Zend\Session\Container;

class EmployeeEditJobServiceFormFactory
{
    /* @var $jobServiceService JobServiceService */
    private $jobServiceService;
    /* @var $salonService SalonService */
    private $salonService;
    /* @var $jobServiceTemplateService JobServiceTemplateService */
    private $jobServiceTemplateService;
    
    public function __construct(
        JobServiceService $jobServiceService,
        SalonService $salonService,
        JobServiceTemplateService $jobServiceTemplateService
    ) {
        $this->jobServiceService = $jobServiceService;
        $this->salonService = $salonService;
        $this->jobServiceTemplateService = $jobServiceTemplateService;
    }

    public function createEmployeeEditJobServiceForm($idJobService)
    {
        // Récupère les données de la prestation
        $jobService = $this->jobServiceService->findById($idJobService);
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Récupère les informations du salon
        $salon = $this->salonService->findByEmployeeIdAccount($sessionContainer->account->getIdAccount());
        
        // Récupère les modèles de prestation du salon
        $jobServiceTemplateCollection = $this->jobServiceTemplateService->findAllActiveByIdSalon($salon->getIdSalon());
        
        return new EmployeeEditJobServiceForm(
            $jobService,
            $jobServiceTemplateCollection
        );
    }
}
