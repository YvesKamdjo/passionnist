<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\JobServiceTypeService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobServiceTypeServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new JobServiceTypeService(
                $serviceManager->get('Backend\Mapper\JobServiceType'), $serviceManager->get('Logger\Error')
        );
    }
}
