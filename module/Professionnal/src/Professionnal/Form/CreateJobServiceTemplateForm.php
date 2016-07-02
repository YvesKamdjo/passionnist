<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Collection\ArrayCollection;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\I18n\Validator\IsFloat;
use Zend\InputFilter\Input;

class CreateJobServiceTemplateForm extends Form
{
    private $jobServiceTypeCollection;
    private $customerCharacteristicCollection;

    /**
     * Instancie un formulaire d'édition
     */
    public function __construct(
        ArrayCollection $jobServiceTypeCollection,
        ArrayCollection $customerCharacteristicCollection
    ) {
        parent::__construct();

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
        $this->add(new Text('name'));
        
        // Prix
        $this->add(new Text('price'));
        
        // Type de prestation
        $jobServiceTypeList = [];
        
        /* @var \Backend\Entity\JobServiceType $jobServiceType */
        foreach ($this->jobServiceTypeCollection as $jobServiceType) {
            $jobServiceTypeList[$jobServiceType->getIdJobServiceType()] = $jobServiceType->getName();
        }
        
        $jobServiceTypeElement = new MultiCheckbox('jobServiceType');
        $jobServiceTypeElement->setValueOptions($jobServiceTypeList);
        $this->add($jobServiceTypeElement);
        
        // Caractéristique d'utilisateur
        $customerCharacteristicList = [];
        $customerCharacteristicValues = [];
        
        /* @var $customerCharacteristic \Backend\Entity\CustomerCharacteristic */
        foreach ($this->customerCharacteristicCollection as $customerCharacteristic) {
            $customerCharacteristicList[$customerCharacteristic->getIdCustomerCharacteristic()] = $customerCharacteristic->getName();
            $customerCharacteristicValues[] = $customerCharacteristic->getIdCustomerCharacteristic();
        }
        
        $customerCharacteristicElement = new MultiCheckbox('customerCharacteristic');
        $customerCharacteristicElement->setValueOptions($customerCharacteristicList);
        $customerCharacteristicElement->setValue($customerCharacteristicValues);
        $this->add($customerCharacteristicElement);
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
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($nameInput);
        $inputFilter->add($priceInput);
        $inputFilter->add($jobServiceTypeInput);
        $inputFilter->add($customerCharacteristicInput);
    }
}
