<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Booking
{    
    private $idBooking;
    private $idJobService;
    private $idCustomer;
    private $start;
    private $duration;
    private $jobServiceName;
    private $jobServicePrice;
    private $discountRate;
    private $customerName;
    private $customerAddress;
    private $customerZipcode;
    private $customerCity;
    private $billingName;
    private $billingAddress;
    private $billingZipcode;
    private $billingCity;
    
    public function getIdBooking()
    {
        return $this->idBooking;
    }

    public function getIdJobService()
    {
        return $this->idJobService;
    }

    public function getIdCustomer()
    {
        return $this->idCustomer;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getJobServiceName()
    {
        return $this->jobServiceName;
    }

    public function getJobServicePrice()
    {
        return $this->jobServicePrice;
    }

    public function getDiscountRate()
    {
        return $this->discountRate;
    }

    public function getCustomerName()
    {
        return $this->customerName;
    }

    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    public function getCustomerZipcode()
    {
        return $this->customerZipcode;
    }

    public function getCustomerCity()
    {
        return $this->customerCity;
    }

    public function getBillingName()
    {
        return $this->billingName;
    }

    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    public function getBillingZipcode()
    {
        return $this->billingZipcode;
    }

    public function getBillingCity()
    {
        return $this->billingCity;
    }

        
    public function setIdBooking($idBooking)
    {
        $this->idBooking = $idBooking;
        return $this;
    }

    public function setIdJobService($idJobService)
    {
        $this->idJobService = $idJobService;
        return $this;
    }

    public function setIdCustomer($idCustomer)
    {
        $this->idCustomer = $idCustomer;
        return $this;
    }

    public function setStart($start)
    {
        $this->start = $start;
        return $this;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function setJobServiceName($jobServiceName)
    {
        $this->jobServiceName = $jobServiceName;
        return $this;
    }

    public function setJobServicePrice($jobServicePrice)
    {
        $this->jobServicePrice = $jobServicePrice;
        return $this;
    }

    public function setDiscountRate($discountRate)
    {
        $this->discountRate = $discountRate;
        return $this;
    }

    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;
        return $this;
    }

    public function setCustomerZipcode($customerZipcode)
    {
        $this->customerZipcode = $customerZipcode;
        return $this;
    }

    public function setCustomerCity($customerCity)
    {
        $this->customerCity = $customerCity;
        return $this;
    }

    public function setBillingName($billingName)
    {
        $this->billingName = $billingName;
        return $this;
    }

    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    public function setBillingZipcode($billingZipcode)
    {
        $this->billingZipcode = $billingZipcode;
        return $this;
    }

    public function setBillingCity($billingCity)
    {
        $this->billingCity = $billingCity;
        return $this;
    }


}
