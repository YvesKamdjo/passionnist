<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Entity\JobService;
use Zend\Form\Element\File;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class UploadJobServiceImageForm extends Form
{
    /* @var $jobService JobService */
    private $jobService;

    /**
     * Instancie un formulaire de création
     */
    public function __construct(JobService $jobService) {
        parent::__construct();

        $this->jobService = $jobService;

        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Photo de prestation
        $jobServiceImageElement = new File('job-service-image');
        $this->add($jobServiceImageElement);
        
        // idJobService
        $idJobServiceElement = new Text('job-service-id-job-service');
        $idJobServiceElement->setValue($this->jobService->getIdJobService());
        $this->add($idJobServiceElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Photo de prestation
        $jobServiceImageInput = new Input('job-service-image');

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($jobServiceImageInput);
    }
}
