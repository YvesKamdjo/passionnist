<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Entity\Account;
use Backend\Entity\Booking;
use Backend\Entity\Invoice;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class InvoiceMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }
    
    public function findLastByCustomerId(Account $customer)
    {
        // Récupère la dernière facture d'un client
        $select = '
            SELECT
                Invoice.idInvoice,
                Invoice.idBooking,
                Invoice.jobServiceName,
                Invoice.jobServicePrice,
                Invoice.discountRate,
                Invoice.start,
                Invoice.duration,
                Invoice.customerName,
                Invoice.customerAddress,
                Invoice.customerZipcode,
                Invoice.customerCity,
                Invoice.billingName,
                Invoice.billingAddress,
                Invoice.billingZipcode,
                Invoice.billingCity
            FROM 
                Invoice
            INNER JOIN Booking
                ON Booking.idBooking = Invoice.idBooking
                AND Booking.idCustomer = :idCustomer
            ORDER BY
                Invoice.idInvoice DESC
            LIMIT 1
            ;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idCustomer' => $customer->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une facture",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    public function findByBookingId(Booking $booking)
    {
        // Récupère la dernière facture d'un client
        $select = '
            SELECT
                Invoice.idInvoice,
                Invoice.idBooking,
                Invoice.jobServiceName,
                Invoice.jobServicePrice,
                Invoice.discountRate,
                Invoice.start,
                Invoice.duration,
                Invoice.customerName,
                Invoice.customerAddress,
                Invoice.customerZipcode,
                Invoice.customerCity,
                Invoice.billingName,
                Invoice.billingAddress,
                Invoice.billingZipcode,
                Invoice.billingCity
            FROM 
                Invoice
            WHERE
                Invoice.idBooking = :bookingId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':bookingId' => $booking->getIdBooking(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une facture",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    /**
     * Crée une facture
     * 
     * @param Invoice $invoice
     * @throws MapperException
     */
    public function create(Invoice $invoice)
    {   
        // Création de la facture
        $insert = '
            INSERT INTO Invoice (
                idInvoice,
                idBooking,
                jobServiceName,
                jobServicePrice,
                discountRate,
                start,
                duration,
                customerName,
                customerAddress,
                customerZipcode,
                customerCity,
                billingName,
                billingAddress,
                billingZipcode,
                billingCity
            )
            VALUES (
                :idInvoice,
                :idBooking,
                :jobServiceName,
                :jobServicePrice,
                :discountRate,
                :start,
                :duration,
                :customerName,
                :customerAddress,
                :customerZipcode,
                :customerCity,
                :billingName,
                :billingAddress,
                :billingZipcode,
                :billingCity
            );';
        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idInvoice' => $invoice->getIdInvoice(),
                ':idBooking' => $invoice->getIdBooking(),
                ':jobServiceName' => $invoice->getJobServiceName(),
                ':jobServicePrice' => $invoice->getJobServicePrice(),
                ':discountRate' => $invoice->getDiscountRate(),
                ':start' => $invoice->getStart(),
                ':duration' => $invoice->getDuration(),
                ':customerName' => $invoice->getCustomerName(),
                ':customerAddress' => $invoice->getCustomerAddress(),
                ':customerZipcode' => $invoice->getCustomerZipcode(),
                ':customerCity' => $invoice->getCustomerCity(),
                ':billingName' => $invoice->getBillingName(),
                ':billingAddress' => $invoice->getBillingAddress(),
                ':billingZipcode' => $invoice->getBillingZipcode(),
                ':billingCity' => $invoice->getBillingCity(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une facture",
                null,
                $exception
            );
        }
        
        $invoice->setIdBooking($this->db->getDriver()->getLastGeneratedValue());
    }
    
    private function hydrateEntity(array $row)
    {
        $invoice = new Invoice();
        $invoice->setIdInvoice($row['idInvoice']);
        $invoice->setIdBooking($row['idBooking']);
        $invoice->setJobServiceName($row['jobServiceName']);
        $invoice->setJobServicePrice($row['jobServicePrice']);
        $invoice->setDiscountRate($row['discountRate']);
        $invoice->setStart($row['start']);
        $invoice->setDuration($row['duration']);
        $invoice->setCustomerName($row['customerName']);
        $invoice->setCustomerAddress($row['customerAddress']);
        $invoice->setCustomerZipcode($row['customerZipcode']);
        $invoice->setCustomerCity($row['customerCity']);
        $invoice->setBillingName($row['billingName']);
        $invoice->setBillingAddress($row['billingAddress']);
        $invoice->setBillingZipcode($row['billingZipcode']);
        $invoice->setBillingCity($row['billingCity']);
        
        return $invoice;
    }
}
