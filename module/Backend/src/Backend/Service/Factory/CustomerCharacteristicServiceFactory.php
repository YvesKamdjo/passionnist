<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\CustomerCharacteristicService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CustomerCharacteristicServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new CustomerCharacteristicService(
                $serviceManager->get('Backend\Mapper\CustomerCharacteristic'), $serviceManager->get('Logger\Error')
        );
    }
}
