<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\SalonService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SalonServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new SalonService(
            $serviceManager->get('Backend\Mapper\Salon'),
            $serviceManager->get('Backend\Mapper\Account'),
            $serviceManager->get('Backend\Mapper\Permission'),
            $serviceManager->get('Backend\Mapper\AttachmentRequest'),
            $serviceManager->get('Backend\Service\SalonConfigurator'),
            $serviceManager->get('Application\Service\Email'),
            $serviceManager->get('Logger\Error')
        );
    }
}
