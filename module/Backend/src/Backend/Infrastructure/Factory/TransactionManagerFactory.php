<?php
/**
 * @package omictools
 * @author contact@wixiweb.fr
 */

namespace Backend\Infrastructure\Factory;

use Backend\Infrastructure\TransactionManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TransactionManagerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return TransactionManager
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new TransactionManager($serviceManager->get('db'));
    }
}
