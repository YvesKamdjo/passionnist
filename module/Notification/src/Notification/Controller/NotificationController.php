<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Controller;

use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ConsoleModel;

class NotificationController extends AbstractActionController
{
    /**
     * Notification aux prospects qui n'ont pas encore été notifiés de leur
     * pré-inscription
     * 
     * @return ConsoleModel
     */
    public function notifyNewProspectAction()
    {
        try {
            $this->getServiceLocator()
                ->get('Notification\Service\Notification')
                ->notifyNewProspects();
        }
        catch (Exception $exception) {
            $logger = $this->getServiceLocator()
                ->get('Logger\Error');
            $logger->crit($exception);
            
            $viewModel = new ConsoleModel();
            $viewModel->setErrorLevel($exception->getCode());
            return $viewModel;
        }
        
        $viewModel = new ConsoleModel();
        $viewModel->setErrorLevel(0);
        return $viewModel;
    }
    
    /**
     * Notification aux professionnels qui n'ont pas encore été notifiés d'une
     * nouvelle réservation
     * 
     * @return ConsoleModel
     */
    public function notifyProfessionalNewBookingAction()
    {
        try {
            $this->getServiceLocator()
                ->get('Notification\Service\Notification')
                ->notifyProfessionalNewBooking();
        }
        catch (Exception $exception) {
            $logger = $this->getServiceLocator()
                ->get('Logger\Error');
            $logger->crit($exception);
            
            $viewModel = new ConsoleModel();
            $viewModel->setErrorLevel($exception->getCode());
            return $viewModel;
        }
        
        $viewModel = new ConsoleModel();
        $viewModel->setErrorLevel(0);
        return $viewModel;
    }
    
    /**
     * Notification aux clients qui n'ont pas encore été notifiés d'une
     * nouvelle réservation
     * 
     * @return ConsoleModel
     */
    public function notifyCustomerNewBookingAction()
    {
        try {
            $this->getServiceLocator()
                ->get('Notification\Service\Notification')
                ->notifyCustomerNewBooking();
        }
        catch (Exception $exception) {
            $logger = $this->getServiceLocator()
                ->get('Logger\Error');
            $logger->crit($exception);
            
            $viewModel = new ConsoleModel();
            $viewModel->setErrorLevel($exception->getCode());
            return $viewModel;
        }
        
        $viewModel = new ConsoleModel();
        $viewModel->setErrorLevel(0);
        return $viewModel;
    }
}