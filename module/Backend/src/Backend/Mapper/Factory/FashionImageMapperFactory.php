<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper\Factory;

use Backend\Mapper\FashionImageMapper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FashionImageMapperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $config = $serviceManager->get('config');
        
        return new FashionImageMapper(
            $serviceManager->get('db'),
            $config['pinterest']
        );
    }
}
