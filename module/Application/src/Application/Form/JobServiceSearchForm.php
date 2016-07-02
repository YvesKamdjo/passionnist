<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\AccountType;
use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobServiceType;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class JobServiceSearchForm extends Form
{
    private $account;
    private $jobServiceTypeCollection;
    private $customerCharacteristicCollection;
    
    /**
     * Instancie un formulaire d'inscription
     */
    public function __construct(
        Account $account,
        ArrayCollection $jobServiceTypeCollection,
        ArrayCollection $customerCharacteristicCollection
    ) {
        parent::__construct();
        
        $this->account = $account;
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
        // Date
        $dateElement = new Text('date');
        $this->add($dateElement);
        
        $dateHiddenElement = new Hidden('date_hidden');
        $this->add($dateHiddenElement);
        
        // Minimum de likes
        $minLikeElement = new Text('minLike');
        $this->add($minLikeElement);
        
        // Minimum de note
        $minRateElement = new Select('minRate');
        $minRateElement->setEmptyOption('Note minimum');
        $minRateElement->setValueOptions(array(
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
        ));
        $this->add($minRateElement);
        
        // Maximum de prix
        $maxPriceElement = new Text('maxPrice');
        $this->add($maxPriceElement);

        // Adresse
        $addressElement = new Text('address');
        $addressElement->setValue($this->account->getAddress());
        $this->add($addressElement);
        
        // Latitude
        $latitudeElement = new Hidden('latitude');
        $latitudeElement->setValue($this->account->getLatitude());
        $this->add($latitudeElement);

        // Longitude
        $longitudeElement = new Hidden('longitude');
        $longitudeElement->setValue($this->account->getLongitude());
        $this->add($longitudeElement);
        
        // Types de prestation
        $jobServiceTypeList = [];
        
        /* @var $jobServiceType JobServiceType */
        foreach ($this->jobServiceTypeCollection as $jobServiceType) {
            $jobServiceTypeList[$jobServiceType->getIdJobServiceType()] = $jobServiceType->getName();
        }
        
        $jobSeviceTypeElement = new MultiCheckbox('idJobServiceType');
        $jobSeviceTypeElement->setValueOptions($jobServiceTypeList);
        $this->add($jobSeviceTypeElement);
        
        // Caractéristique d'utilisateur
        $customerCharacteristicList = [];
        
        /* @var $customerCharacteristic CustomerCharacteristic */
        foreach ($this->customerCharacteristicCollection as $customerCharacteristic) {
            $customerCharacteristicList[$customerCharacteristic->getIdCustomerCharacteristic()] = $customerCharacteristic->getName();
        }
        
        $customerCharacteristicElement = new MultiCheckbox('idCustomerCharacteristic');
        $customerCharacteristicElement->setValueOptions($customerCharacteristicList);
        $this->add($customerCharacteristicElement);
        
        // Type de professionnel
        $professionalTypeElement = new MultiCheckbox('idAccountType');
        $professionalTypeElement->setValueOptions([
            AccountType::ACCOUNT_TYPE_EMPLOYEE => "En salon",
            AccountType::ACCOUNT_TYPE_FREELANCE => "À domicile",
        ]);
        $this->add($professionalTypeElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        $dateInput = new Input('date');
        $dateInput->setRequired(false);
        
        $minLikeInput = new Input('minLike');
        $minLikeInput->setRequired(false);
        
        $minRateInput = new Input('minRate');
        $minRateInput->setRequired(false);
        
        $maxPriceInput = new Input('maxPrice');
        $maxPriceInput->setRequired(false);
        
        $jobServiceTypeInput = new Input('idJobServiceType');
        $jobServiceTypeInput->setRequired(false);
        
        $customerCharacteristicInput = new Input('idCustomerCharacteristic');
        $customerCharacteristicInput->setRequired(false);
        
        $professionalTypeInput = new Input('idAccountType');
        $professionalTypeInput->setRequired(false);
        
        $addressInput = new Input('address');
        $addressInput->setRequired(false);
        
        $latitudeInput = new Input('latitude');
        $latitudeInput->setRequired(false);
        
        $longitudeInput = new Input('longitude');
        $longitudeInput->setRequired(false);
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($dateInput);
        $inputFilter->add($minLikeInput);
        $inputFilter->add($minRateInput);
        $inputFilter->add($maxPriceInput);
        $inputFilter->add($jobServiceTypeInput);
        $inputFilter->add($customerCharacteristicInput);
        $inputFilter->add($professionalTypeInput);
        $inputFilter->add($addressInput);
        $inputFilter->add($latitudeInput);
        $inputFilter->add($longitudeInput);
    }
}
