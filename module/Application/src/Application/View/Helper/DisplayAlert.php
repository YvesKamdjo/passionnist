<?php

/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DisplayAlert extends AbstractHelper
{
    /**
     * Message de type succès
     */
    const SUCCESS = 1;
    /**
     * Message d'information
     */
    const INFO = 2;
    /**
     * Message d'avertissement
     */
    const WARNING = 3;
    /**
     * Message d'erreur
     */
    const ERROR = 4;
    
    /**
     * 
     */
    public function __invoke($message, $style = self::WARNING, $isDismissible = true)
    {
        $alertClass = 'alert-warning';
        $dismissButton = '<button type="button" class="close" data-dismiss="alert"><i class="material-icons">&#xE5CD;</i></button>';
        
        switch ($style) {
            case self::SUCCESS:
                $alertClass = 'alert-success';
                break;
            
            case self::INFO:
                $alertClass = 'alert-info';
                break;
            
            case self::ERROR:
                $alertClass = 'alert-error';
                break;
                
            default:
                $alertClass = 'alert-warning';
                break;
        }
        
        if ($isDismissible === true) {
            $alertClass .= ' alert-dismissible';
        }
        else {
            $dismissButton = '';
        }
        
        return sprintf(
            '<div class="alert %2$s">
                %3$s %1$s
            </div>',
            $message,
            $alertClass,
            $dismissButton
        );
    }
}
