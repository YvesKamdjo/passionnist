<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmployeeEditJobServiceFormFactoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new EmployeeEditJobServiceFormFactory(
            $serviceManager->get('Backend\Service\JobService'),
            $serviceManager->get('Backend\Service\Salon'),
            $serviceManager->get('Backend\Service\JobServiceTemplate')
        );
    }
}
