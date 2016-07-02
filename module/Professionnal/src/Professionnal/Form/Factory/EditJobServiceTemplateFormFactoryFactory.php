<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditJobServiceTemplateFormFactoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new EditJobServiceTemplateFormFactory(
            $serviceManager->get('Backend\Service\JobServiceTemplate'),
            $serviceManager->get('Backend\Service\JobServiceType'),
            $serviceManager->get('Backend\Service\CustomerCharacteristic')
        );
    }
}
