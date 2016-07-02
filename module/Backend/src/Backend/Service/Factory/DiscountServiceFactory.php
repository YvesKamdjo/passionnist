<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\DiscountService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DiscountServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new DiscountService(
            $serviceManager->get('Backend\Mapper\Discount'), 
            $serviceManager->get('Backend\Mapper\Salon'), 
            $serviceManager->get('Logger\Error')
        );
    }
}
