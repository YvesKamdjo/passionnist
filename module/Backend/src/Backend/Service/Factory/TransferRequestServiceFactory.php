<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\TransferRequestService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TransferRequestServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new TransferRequestService(
            $serviceManager->get('Backend\Mapper\TransferRequest'),
            $serviceManager->get('Backend\Mapper\Transaction'),
            $serviceManager->get('Logger\Error')
        );
    }

}
