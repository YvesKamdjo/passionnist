<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\NewsletterService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NewsletterServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new NewsletterService(
            $serviceManager->get('Backend\Mapper\Newsletter'),
            $serviceManager->get('Logger\Error')
        );
    }
}
