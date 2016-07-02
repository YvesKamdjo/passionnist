<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Collection\ArrayCollection;
use Backend\Entity\JobServiceTemplate;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\I18n\Validator\IsInt;
use Zend\InputFilter\Input;

class EmployeeCreateJobServiceForm extends Form
{
    private $jobServiceTemplateCollection;

    /**
     * Instancie un formulaire de création
     */
    public function __construct(ArrayCollection $jobServiceTemplateCollection) {
        parent::__construct();

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
        $this->add(new Text('name'));
        
        // Durée
        $duration = new Select('duration');
        $duration->setValueOptions([
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
        $this->add($duration);
        
        // Description
        $this->add(new Text('description'));
        
        // Prestation de salon
        $selectSalonJobService = new Select('salon-job-service');
        
        // Remplissage des prestations du salon
        $selectSalonJobServiceValues = [];
        
        /* @var $jobServiceTemplate JobServiceTemplate */
        foreach ($this->jobServiceTemplateCollection as $jobServiceTemplate) {
            $jobServiceName = $jobServiceTemplate->getName() . ' - ' . $jobServiceTemplate->getPrice() . '€';
            $selectSalonJobServiceValues[$jobServiceTemplate->getIdJobServiceTemplate()] = $jobServiceName;
        }
        
        $selectSalonJobService->setValueOptions($selectSalonJobServiceValues);
        $this->add($selectSalonJobService);
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
