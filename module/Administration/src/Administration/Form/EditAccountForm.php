<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Administration\Form;

use Backend\Entity\Account;
use Zend\Form\Element\Email;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\EmailAddress;

class EditAccountForm extends Form
{
    private $account;

    /**
     * Instancie un formulaire d'édition
     */
    public function __construct(Account $account)
    {
        parent::__construct();

        $this->account = $account;

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
        $lastNameElement->setValue($this->account->getLastName());
        $this->add($lastNameElement);

        // Prénom
        $firstNameElement = new Text('first-name');
        $firstNameElement->setValue($this->account->getFirstName());
        $this->add($firstNameElement);

        // Email
        $emailElement = new Email('email');
        $emailElement->setValue($this->account->getEmail());
        $this->add($emailElement);

        // Téléphone
        $phoneElement = new Text('phone');
        $phoneElement->setValue($this->account->getPhone());
        $this->add($phoneElement);
        
        // Adresse
        $addressElement = new Text('address');
        $addressElement->setValue($this->account->getAddress());
        $this->add($addressElement);
        
        // Code postal
        $zipcodeElement = new Text('zipcode');
        $zipcodeElement->setValue($this->account->getZipcode());
        $this->add($zipcodeElement);
        
        // Ville
        $cityElement = new Text('city');
        $cityElement->setValue($this->account->getCity());
        $this->add($cityElement);
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

        // Adresse
        $addressInput = new Input('address');
        $addressInput->setRequired(false);

        // Code postal
        $zipcodeInput = new Input('zipcode');
        $zipcodeInput->setRequired(false);
        
        // Ville
        $cityInput = new Input('city');
        $cityInput->setRequired(false);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($lastNameInput);
        $inputFilter->add($firstNameInput);
        $inputFilter->add($emailInput);
        $inputFilter->add($addressInput);
        $inputFilter->add($zipcodeInput);
        $inputFilter->add($cityInput);
    }
}
