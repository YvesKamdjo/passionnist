<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\WeekTemplateService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WeekTemplateServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new WeekTemplateService(
            $serviceManager->get('Backend\Mapper\WeekTemplate'),
                                 $serviceManager->get('Logger\Error')
        );
    }

}
