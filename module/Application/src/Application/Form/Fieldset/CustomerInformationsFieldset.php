<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form\Fieldset;

use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class CustomerInformationsFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct();

        // Nom de facturation
        $customerNameElement = new Text('customerName');
        $customerNameElement->setAttribute('id', 'customer-name');
        $customerNameElement->setLabel('Nom prénom');
        $this->add($customerNameElement);
        
        // Adresse de facturation
        $customerAddress = new Text('customerAddress');
        $customerAddress->setAttribute('id', 'customer-address');
        $customerAddress->setLabel('Adresse');
        $this->add($customerAddress);
        
        // Code postal de facturation
        $customerZipcode = new Text('customerZipcode');
        $customerZipcode->setAttribute('id', 'customer-zipcode');
        $customerZipcode->setLabel('Code postal');
        $this->add($customerZipcode);

        // Ville de facturation
        $customerCity = new Text('customerCity');
        $customerCity->setAttribute('id', 'customer-city');
        $customerCity->setLabel('Ville');
        $this->add($customerCity);
    }

    public function getInputFilterSpecification()
    {
        return array(
            'customerName' => array(
                'required' => true,
            ),
            'customerAddress' => array(
                'required' => true,
            ),
            'customerZipcode' => array(
                'required' => true,
            ),
            'customerCity' => array(
                'required' => true,
            ),
        );
    }
}
