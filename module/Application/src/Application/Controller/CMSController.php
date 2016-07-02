<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CMSController extends AbstractActionController
{    
    public function legalAction()
    {
        return new ViewModel();
    }
}