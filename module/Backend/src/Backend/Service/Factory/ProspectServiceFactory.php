<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\ProspectService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProspectServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new ProspectService(
                $serviceManager->get('Backend\Mapper\Prospect'),
                $serviceManager->get('Application\Service\Email'),
                $serviceManager->get('Logger\Error')
        );
    }
}
