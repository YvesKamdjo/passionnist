<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Finder\Factory;

use Notification\Finder\NewProspectsNotificationFinder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NewProspectsNotificationFinderFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceManager
     * @return NewProspectsNotificationFinder
     */
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new NewProspectsNotificationFinder(
            $serviceManager->get('db')
        );
    }
}
