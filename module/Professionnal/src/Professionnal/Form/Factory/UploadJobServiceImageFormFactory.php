<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Service\JobServiceService;
use Professionnal\Form\UploadJobServiceImageForm;

class UploadJobServiceImageFormFactory
{
    /* @var $jobServiceService JobServiceService */
    private $jobServiceService;
    
    public function __construct(
        JobServiceService $jobServiceService
    ) {
        $this->jobServiceService = $jobServiceService;
    }

    public function createUploadJobServiceImageForm($idJobService)
    {
        // Récupère les données de la prestation
        $jobService = $this->jobServiceService->findById($idJobService);
                
        return new UploadJobServiceImageForm(
            $jobService
        );
    }
}
