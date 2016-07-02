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
use Backend\Entity\Invoice;
use Backend\Mapper\BookingMapper;
use Backend\Mapper\InvoiceMapper;
use Zend\Log\Logger;

class InvoiceService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $invoiceMapper InvoiceMapper */
    private $invoiceMapper;
    
    /* @var $bookingMapper BookingMapper */
    private $bookingMapper;

    public function __construct(
        $invoiceMapper,
        $bookingMapper,
        $logger
    ) {
        $this->invoiceMapper = $invoiceMapper;
        $this->bookingMapper = $bookingMapper;
        $this->logger = $logger;
    }

    public function findLastByAccountId($accountId)
    {
        $customer = new Account();
        $customer->setIdAccount($accountId);
        
        try {
            return $this->invoiceMapper->findLastByCustomerId($customer);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    public function findByBookingId($bookingId)
    {
        $booking = new Booking();
        $booking->setIdBooking($bookingId);
        
        try {
            return $this->invoiceMapper->findByBookingId($booking);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function create($bookingId)
    {
        $booking = new Booking();
        $booking->setIdBooking($bookingId);
        
        // Récupération de la réservation
        $storedBooking = $this->bookingMapper->findByBookingId($booking);
        
        $invoice = new Invoice();
        $invoice->setIdBooking($bookingId);
        $invoice->setJobServiceName($storedBooking->getJobServiceName());
        $invoice->setJobServicePrice($storedBooking->getJobServicePrice());
        $invoice->setDiscountRate($storedBooking->getDiscountRate());
        $invoice->setStart($storedBooking->getStart());
        $invoice->setDuration($storedBooking->getDuration());
        $invoice->setCustomerName($storedBooking->getCustomerName());
        $invoice->setCustomerAddress($storedBooking->getCustomerAddress());
        $invoice->setCustomerZipcode($storedBooking->getCustomerZipcode());
        $invoice->setCustomerCity($storedBooking->getCustomerCity());
        $invoice->setBillingName($storedBooking->getBillingName());
        $invoice->setBillingAddress($storedBooking->getBillingAddress());
        $invoice->setBillingZipcode($storedBooking->getBillingZipcode());
        $invoice->setBillingCity($storedBooking->getBillingCity());
        
        return $this->invoiceMapper->create($invoice);
    }
}
