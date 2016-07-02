<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Finder;

use Backend\Collection\ArrayCollection;
use Backend\Collection\CollectionInterface;
use DateTime;
use Notification\DataTransferObject\NewBookingNotificationViewModel;
use Notification\Service\NotificationService;
use Zend\Db\Adapter\Adapter;

class NewBookingNotificationFinder
{
    /** @var Adapter */
    private $db;
    
    /**
     * @param Adapter $db
     */
    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }
    
    /**
     * Retourne la liste des réservations n'ayant pas encore été notifiés à leur
     * professionnel.
     * 
     * @return CollectionInterface
     */
    public function findAllForProfessional()
    {
        $select = '
        SELECT
            Booking.idBooking,
            Professional.firstName,
            Professional.lastName,
            Professional.email,
            Invoice.billingName,
            Invoice.billingAddress,
            Invoice.billingCity,
            Invoice.billingZipcode,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.name, 
                Invoice.customerName
            ) as jobServicePlaceName,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.address, 
                Invoice.customerAddress
            ) as jobServicePlaceAddress,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.city, 
                Invoice.customerCity
            ) as jobServicePlaceCity,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.zipcode, 
                Invoice.customerZipcode
            ) as jobServicePlaceZipcode,
            Invoice.jobServiceName,
            Invoice.duration,
            Invoice.jobServicePrice,
            Invoice.discountRate,
            Invoice.start
        FROM
            Booking
        INNER JOIN Invoice
            ON Invoice.idBooking = Booking.idBooking
        INNER JOIN JobService
            ON JobService.idJobService = Booking.idJobService
        INNER JOIN Account as Professional
            ON Professional.idAccount = JobService.idProfessional
        LEFT JOIN Employee
            ON Employee.idEmployee = Professional.idAccount
        LEFT JOIN Salon
            ON Salon.idSalon = Employee.idSalon
        WHERE
            Booking.idBooking NOT IN (
                SELECT
                    `key`
                FROM
                    Notification
                WHERE
                    idNotification = :idNotification
            )
            ;';
            
        $statement = $this->db->createStatement($select);
        $result = $statement->execute([
            ':idNotification' => NotificationService::NOTIFICATION_PROFESSIONAL_NEW_BOOKING
        ]);
        
        $notifications = new ArrayCollection();
        if ($result->isQueryResult() === false || $result->count() < 1) {
            return $notifications;
        }
        
        foreach ($result as $row) {
            $notification = new NewBookingNotificationViewModel();
            
            $notification->idBooking = (int) $row['idBooking'];
            $notification->professionalFirstName = $row['firstName'];
            $notification->professionalLastName = $row['lastName'];
            $notification->professionalEmail = $row['email'];
            $notification->billingName = $row['billingName'];
            $notification->billingAddress = $row['billingAddress'];
            $notification->billingCity = $row['billingCity'];
            $notification->billingZipcode = $row['billingZipcode'];
            $notification->jobServicePlaceName = $row['jobServicePlaceName'];
            $notification->jobServicePlaceAddress = $row['jobServicePlaceAddress'];
            $notification->jobServicePlaceCity = $row['jobServicePlaceCity'];
            $notification->jobServicePlaceZipcode = $row['jobServicePlaceZipcode'];
            $notification->jobServiceName = $row['jobServiceName'];
            $notification->duration = (int) $row['duration'];
            $notification->price = (int) $row['jobServicePrice'];
            $notification->discountRate = (int) $row['discountRate'];
            $notification->start = new DateTime($row['start']);
            
            $notifications->add($notification);
        }
        
        return $notifications;
    }
    
    /**
     * Retourne la liste des réservations n'ayant pas encore été notifiés à leur
     * client.
     * 
     * @return CollectionInterface
     */
    public function findAllForCustomer()
    {
        $select = '
        SELECT
            Booking.idBooking,
            Professional.firstName,
            Professional.lastName,
            Professional.email,
            Customer.email as customerEmail,
            Invoice.billingName,
            Invoice.billingAddress,
            Invoice.billingCity,
            Invoice.billingZipcode,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.name, 
                Invoice.customerName
            ) as jobServicePlaceName,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.address, 
                Invoice.customerAddress
            ) as jobServicePlaceAddress,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.city, 
                Invoice.customerCity
            ) as jobServicePlaceCity,
            IF(
                Salon.idSalon IS NOT NULL, 
                Salon.zipcode, 
                Invoice.customerZipcode
            ) as jobServicePlaceZipcode,
            Invoice.jobServiceName,
            Invoice.duration,
            Invoice.jobServicePrice,
            Invoice.discountRate,
            Invoice.start
        FROM
            Booking
        INNER JOIN Invoice
            ON Invoice.idBooking = Booking.idBooking
        INNER JOIN JobService
            ON JobService.idJobService = Booking.idJobService
        INNER JOIN Account as Professional
            ON Professional.idAccount = JobService.idProfessional
        INNER JOIN Account as Customer
            ON Customer.idAccount = Booking.idCustomer
        LEFT JOIN Employee
            ON Employee.idEmployee = Professional.idAccount
        LEFT JOIN Salon
            ON Salon.idSalon = Employee.idSalon
        WHERE
            Booking.idBooking NOT IN (
                SELECT
                    `key`
                FROM
                    Notification
                WHERE
                    idNotification = :idNotification
            )
            ;';
            
        $statement = $this->db->createStatement($select);
        $result = $statement->execute([
            ':idNotification' => NotificationService::NOTIFICATION_CUSTOMER_NEW_BOOKING
        ]);
        
        $notifications = new ArrayCollection();
        if ($result->isQueryResult() === false || $result->count() < 1) {
            return $notifications;
        }
        
        foreach ($result as $row) {
            $notification = new NewBookingNotificationViewModel();
            
            $notification->idBooking = (int) $row['idBooking'];
            $notification->professionalFirstName = $row['firstName'];
            $notification->professionalLastName = $row['lastName'];
            $notification->professionalEmail = $row['email'];
            $notification->customerEmail = $row['customerEmail'];
            $notification->billingName = $row['billingName'];
            $notification->billingAddress = $row['billingAddress'];
            $notification->billingCity = $row['billingCity'];
            $notification->billingZipcode = $row['billingZipcode'];
            $notification->jobServicePlaceName = $row['jobServicePlaceName'];
            $notification->jobServicePlaceAddress = $row['jobServicePlaceAddress'];
            $notification->jobServicePlaceCity = $row['jobServicePlaceCity'];
            $notification->jobServicePlaceZipcode = $row['jobServicePlaceZipcode'];
            $notification->jobServiceName = $row['jobServiceName'];
            $notification->duration = (int) $row['duration'];
            $notification->price = (int) $row['jobServicePrice'];
            $notification->discountRate = (int) $row['discountRate'];
            $notification->start = new DateTime($row['start']);
            
            $notifications->add($notification);
        }
        
        return $notifications;
    }
}
