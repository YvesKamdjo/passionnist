<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Administration\Form\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EditAccountFormFactoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new EditAccountFormFactory(
            $serviceManager->get('Backend\Service\Account')
        );
    }
}
