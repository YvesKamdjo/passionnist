<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service\Factory;

use Backend\Service\FashionImageService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FashionImageServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new FashionImageService(
            $serviceManager->get('Backend\Mapper\FashionImage'),
            $serviceManager->get('Logger\Error')
        );
    }
}
