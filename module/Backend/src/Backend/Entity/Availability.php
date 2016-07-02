<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Availability
{
    
    private $idAvailability;
    private $idWeekTemplate;
    private $startTime;
    private $endTime;
    private $day;

    public function getIdAvailability()
    {
        return $this->idAvailability;
    }

    public function getIdWeekTemplate()
    {
        return $this->idWeekTemplate;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getDay()
    {
        return $this->day;
    }

    public function setIdAvailability($idAvailability)
    {
        $this->idAvailability = $idAvailability;
        return $this;
    }

    public function setIdWeekTemplate($idWeekTemplate)
    {
        $this->idWeekTemplate = $idWeekTemplate;
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

    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

}
