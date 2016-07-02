<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Finder\Factory;

use Notification\Finder\NewBookingNotificationFinder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NewBookingNotificationFinderFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return NewBookingNotificationFinder
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new NewBookingNotificationFinder(
            $serviceManager->get('db')
        );
    }
}
