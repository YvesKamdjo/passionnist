<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Application\Exception\ServiceException;
use Backend\Service\BookingService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class BookingController extends AbstractActionController
{
    public function bookingListAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Booking
        /* @var $bookingService BookingService */
        $bookingService = $this->getServiceLocator()
            ->get('Backend\Service\Booking');
        
        $viewModel = new \Zend\View\Model\ViewModel();
        
        try {
            $bookingCollection = $bookingService->findByCustomerId($sessionContainer->account->getIdAccount());
            
            $viewModel->setVariable('bookingCollection', $bookingCollection);
        }
        catch (ServiceException $exception) {
            echo 'Fail!';
            exit;
        }
        
        return $viewModel;
    }
}
