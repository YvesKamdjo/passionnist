<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Collection\ArrayCollection;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\I18n\Validator\IsFloat;
use Zend\I18n\Validator\IsInt;
use Zend\InputFilter\Input;

class FreelanceCreateJobServiceForm extends Form
{
    private $jobServiceTypeCollection;
    private $customerCharacteristicCollection;

    /**
     * Instancie un formulaire de création
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
        $customerCharacteristicValuesList = [];
        
        /* @var $customerCharacteristic \Backend\Entity\CustomerCharacteristic */
        foreach ($this->customerCharacteristicCollection as $customerCharacteristic) {
            $customerCharacteristicList[$customerCharacteristic->getIdCustomerCharacteristic()] = $customerCharacteristic->getName();
            $customerCharacteristicValuesList[] = $customerCharacteristic->getIdCustomerCharacteristic();
        }
        
        $customerCharacteristicElement = new MultiCheckbox('customerCharacteristic');
        $customerCharacteristicElement->setValueOptions($customerCharacteristicList);
        $customerCharacteristicElement->setValue($customerCharacteristicValuesList);
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
        
        // Durée
        $durationInput = new Input('duration');
        $durationInput->setRequired(true);
        $durationInput->getValidatorChain()
                ->attach(new IsInt());
        
        // Description
        $descriptionInput = new Input('description');

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
        $inputFilter->add($durationInput);
        $inputFilter->add($descriptionInput);
        $inputFilter->add($priceInput);
        $inputFilter->add($jobServiceTypeInput);
        $inputFilter->add($customerCharacteristicInput);
    }
}
