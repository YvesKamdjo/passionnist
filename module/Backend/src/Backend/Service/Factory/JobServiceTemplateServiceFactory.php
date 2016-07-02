<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\JobServiceTemplateService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobServiceTemplateServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new JobServiceTemplateService(
            $serviceManager->get('Backend\Mapper\JobServiceTemplate'),
            $serviceManager->get('Backend\Mapper\Salon'),
            $serviceManager->get('Backend\Mapper\JobService'),
            $serviceManager->get('Logger\Error')
        );
    }
}
