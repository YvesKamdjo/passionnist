<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\Email;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\Validator\EmailAddress;

class ProfessionnalJoinSalonForm extends Form
{
    /**
     * Instancie un formulaire permettant de rejoindre un salon
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
        // Adresse email du gérant
        $managerEmailElement = new Email('manager-email');
        $this->add($managerEmailElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // Nom
        $managerEmailInput = new Input('manager-email');
        $managerEmailInput->setRequired(true);
        $managerEmailInput->getValidatorChain()
                ->attach(new EmailAddress());

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($managerEmailInput);
    }
}
