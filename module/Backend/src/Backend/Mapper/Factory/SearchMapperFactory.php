<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper\Factory;

use Backend\Mapper\SearchMapper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        return new SearchMapper(
                $serviceManager->get('db')
        );
    }
}
