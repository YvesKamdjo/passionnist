<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\PaymentService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PaymentServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $config = $serviceManager->get('config');
        
        return new PaymentService(
            $serviceManager->get('Backend\Service\Invoice'),
            $serviceManager->get('Backend\Mapper\Payment'),
            $serviceManager->get('Backend\Mapper\Booking'),
            $serviceManager->get('Backend\Mapper\Transaction'),
            $serviceManager->get('Backend\Mapper\JobService'),
            $serviceManager->get('Backend\Mapper\Salon'),
            $config['payment'],
            $config['application'],
            $serviceManager->get('Logger\Error')
        );
    }
}
