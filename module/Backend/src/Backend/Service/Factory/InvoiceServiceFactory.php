<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\InvoiceService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InvoiceServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new InvoiceService(
            $serviceManager->get('Backend\Mapper\Invoice'),
            $serviceManager->get('Backend\Mapper\Booking'),
            $serviceManager->get('Logger\Error')
        );
    }
}
