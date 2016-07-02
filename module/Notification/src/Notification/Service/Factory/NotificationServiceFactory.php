<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Service\Factory;

use Notification\Service\NotificationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NotificationServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new NotificationService(
            $serviceManager->get('Notification\Finder\NewProspectsNotification'),
            $serviceManager->get('Notification\Finder\NewBookingNotification'),
            $serviceManager->get('Notification\Mapper\Notification'),
            $serviceManager->get('Application\Service\Email'),
            $serviceManager->get('Logger\Error')
        );
    }
}
