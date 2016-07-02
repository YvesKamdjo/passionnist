<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\BookingCommentService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BookingCommentServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new BookingCommentService(
            $serviceManager->get('Backend\Mapper\BookingComment'), 
            $serviceManager->get('Logger\Error')
        );
    }
}
