<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ProfessionnalSignUpFormFactoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new ProfessionnalSignUpFormFactory(
            $serviceManager->get('Backend\Service\AccountType'),
            $serviceManager->get('Backend\Service\Referral')
        );
    }
}
