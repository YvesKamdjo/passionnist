<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper\Factory;

use Backend\Mapper\AccountTypeMapper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountTypeMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new AccountTypeMapper(
                $serviceManager->get('db')
        );
    }
}
