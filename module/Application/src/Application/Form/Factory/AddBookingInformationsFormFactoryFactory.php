<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */
namespace Application\Form\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AddBookingInformationsFormFactoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new AddBookingInformationsFormFactory(
            $serviceManager->get('Backend\Service\Invoice')
        );
    }
}
