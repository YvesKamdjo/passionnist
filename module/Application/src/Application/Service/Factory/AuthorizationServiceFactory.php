<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Service\Factory;

use Application\Service\AuthorizationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthorizationServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new AuthorizationService(
            $serviceManager->get('Backend\Mapper\Permission'),
            $serviceManager->get('Logger\Error')
        );
    }
}
