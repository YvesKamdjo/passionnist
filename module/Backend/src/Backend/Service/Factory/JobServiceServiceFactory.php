<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\JobServiceService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobServiceServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new JobServiceService(
            $serviceManager->get('Backend\Mapper\JobService'),
            $serviceManager->get('Backend\Mapper\JobServiceTemplate'),
            $serviceManager->get('Backend\Mapper\JobServiceType'),
            $serviceManager->get('Backend\Mapper\CustomerCharacteristic'),
            $serviceManager->get('Backend\Mapper\Salon'),
            $serviceManager->get('Backend\Mapper\Search'),
            $serviceManager->get('Backend\Mapper\Account'),
            $serviceManager->get('Logger\Error')
        );
    }
}
