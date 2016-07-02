<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Collection\ArrayCollection;
use Backend\Entity\JobServiceTemplate;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\I18n\Validator\IsFloat;
use Zend\InputFilter\Input;

class EditJobServiceTemplateForm extends Form
{
    private $jobServiceTemplate;
    private $jobServiceTemplateJobServiceTypeList;
    private $jobServiceTemplateCustomerCharacteristicList;
    private $jobServiceTypeCollection;
    private $customerCharacteristicCollection;

    /**
     * Instancie un formulaire d'édition
     */
    public function __construct(
        JobServiceTemplate $jobServiceTemplate,
        $jobServiceJobServiceTypeList,
        $jobServiceCustomerCharacteristicList,
        ArrayCollection $jobServiceTypeCollection,
        ArrayCollection $customerCharacteristicCollection
    ) {
        parent::__construct();

        $this->jobServiceTemplate = $jobServiceTemplate;
        $this->jobServiceTemplateJobServiceTypeList = $jobServiceJobServiceTypeList;
        $this->jobServiceTemplateCustomerCharacteristicList = $jobServiceCustomerCharacteristicList;
        $this->jobServiceTypeCollection = $jobServiceTypeCollection;
        $this->customerCharacteristicCollection = $customerCharacteristicCollection;

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
        $nameElement->setValue($this->jobServiceTemplate->getName());
        $this->add($nameElement);
        
        // Prix
        $priceElement = new Text('price');
        $priceElement->setValue($this->jobServiceTemplate->getPrice());
        $this->add($priceElement);
        
        // Type de prestation
        $jobServiceTypeList = [];
        
        /* @var \Backend\Entity\JobServiceType $jobServiceType */
        foreach ($this->jobServiceTypeCollection as $jobServiceType) {
            $jobServiceTypeList[$jobServiceType->getIdJobServiceType()] = $jobServiceType->getName();
        }
        
        $jobServiceTypeElement = new MultiCheckbox('jobServiceType');
        $jobServiceTypeElement->setValueOptions($jobServiceTypeList);
        $jobServiceTypeElement->setValue($this->jobServiceTemplateJobServiceTypeList);
        $this->add($jobServiceTypeElement);
        
        // Caractéristique d'utilisateur
        $customerCharacteristicList = [];
        
        /* @var \Backend\Entity\CustomerCharacteristic $customerCharacteristic */
        foreach ($this->customerCharacteristicCollection as $customerCharacteristic) {
            $customerCharacteristicList[$customerCharacteristic->getIdCustomerCharacteristic()] = $customerCharacteristic->getName();
        }
        
        $customerCharacteristicElement = new MultiCheckbox('customerCharacteristic');
        $customerCharacteristicElement->setValueOptions($customerCharacteristicList);
        $customerCharacteristicElement->setValue($this->jobServiceTemplateCustomerCharacteristicList);
        $this->add($customerCharacteristicElement);
        
        // Répercuter la suppression sur les prestations
        $this->add(new Checkbox('synchronize-job-service'));
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Nom
        $nameInput = new Input('name');
        $nameInput->setRequired(true);
        
        // Prix
        $priceInput = new Input('price');
        $priceInput->setRequired(true);
        $priceInput->getValidatorChain()
                ->attach(new IsFloat());

        // Type de prestation
        $jobServiceTypeInput = new Input('jobServiceType');
        $jobServiceTypeInput->setRequired(false);
        
        // Caractéristique d'utilisateur
        $customerCharacteristicInput = new Input('customerCharacteristic');
        $customerCharacteristicInput->setRequired(false);
        
        // Répercuter la suppression sur les prestations
        $synchronizeJobServiceInput = new Input('synchronize-job-service');
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($nameInput);
        $inputFilter->add($priceInput);
        $inputFilter->add($jobServiceTypeInput);
        $inputFilter->add($customerCharacteristicInput);
        $inputFilter->add($synchronizeJobServiceInput);
    }
}
