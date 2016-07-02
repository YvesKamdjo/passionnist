<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form\Fieldset;

use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class BillingInformationsFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct();

        // Nom de facturation
        $billingNameElement = new Text('billingName');
        $billingNameElement->setAttribute('id', 'billing-name');
        $billingNameElement->setLabel('Nom prénom');
        $this->add($billingNameElement);
        
        // Adresse de facturation
        $billingAddress = new Text('billingAddress');
        $billingAddress->setAttribute('id', 'billing-address');
        $billingAddress->setLabel('Adresse');
        $this->add($billingAddress);
        
        // Code postal de facturation
        $billingZipcode = new Text('billingZipcode');
        $billingZipcode->setAttribute('id', 'billing-zipcode');
        $billingZipcode->setLabel('Code postal');
        $this->add($billingZipcode);

        // Ville de facturation
        $billingCity = new Text('billingCity');
        $billingCity->setAttribute('id', 'billing-city');
        $billingCity->setLabel('Ville');
        $this->add($billingCity);
    }

    public function getInputFilterSpecification()
    {
        return array(
            'billingName' => array(
                'required' => false,
            ),
            'billingAddress' => array(
                'required' => false,
            ),
            'billingZipcode' => array(
                'required' => false,
            ),
            'billingCity' => array(
                'required' => false,
            ),
        );
    }
}
