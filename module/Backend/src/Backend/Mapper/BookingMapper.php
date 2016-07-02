<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\Booking;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class BookingMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }
    
    public function create(Booking $booking)
    {        
        // Création de la reservation
        $insert = '
            INSERT INTO Booking (
                idJobService,
                idCustomer,
                start,
                duration,
                jobServiceName,
                jobServicePrice,
                discountRate,
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
                :idJobService,
                :idCustomer,
                :start,
                :duration,
                :jobServiceName,
                :jobServicePrice,
                :discountRate,
                :customerName,
                :customerAddress,
                :customerZipcode,
                :customerCity,
                :billingName,
                :billingAddress,
                :billingZipcode,
                :billingCity
            )';
        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idJobService' => $booking->getIdJobService(),
                ':idCustomer' => $booking->getIdCustomer(),
                ':start' => $booking->getStart(),
                ':duration' => $booking->getDuration(),
                ':jobServiceName' => $booking->getJobServiceName(),
                ':jobServicePrice' => $booking->getJobServicePrice(),
                ':discountRate' => $booking->getDiscountRate(),
                ':customerName' => $booking->getCustomerName(),
                ':customerAddress' => $booking->getCustomerAddress(),
                ':customerZipcode' => $booking->getCustomerZipcode(),
                ':customerCity' => $booking->getCustomerCity(),
                ':billingName' => $booking->getBillingName(),
                ':billingAddress' => $booking->getBillingAddress(),
                ':billingZipcode' => $booking->getBillingZipcode(),
                ':billingCity' => $booking->getBillingCity(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une réservation",
                null,
                $exception
            );
        }
        
        $booking->setIdBooking($this->db->getDriver()->getLastGeneratedValue());
    }
    
    public function delete(Booking $booking)
    {
        // Suppression de la reservation
        $delete = '
            DELETE FROM
                Booking
            WHERE
                idBooking = :idBooking';
        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idBooking' => $booking->getIdBooking(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'une réservation",
                null,
                $exception
            );
        }
    }
    
    public function findByBookingId(Booking $booking)
    {
        // Récupère la réservation
        $select = '
            SELECT
                idBooking,
                idJobService,
                idCustomer,
                start,
                duration,
                jobServiceName,
                jobServicePrice,
                discountRate,
                customerName,
                customerAddress,
                customerZipcode,
                customerCity,
                billingName,
                billingAddress,
                billingZipcode,
                billingCity
            FROM 
                Booking
            WHERE
                idBooking = :idBooking;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idBooking' => $booking->getIdBooking(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une réservation",
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
     * Récupère la liste des réservations payées d'un utilisateur
     * 
     * @param Booking $booking
     * @return ArrayCollection
     * @throws MapperException
     */
    public function findByCustomerId(Booking $booking)
    {
        // Récupère les réservations
        $select = '
            SELECT
                Booking.idBooking,
                Booking.idJobService,
                Booking.idCustomer,
                Booking.start,
                Booking.duration,
                Booking.jobServiceName,
                Booking.jobServicePrice,
                Booking.discountRate,
                Booking.customerName,
                Booking.customerAddress,
                Booking.customerZipcode,
                Booking.customerCity,
                Booking.billingName,
                Booking.billingAddress,
                Booking.billingZipcode,
                Booking.billingCity
            FROM 
                Booking
            INNER JOIN Invoice
                ON Invoice.idBooking = Booking.idBooking
            WHERE
                idCustomer = :idCustomer
            ORDER BY
                start;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idCustomer' => $booking->getIdCustomer(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des réservations d'un utilisateur",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $bookingCollection = new ArrayCollection();
        foreach ($result as $bookingRow) {
            $booking = $this->hydrateEntity($bookingRow);
            $bookingCollection->add($booking);
        }

        return $bookingCollection;
    }

    public function findNextBookingByCustomerId(Account $customer)
    {
        // Récupère la réservation
        $select = '
            SELECT
                idBooking,
                idJobService,
                idCustomer,
                start,
                duration,
                jobServiceName,
                jobServicePrice,
                discountRate,
                customerName,
                customerAddress,
                customerZipcode,
                customerCity,
                billingName,
                billingAddress,
                billingZipcode,
                billingCity
            FROM 
                Booking
            WHERE
                idCustomer = :customerId
            AND
                DATE(start) >= NOW()
            ORDER BY
                start ASC
            LIMIT
                1;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':customerId' => $customer->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de la prochaine réservation",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    public function findByProfessionalId(Account $professional)
    {
        // Récupère la réservation
        $select = '
            SELECT
                Booking.idBooking,
                Booking.idJobService,
                Booking.idCustomer,
                Booking.start,
                Booking.duration,
                Booking.jobServiceName,
                Booking.jobServicePrice,
                Booking.discountRate,
                Booking.customerName,
                Booking.customerAddress,
                Booking.customerZipcode,
                Booking.customerCity,
                Booking.billingName,
                Booking.billingAddress,
                Booking.billingZipcode,
                Booking.billingCity
            FROM 
                Booking
            INNER JOIN JobService
                ON JobService.idJobService = Booking.idJobService
                AND JobService.idProfessional = :professionalId
            ORDER BY
                start DESC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':professionalId' => $professional->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des réservations d'un professionnel",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $bookingCollection = new ArrayCollection();
        foreach ($result as $bookingRow) {
            $booking = $this->hydrateEntity($bookingRow);
            $bookingCollection->add($booking);
        }

        return $bookingCollection;
    }
    
    /**
     * Peuple une réservation
     * 
     * @param array $row
     * @return Booking
     */
    private function hydrateEntity(array $row)
    {
        $booking = new Booking();
        $booking->setIdBooking($row['idBooking']);
        $booking->setIdJobService($row['idJobService']);
        $booking->setIdCustomer($row['idCustomer']);
        $booking->setStart($row['start']);
        $booking->setDuration($row['duration']);
        $booking->setJobServiceName($row['jobServiceName']);
        $booking->setJobServicePrice($row['jobServicePrice']);
        $booking->setDiscountRate($row['discountRate']);
        $booking->setCustomerName($row['customerName']);
        $booking->setCustomerAddress($row['customerAddress']);
        $booking->setCustomerZipcode($row['customerZipcode']);
        $booking->setCustomerCity($row['customerCity']);
        $booking->setBillingName($row['billingName']);
        $booking->setBillingAddress($row['billingAddress']);
        $booking->setBillingZipcode($row['billingZipcode']);
        $booking->setBillingCity($row['billingCity']);
        
        return $booking;
    }
}
