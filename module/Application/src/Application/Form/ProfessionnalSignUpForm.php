<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Backend\Collection\ArrayCollection;
use Backend\Entity\AccountType;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Email;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Password;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;

class ProfessionnalSignUpForm extends Form
{
    private $accountTypeCollection;
    private $referralCollection;
    private $sentData;
    
    /**
     * Instancie un formulaire d'inscription
     */
    public function __construct(
        array $sentData,
        ArrayCollection $accountTypeCollection, 
        ArrayCollection $referralCollection
    ) {
        parent::__construct();
        
        $this->sentData = $sentData;
        $this->accountTypeCollection = $accountTypeCollection;
        $this->referralCollection = $referralCollection;

        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Nom
        $lastNameElement = new Text('last-name');
        if (isset($this->sentData['lastName'])) {
            $lastNameElement->setValue($this->sentData['lastName']);
        }
        $this->add($lastNameElement);
        
        // Prénom
        $firstNameElement = new Text('first-name');
        if (isset($this->sentData['firstName'])) {
            $firstNameElement->setValue($this->sentData['firstName']);
        }
        $this->add($firstNameElement);
        
        // Email
        $emailElement = new Email('email');
        if (isset($this->sentData['email'])) {
            $emailElement->setValue($this->sentData['email']);
        }
        $this->add($emailElement);

        // Téléphone
        $phoneElement = new Text('phone');
        if (isset($this->sentData['phone'])) {
            $phoneElement->setValue($this->sentData['phone']);
        }
        $this->add($phoneElement);
        
        // Mot de passe
        $this->add(new Password('password'));

        // CGU
        $this->add(new Checkbox('terms'));
        
        // Type de compte
        $selectAccountType = new Select('account-type');
        
        // Remplissage du type de compte
        $selectAccountTypeValues = [[
            'value' => null,
            'label' => 'Choisissez un statut',
            'disabled' => 'disabled',
            'selected' => 'selected'
        ]];
        /* @var $accountType AccountType */
        foreach ($this->accountTypeCollection as $accountType) {
            // Suppression de la liste du type client
            if ($accountType->getIdAccountType() == AccountType::ACCOUNT_TYPE_CUSTOMER
                || $accountType->getIdAccountType() == AccountType::ACCOUNT_TYPE_ADMIN
            ) {
                continue;
            }
            
            $selectAccountTypeValues[$accountType->getIdAccountType()] = ucfirst($accountType->getKey());
        }
        
        $selectAccountType->setValueOptions($selectAccountTypeValues);
        if (isset($this->sentData['account-type'])) {
            $selectAccountType->setValue($this->sentData['account-type']);
        }
        $this->add($selectAccountType);
        
        // Adresse
        $addressElement = new Hidden('address');
        $this->add($addressElement);
        
        // Code postal
        $zipcodeElement = new Hidden('zipcode');
        $this->add($zipcodeElement);

        // Ville
        $cityElement = new Hidden('city');
        $this->add($cityElement);
        
        // Latitude
        $latitudeElement = new Hidden('latitude');
        $this->add($latitudeElement);

        // Longitude
        $longitudeElement = new Hidden('longitude');
        $this->add($longitudeElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {        
        // Nom
        $lastNameInput = new Input('last-name');
        $lastNameInput->setRequired(true);
        
        // Prénom
        $firstNameInput = new Input('first-name');
        $firstNameInput->setRequired(true);
        
        // Email
        $emailInput = new Input('email');
        $emailInput->setRequired(true);
        $emailInput->getValidatorChain()
                ->attach(new EmailAddress());
        
        // Téléphone
        $phoneInput = new Input('phone');
        $phoneInput->setRequired(true);
        
        // Mot de passe
        $passwordInput = new Input('password');
        $passwordInput->setRequired(true);
        $passwordInput->getValidatorChain()
                ->attach(new StringLength(array('min' => 6)));

        // CGU
        $termsInput = new Input('terms');
        $termsInput->getValidatorChain()
            ->attach(new Identical('1'));
        $termsInput->setErrorMessage("Veuillez lire et accepter les CGU");
        
        // Type de compte
        $accountTypeInput = new Input('account-type');
        $accountTypeInput->setRequired(true);
        
        // Adresse
        $addressInput = new Input('address');
        $addressInput->setRequired(true);

        // Code postal
        $zipcodeInput = new Input('zipcode');
        $zipcodeInput->setRequired(true);
        
        // Ville
        $cityInput = new Input('city');
        $cityInput->setRequired(true);
        
        // Latitude
        $latitudeInput = new Input('latitude');
        $latitudeInput->setRequired(true);
        
        // Longitude
        $longitudeInput = new Input('longitude');
        $longitudeInput->setRequired(true);
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($lastNameInput);
        $inputFilter->add($firstNameInput);
        $inputFilter->add($emailInput);
        $inputFilter->add($phoneInput);
        $inputFilter->add($passwordInput);
        $inputFilter->add($termsInput);
        $inputFilter->add($accountTypeInput);
        $inputFilter->add($addressInput);
        $inputFilter->add($zipcodeInput);
        $inputFilter->add($cityInput);
        $inputFilter->add($latitudeInput);
        $inputFilter->add($longitudeInput);
    }
}
