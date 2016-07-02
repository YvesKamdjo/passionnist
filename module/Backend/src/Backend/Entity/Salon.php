<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Salon
{
    private $idSalon;
    private $name;
    private $address;
    private $zipcode;
    private $city;
    private $latitude;
    private $longitude;
    private $certificateFilename;
    private $isActive;

    public function getIdSalon()
    {
        return $this->idSalon;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getZipcode()
    {
        return $this->zipcode;
    }

    public function getCity()
    {
        return $this->city;
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }
    
    public function getCertificateFilename()
    {
        return $this->certificateFilename;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    public function setIdSalon($idSalon)
    {
        $this->idSalon = $idSalon;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function setCertificateFilename($certificateFilename)
    {
        $this->certificateFilename = $certificateFilename;
        return $this;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = (bool) $isActive;
        return $this;
    }
}
