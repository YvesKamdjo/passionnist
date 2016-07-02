<?php namespace Application\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger;
use Zend\View\Helper\FlashMessenger;

class DisplayFlashMessages extends FlashMessenger
{
    /**
     * Templates for the open/close/separators for message tags
     *
     * @var string
     */
    protected $messageCloseString     = '</div>';
    protected $messageOpenFormat      = '<div %s><button type="button" class="close" data-dismiss="alert"><i class="material-icons">&#xE5CD;</i></button>';
    protected $messageSeparatorString = '<br />';
    
    public function __invoke($namespace = null)
    {
        $classes = ['alert', 'alert-dismissible'];
        
        switch ($namespace) {
            case PluginFlashMessenger::NAMESPACE_INFO:
                $classes[] = 'alert-info';
                break;
            
            case PluginFlashMessenger::NAMESPACE_ERROR:
                $classes[] = 'alert-danger';
                break;
            
            case PluginFlashMessenger::NAMESPACE_SUCCESS:
                $classes[] = 'alert-success';
                break;
            
            case PluginFlashMessenger::NAMESPACE_WARNING:
                $classes[] = 'alert-warning';
                break;
            
            default:
                $classes[] = 'alert-warning';
                break;
        }
        
        parent::__invoke($namespace);
        
        return $this->render($namespace, $classes);
    }
}
