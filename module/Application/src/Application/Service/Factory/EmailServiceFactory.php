<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Service\Factory;

use Application\Service\EmailService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmailServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $config = $serviceManager->get('config');
        
        return new EmailService(
            $serviceManager->get('Zend\View\Renderer\RendererInterface'),
            $serviceManager->get('Logger\Error'),
            $config['mailer']
        );
    }
}
