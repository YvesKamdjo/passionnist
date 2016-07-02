<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\AttachmentRequestService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AttachmentRequestServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new AttachmentRequestService(
            $serviceManager->get('Backend\Mapper\AttachmentRequest'),
            $serviceManager->get('Backend\Mapper\Salon'),
            $serviceManager->get('Backend\Mapper\Account'),
            $serviceManager->get('Backend\Mapper\Permission'),
            $serviceManager->get('Logger\Error')
        );
    }
}
