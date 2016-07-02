<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\Checkbox;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class DeleteJobServiceTemplateConfirmForm extends Form
{    
    /**
     * Instancie un formulaire de suppression d'un template de prestation
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
        // Répercuter la suppression sur les prestations
        $this->add(new Checkbox('synchronize-job-service'));
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {        
        // Répercuter la suppression sur les prestations
        $synchronizeJobServiceInput = new Input('synchronize-job-service');
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($synchronizeJobServiceInput);
    }
}
