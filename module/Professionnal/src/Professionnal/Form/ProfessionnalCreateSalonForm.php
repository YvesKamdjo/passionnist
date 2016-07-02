<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\Identical;

class ProfessionnalCreateSalonForm extends Form
{    
    /**
     * Instancie un formulaire de création de salon
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Nom du salon
        $this->add(new Text('name'));
        
        // Adresse
        $this->add(new Hidden('address'));
        
        // Code postal
        $this->add(new Hidden('zipcode'));

        // Ville
        $this->add(new Hidden('city'));
        
        // Latitude
        $this->add(new Hidden('latitude'));

        // Longitude
        $this->add(new Hidden('longitude'));

        // CGU
        $this->add(new Checkbox('terms'));
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {        
        // Nom du salon
        $nameInput = new Input('name');
        $nameInput->setRequired(true);
        
        // Adresse
        $addressInput = new Input('address');
        $addressInput->setRequired(true);
        $addressInput->setErrorMessage("L'adresse est incomplète, veuillez être plus précis");
        
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
        
        // CGU
        $termsInput = new Input('terms');
        $termsInput->getValidatorChain()
            ->attach(new Identical('1'));
        $termsInput->setErrorMessage("Veuillez lire et accepter les CGU");
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($nameInput);
        $inputFilter->add($addressInput);
        $inputFilter->add($zipcodeInput);
        $inputFilter->add($cityInput);
        $inputFilter->add($latitudeInput);
        $inputFilter->add($longitudeInput);
        $inputFilter->add($termsInput);
    }
}
