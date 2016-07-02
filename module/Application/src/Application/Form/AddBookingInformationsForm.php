<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Application\Form\Fieldset\BillingInformationsFieldset;
use Application\Form\Fieldset\CustomerInformationsFieldset;
use Backend\Entity\Invoice;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Collection;
use Zend\Form\Element\Hidden;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class AddBookingInformationsForm extends Form
{    
    /**
     * @var Invoice
     */
    private $lastInvoice;
    
    private $expectedDate;
    
    /**
     * Instancie un formulaire d'inscription
     */
    public function __construct(
        Invoice $lastInvoice,
        $expectedDate
    ) {
        parent::__construct();
        
        $this->lastInvoice = $lastInvoice;
        $this->expectedDate = $expectedDate;

        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        $customerInformations = new Collection('customerInformations');
        $customerInformations->setCount(1)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new CustomerInformationsFieldset());

        // Peuplement du fieldset
        $customerInformationsTargetElement = $customerInformations->getTargetElement();
        $customerInformationsElements = $customerInformationsTargetElement->getElements();
        
        $customerInformationsElements['customerName']->setValue($this->lastInvoice->getCustomerName());
        $customerInformationsElements['customerAddress']->setValue($this->lastInvoice->getCustomerAddress());
        $customerInformationsElements['customerZipcode']->setValue($this->lastInvoice->getCustomerZipcode());
        $customerInformationsElements['customerCity']->setValue($this->lastInvoice->getCustomerCity());
        
        $this->add($customerInformations);
        
        $billingInformations = new Collection('billingInformations');
        $billingInformations->setCount(1)
            ->setAllowAdd(false)
            ->setShouldCreateTemplate(false)
            ->setTargetElement(new BillingInformationsFieldset());
        
        // Peuplement du fieldset
        $billingInformationsTargetElement = $billingInformations->getTargetElement();
        $billingInformationsElements = $billingInformationsTargetElement->getElements();
        
        $billingInformationsElements['billingName']->setValue($this->lastInvoice->getBillingName());
        $billingInformationsElements['billingAddress']->setValue($this->lastInvoice->getBillingAddress());
        $billingInformationsElements['billingZipcode']->setValue($this->lastInvoice->getBillingZipcode());
        $billingInformationsElements['billingCity']->setValue($this->lastInvoice->getBillingCity());
        
        $this->add($billingInformations);
        
        // Date de prestation demandée
        $expectedDateElement = new Hidden('expectedDate');
        $expectedDateElement->setValue($this->expectedDate);
        $this->add($expectedDateElement);
        
        // Utilisation d'une autre adresse pour la prestation et la facturation
        $this->add(new Checkbox('otherAddress'));
    }
    
    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {        
        // Date de prestation demandée
        $expectedDateInput = new Input('expectedDate');
        $expectedDateInput->setRequired(true);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($expectedDateInput);
    }
}
