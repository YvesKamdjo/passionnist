<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Zend\Form\Element\Hidden;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class SetLocationForm extends Form
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
        $inputFilter->add($cityInput);
        $inputFilter->add($latitudeInput);
        $inputFilter->add($longitudeInput);
    }
}
