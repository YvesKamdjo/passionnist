<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\File;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class UploadProfessionnalQualificationForm extends Form
{
    /**
     * Instancie un formulaire d'édition
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
        // Diplôme
        $qualificationElement = new File('qualification');
        $this->add($qualificationElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Diplôme
        $qualificationInput = new Input('qualification');

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($qualificationInput);
    }
}
