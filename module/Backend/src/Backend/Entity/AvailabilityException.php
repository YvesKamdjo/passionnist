<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class AvailabilityException
{
    
    private $idAvailabilityException;
    private $idAccount;
    private $startDatetime;
    private $endDatetime;
    private $isAvailability;
    private $details;

    public function getIdAvailabilityException()
    {
        return $this->idAvailabilityException;
    }

    public function getIdAccount()
    {
        return $this->idAccount;
    }

    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    public function getEndDatetime()
    {
        return $this->endDatetime;
    }

    public function getIsAvailability()
    {
        return $this->isAvailability;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setIdAvailabilityException($idAvailabilityException)
    {
        $this->idAvailabilityException = $idAvailabilityException;
        return $this;
    }

    public function setIdAccount($idAccount)
    {
        $this->idAccount = $idAccount;
        return $this;
    }

    public function setStartDatetime($startDatetime)
    {
        $this->startDatetime = $startDatetime;
        return $this;
    }

    public function setEndDatetime($endDatetime)
    {
        $this->endDatetime = $endDatetime;
        return $this;
    }

    public function setIsAvailability($isAvailability)
    {
        $this->isAvailability = $isAvailability;
        return $this;
    }

    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

}
