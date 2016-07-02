<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\AvailabilityService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AvailabilityServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new AvailabilityService(
            $serviceManager->get('Backend\Mapper\Availability'),
            $serviceManager->get('Backend\Mapper\WeekTemplate'),
            $serviceManager->get('Logger\Error')
        );
    }
}
