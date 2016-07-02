<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\JobServiceImageService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobServiceImageServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new JobServiceImageService(
            $serviceManager->get('Backend\Mapper\JobServiceImage'),
            $serviceManager->get('Logger\Error')
        );
    }
}
