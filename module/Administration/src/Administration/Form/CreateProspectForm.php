<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Administration\Form;

use Zend\Form\Element\Email;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\EmailAddress;

class CreateProspectForm extends Form
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
        // Nom
        $lastNameElement = new Text('last-name');
        $this->add($lastNameElement);

        // Prénom
        $firstNameElement = new Text('first-name');
        $this->add($firstNameElement);

        // Email
        $emailElement = new Email('email');
        $this->add($emailElement);

        // Téléphone
        $phoneElement = new Text('phone');
        $this->add($phoneElement);
        
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Nom
        $lastNameInput = new Input('last-name');
        $lastNameInput->setRequired(false);
        
        // Prénom
        $firstNameInput = new Input('first-name');
        $firstNameInput->setRequired(false);

        // Email
        $emailInput = new Input('email');
        $emailInput->setRequired(true);
        $emailInput->getValidatorChain()
                ->attach(new EmailAddress());

        // Téléphone
        $phoneInput = new Input('phone');
        $phoneInput->setRequired(false);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($lastNameInput);
        $inputFilter->add($firstNameInput);
        $inputFilter->add($emailInput);
        $inputFilter->add($phoneInput);
    }
}
