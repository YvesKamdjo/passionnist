<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\StringLength;

class NewPasswordForm extends Form
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
        $this->add(new Text('password'));
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Password
        $passwordInput = new Input('password');
        $passwordInput->setRequired(true);
        $passwordInput->getValidatorChain()
                ->attach(new StringLength(array('min' => 6)));

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($passwordInput);
    }
}
