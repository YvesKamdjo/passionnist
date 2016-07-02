<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper\Factory;

use Backend\Mapper\FacebookAccountMapper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FacebookAccountMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $config = $serviceManager->get('config');
        
        return new FacebookAccountMapper(
                $serviceManager->get('db'),
                $config['facebook']
        );
    }
}
