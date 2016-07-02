<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Entity\Salon;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class ProfessionnalEditSalonForm extends Form
{    
    
    private $salon;
    
    /**
     * Instancie un formulaire de création de salon
     */
    public function __construct(Salon $salon)
    {
        parent::__construct();
        
        $this->salon = $salon;
        
        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Nom du salon
        $nameElement = new Text('name');
        $nameElement->setValue($this->salon->getName());
        $this->add($nameElement);
        
        // Adresse
        $addressElement = new Hidden('address');
        $addressElement->setValue($this->salon->getAddress());
        $this->add($addressElement);
        
        // Code postal
        $zipcodeElement = new Hidden('zipcode');
        $zipcodeElement->setValue($this->salon->getZipcode());
        $this->add($zipcodeElement);

        // Ville
        $cityElement = new Hidden('city');
        $cityElement->setValue($this->salon->getCity());
        $this->add($cityElement);
        
        // Latitude
        $latitudeElement = new Hidden('latitude');
        $latitudeElement->setValue($this->salon->getLatitude());
        $this->add($latitudeElement);

        // Longitude
        $longitudeElement = new Hidden('longitude');
        $longitudeElement->setValue($this->salon->getLongitude());
        $this->add($longitudeElement);
        
        // idSalon
        $idSalonElement = new Text('idSalon');
        $idSalonElement->setValue($this->salon->getIdSalon());
        $this->add($idSalonElement);
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
        
        // idSalon
        $idSalonInput = new Input('idSalon');
        $idSalonInput->setRequired(true);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($nameInput);
        $inputFilter->add($addressInput);
        $inputFilter->add($zipcodeInput);
        $inputFilter->add($cityInput);
        $inputFilter->add($latitudeInput);
        $inputFilter->add($longitudeInput);
        $inputFilter->add($idSalonInput);
    }
}
