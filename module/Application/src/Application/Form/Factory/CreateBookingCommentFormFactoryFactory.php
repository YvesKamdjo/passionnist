<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */
namespace Application\Form\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CreateBookingCommentFormFactoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new CreateBookingCommentFormFactory(
            $serviceManager->get('Backend\Service\BookingComment')
        );
    }
}
