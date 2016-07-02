<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\View\Helper;

use Zend\Form\View\Helper\FormElementErrors;

class DisplayFormElementErrors extends FormElementErrors
{
    protected $messageCloseString     = '</div>';
    protected $messageOpenFormat      = '<div class="help-block">';
    protected $messageSeparatorString = '</div><div class="help-block">';
}