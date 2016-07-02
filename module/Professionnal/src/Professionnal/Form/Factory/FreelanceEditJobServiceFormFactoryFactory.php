<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FreelanceEditJobServiceFormFactoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new FreelanceEditJobServiceFormFactory(
            $serviceManager->get('Backend\Service\JobService'),
            $serviceManager->get('Backend\Service\JobServiceType'),
            $serviceManager->get('Backend\Service\CustomerCharacteristic')
        );
    }
}
