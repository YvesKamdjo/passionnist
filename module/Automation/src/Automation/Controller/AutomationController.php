<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Automation\Controller;

use Backend\Service\FashionImageService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ConsoleModel;

class AutomationController extends AbstractActionController
{
    /**
     * Notification aux prospects qui n'ont pas encore été notifiés de leur
     * pré-inscription
     * 
     * @return ConsoleModel
     */
    public function getNewFashionImagesAction()
    {
        /* @var $fashionImageService FashionImageService */
        $fashionImageService = $this->getServiceLocator()
            ->get('Backend\Service\FashionImage');
        
        $fashionImageService->updateFashionImages();
        
        $viewModel = new ConsoleModel();
        $viewModel->setErrorLevel(0);
        return $viewModel;
    }
}