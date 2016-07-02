<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\EmailAddress;

class PasswordLostForm extends Form
{

    /**
     * Instancie un formulaire d'authentification
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
        // Login
        $this->add(new Text('email'));
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Login
        $emailInput = new Input('email');
        $emailInput->setRequired(true);
        $emailInput->getValidatorChain()
                ->attach(new EmailAddress());
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($emailInput);
    }
}
