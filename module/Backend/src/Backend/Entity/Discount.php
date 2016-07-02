<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Discount
{
    
    private $idDiscount;
    private $idSalon;
    private $idFreelance;
    private $startTime;
    private $endTime;
    private $rate;
    private $day;

    public function getIdDiscount()
    {
        return $this->idDiscount;
    }

    public function getIdSalon()
    {
        return $this->idSalon;
    }
    
    public function getIdFreelance()
    {
        return $this->idFreelance;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function setIdDiscount($idDiscount)
    {
        $this->idDiscount = $idDiscount;
        return $this;
    }

    public function setIdSalon($idSalon)
    {
        $this->idSalon = $idSalon;
        return $this;
    }

    public function setIdFreelance($idFreelance)
    {
        $this->idFreelance = $idFreelance;
        return $this;
    }
    
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

}
