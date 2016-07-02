<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\SalonConfiguratorService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SalonConfiguratorServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new SalonConfiguratorService(
            $serviceManager->get('Backend\Mapper\JobServiceTemplate'),
            $serviceManager->get('Logger\Error')
        );
    }
}
