<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\FacebookAccountService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FacebookAccountServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new FacebookAccountService(
            $serviceManager->get('Backend\Mapper\FacebookAccount'),
            $serviceManager->get('Logger\Error')
        );
    }
}
