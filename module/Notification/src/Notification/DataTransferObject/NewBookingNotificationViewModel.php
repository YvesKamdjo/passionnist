<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\DataTransferObject;

use DateTime;

class NewBookingNotificationViewModel {
    
    /* @var $idBooking int */
    public $idBooking;
    
    /* @var $professionalFirstName string */
    public $professionalFirstName;
    
    /* @var $professionalLastName string */
    public $professionalLastName;
    
    /* @var $professionalEmail string */
    public $professionalEmail;
    
    /* @var $customerEmail string */
    public $customerEmail;
    
    /* @var $billingName string */
    public $billingName;
    
    /* @var $billingAddress string */
    public $billingAddress;
    
    /* @var $billingCity string */
    public $billingCity;
    
    /* @var $billingZipcode string */
    public $billingZipcode;
    
    /* @var $jobServicePlaceName string */
    public $jobServicePlaceName;
    
    /* @var $jobServicePlaceAddress string */
    public $jobServicePlaceAddress;
    
    /* @var $jobServicePlaceCity string */
    public $jobServicePlaceCity;
    
    /* @var $jobServicePlaceZipcode string */
    public $jobServicePlaceZipcode;
    
    /* @var $jobServiceName string */
    public $jobServiceName;
    
    /* @var $duration int */
    public $duration;
    
    /* @var $price float */
    public $price;
    
    /* @var $discountRate float */
    public $discountRate;
    
    /* @var $start DateTime */
    public $start;
    
}