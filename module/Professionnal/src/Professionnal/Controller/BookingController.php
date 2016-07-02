<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Booking;
use Backend\Service\BookingService;
use DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

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
        
        $viewModel = new ViewModel();
        
        try {
            $bookingCollection = $bookingService->findByProfessionalId($sessionContainer->account->getIdAccount());
            
            $futureBookingCollection = new ArrayCollection();
            $pastBookingCollection = new ArrayCollection();
            
            // Séparation des réservations à venir et celles passées
            /* @var $booking Booking */
            foreach ($bookingCollection as $booking) {
                if (new DateTime($booking->getStart()) > new DateTime())
                {
                    $futureBookingCollection->add($booking);
                }
                else {
                    $pastBookingCollection->add($booking);
                }
            }
            
            $viewModel->setVariable('futureBookingCollection', $futureBookingCollection);
            $viewModel->setVariable('pastBookingCollection', $pastBookingCollection);
        }
        catch (ServiceException $exception) {
            echo 'Fail!';
            exit;
        }
        
        return $viewModel;
    }
}
