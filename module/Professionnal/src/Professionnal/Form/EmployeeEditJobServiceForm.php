<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Collection\ArrayCollection;
use Backend\Entity\JobService;
use Backend\Entity\JobServiceTemplate;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\I18n\Validator\IsInt;
use Zend\InputFilter\Input;

class EmployeeEditJobServiceForm extends Form
{
    /* @var $jobService JobService */
    private $jobService;
    private $jobServiceTemplateCollection;

    /**
     * Instancie un formulaire de création
     */
    public function __construct(
        JobService $jobService,
        ArrayCollection $jobServiceTemplateCollection
    ) {
        parent::__construct();

        $this->jobService = $jobService;
        $this->jobServiceTemplateCollection = $jobServiceTemplateCollection;

        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Nom
        $nameElement = new Text('name');
        $nameElement->setValue($this->jobService->getName());
        $this->add($nameElement);
        
        // Durée
        $durationElement = new Select('duration');
        $durationElement->setValueOptions([
            15 => '15 minutes', 
            30 => '30 minutes', 
            45 => '45 minutes', 
            60 => '1 heure', 
            75 => '1 heure 15', 
            90 => '1 heure 30', 
            105 => '1 heure 45', 
            120 => '2 heures', 
            135 => '2 heures 15', 
            150 => '2 heures 30', 
            165 => '2 heures 45', 
            180 => '3 heures'
        ]);
        $durationElement->setValue($this->jobService->getDuration());
        $this->add($durationElement);
        
        // Description
        $descriptionElement = new Text('description');
        $descriptionElement->setValue($this->jobService->getDescription());
        $this->add($descriptionElement);
        
        // Prestation de salon
        $selectSalonJobServiceElement = new Select('salon-job-service');
        
        // Remplissage des prestations du salon
        $selectSalonJobServiceValues = [];
        
        /* @var $jobServiceTemplate JobServiceTemplate */
        foreach ($this->jobServiceTemplateCollection as $jobServiceTemplate) {
            $jobServiceName = $jobServiceTemplate->getName() . ' - ' . $jobServiceTemplate->getPrice() . '€';
            $selectSalonJobServiceValues[$jobServiceTemplate->getIdJobServiceTemplate()] = $jobServiceName;
        }
        
        $selectSalonJobServiceElement->setValueOptions($selectSalonJobServiceValues);
        $selectSalonJobServiceElement->setValue($this->jobService->getIdJobServiceTemplate());
        $this->add($selectSalonJobServiceElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Nom
        $nameInput = new Input('name');
        $nameInput->setRequired(true);
        
        // Durée
        $durationInput = new Input('duration');
        $durationInput->setRequired(true);
        $durationInput->getValidatorChain()
                ->attach(new IsInt());
        
        // Description
        $descriptionInput = new Input('description');

        // Prestation du salon
        $jobServiceInput = new Input('salon-job-service');
        $jobServiceInput->setRequired(true);
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($nameInput);
        $inputFilter->add($durationInput);
        $inputFilter->add($descriptionInput);
        $inputFilter->add($jobServiceInput);
    }
}
