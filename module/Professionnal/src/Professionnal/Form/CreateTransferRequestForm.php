<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\I18n\Validator\IsFloat;
use Zend\InputFilter\Input;

class CreateTransferRequestForm extends Form
{    
    
    /**
     * Instancie un formulaire de demande de virement
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
        $amountElement = new Text('amount');
        $this->add($amountElement);
        
        $applicantIdentityElement = new Text('applicant-identity');
        $this->add($applicantIdentityElement);
        
        $ibanElement = new Text('iban');
        $this->add($ibanElement);
        
        $bicElement = new Text('bic');
        $this->add($bicElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {        
        // Montant du virement
        $amountInput = new Input('amount');
        $amountInput->setRequired(true);
        $amountInput->getValidatorChain()
                ->attach(new IsFloat());

        $applicantIdentityInput = new Input('applicant-identity');
        $applicantIdentityInput->setRequired(true);

        $ibanInput = new Input('iban');
        $ibanInput->setRequired(true);

        $bicInput = new Input('bic');
        $bicInput->setRequired(true);
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($amountInput);
        $inputFilter->add($applicantIdentityInput);
        $inputFilter->add($ibanInput);
        $inputFilter->add($bicInput);
    }
}
