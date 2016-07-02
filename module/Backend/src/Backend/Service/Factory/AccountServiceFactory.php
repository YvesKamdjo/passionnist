<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\AccountService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new AccountService(
            $serviceManager->get('Backend\Mapper\Account'),
            $serviceManager->get('Backend\Mapper\Permission'),
            $serviceManager->get('Backend\Mapper\WeekTemplate'),
            $serviceManager->get('Application\Service\Email'),
            $serviceManager->get('Logger\Error')
        );
    }
}
