<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\File;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class UploadProfessionnalAccountImageForm extends Form
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
        // Avatar
        $accountImageElement = new File('account-image');
        $this->add($accountImageElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Nom
        $accountImageInput = new Input('account-image');

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($accountImageInput);
    }
}
