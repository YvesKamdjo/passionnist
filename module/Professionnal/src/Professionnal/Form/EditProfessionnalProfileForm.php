<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Entity\Account;
use Zend\Form\Element\Email;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\I18n\Validator\IsInt;
use Zend\InputFilter\Input;
use Zend\Validator\EmailAddress;

class EditProfessionnalProfileForm extends Form
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
        $addressElement = new Hidden('address');
        $addressElement->setValue($this->account->getAddress());
        $this->add($addressElement);
        
        // Code postal
        $zipcodeElement = new Hidden('zipcode');
        $zipcodeElement->setValue($this->account->getZipcode());
        $this->add($zipcodeElement);

        // Ville
        $cityElement = new Hidden('city');
        $cityElement->setValue($this->account->getCity());
        $this->add($cityElement);
        
        // Latitude
        $latitudeElement = new Hidden('latitude');
        $latitudeElement->setValue($this->account->getLatitude());
        $this->add($latitudeElement);

        // Longitude
        $longitudeElement = new Hidden('longitude');
        $longitudeElement->setValue($this->account->getLongitude());
        $this->add($longitudeElement);

        // Rayon de déplacement
        $moveRangeElement = new Text('move-range');
        $moveRangeElement->setValue($this->account->getMoveRange());
        $this->add($moveRangeElement);
        
        // Biographie
        $biographyElement = new Text('biography');
        $biographyElement->setValue($this->account->getBiography());
        $this->add($biographyElement);
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
        
        // Latitude
        $latitudeInput = new Input('latitude');
        $latitudeInput->setRequired(false);
        
        // Longitude
        $longitudeInput = new Input('longitude');
        $longitudeInput->setRequired(false);
        
        // Biographie
        $biographyInput = new Input('city');
        $biographyInput->setRequired(false);
        
        // Rayon de déplacement
        $moveRangeInput = new Input('move-range');
        $moveRangeInput->setRequired(false);
        $moveRangeInput->getValidatorChain()
                ->attach(new IsInt);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($lastNameInput);
        $inputFilter->add($firstNameInput);
        $inputFilter->add($emailInput);
        $inputFilter->add($addressInput);
        $inputFilter->add($zipcodeInput);
        $inputFilter->add($cityInput);
        $inputFilter->add($biographyInput);
        $inputFilter->add($moveRangeInput);
    }
}
