<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Entity\Booking;
use Backend\Mapper\BookingMapper;
use DateTime;
use Zend\Log\Logger;

class BookingService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $bookingMapper BookingMapper */
    private $bookingMapper;

    public function __construct($bookingMapper, $logger)
    {
        $this->bookingMapper = $bookingMapper;
        $this->logger = $logger;
    }
    
    /**
     * Ajoute une réservation
     * 
     * @param array $bookingData
     * @return Booking
     * @throws ServiceException
     */
    public function add(array $bookingData)
    {
        $booking = new Booking();
        $booking->setIdJobService($bookingData['idJobService']);
        $booking->setIdCustomer($bookingData['idCustomer']);
        $booking->setStart($bookingData['start']);
        $booking->setDuration($bookingData['duration']);
        $booking->setJobServiceName($bookingData['jobServiceName']);
        $booking->setJobServicePrice($bookingData['jobServicePrice']);
        $booking->setDiscountRate($bookingData['discountRate']);
        $booking->setCustomerName($bookingData['customerName']);
        $booking->setCustomerAddress($bookingData['customerAddress']);
        $booking->setCustomerZipcode($bookingData['customerZipcode']);
        $booking->setCustomerCity($bookingData['customerCity']);
        $booking->setBillingName($bookingData['billingName']);
        $booking->setBillingAddress($bookingData['billingAddress']);
        $booking->setBillingZipcode($bookingData['billingZipcode']);
        $booking->setBillingCity($bookingData['billingCity']);

        try {
            $this->bookingMapper->create($booking);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        return $booking;
    }
    
    public function findByCustomerId($customerId)
    {
        $booking = new Booking();
        $booking->setIdCustomer($customerId);

        try {
            return $this->bookingMapper->findByCustomerId($booking);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    
    public function checkIfAllowedToInteract(array $checkingData)
    {
        $booking = new Booking();
        $booking->setIdBooking($checkingData['bookingId']);
        
        // Récupération de la réservation
        $storedBooking = $this->bookingMapper->findByBookingId($booking);
        
        // Si aucune réservation n'est récupérée
        if (!is_a($storedBooking, 'Backend\Entity\Booking')) {
            $storedBooking = new Booking();
        }
        
        // Si la demande de commentaire n'est pas faite par le client
        if ($storedBooking->getIdCustomer() != $checkingData['customerId']) {
            return false;
        }
        
        $currentDatetime = new DateTime();
        $storedDatetime = new DateTime($storedBooking->getStart());
        
        // Si prestation n'est pas encore passée
        if ($currentDatetime <= $storedDatetime)
        {
            return false;
        }
        
        return true;
    }
    
    public function findNextBookingByCustomerId($customerId)
    {
        $booking = new Account();
        $booking->setIdAccount($customerId);

        try {
            return $this->bookingMapper->findNextBookingByCustomerId($booking);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findByProfessionalId($professionalId)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);

        try {
            return $this->bookingMapper->findByProfessionalId($professional);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
