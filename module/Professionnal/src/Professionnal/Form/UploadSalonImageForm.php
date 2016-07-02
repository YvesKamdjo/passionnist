<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\File;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class UploadSalonImageForm extends Form
{
    /**
     * Instancie un formulaire de création
     */
    public function __construct() {
        parent::__construct();

        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Photo de salon
        $salonImageElement = new File('salon-image');
        $this->add($salonImageElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Photo de prestation
        $salonImageInput = new Input('salon-image');

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($salonImageInput);
    }
}
