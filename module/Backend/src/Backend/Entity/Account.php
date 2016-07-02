<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Account
{
    private $idAccount;
    private $firstName;
    private $lastName;
    private $email;
    private $phone;
    private $password;
    private $address;
    private $zipcode;
    private $city;
    private $latitude;
    private $longitude;
    private $moveRange;
    private $biography;
    private $idReferral;
    private $accountImageFilename;
    private $qualificationFilename;
    private $isActive;

    public function getIdAccount()
    {
        return $this->idAccount;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getName()
    {
        return $this->getFirstName(). ' '.$this->getLastName();
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getPassword()
    {
        return $this->password;
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
    
    public function getMoveRange()
    {
        return $this->moveRange;
    }

    public function getBiography()
    {
        return $this->biography;
    }

    public function getIdReferral()
    {
        return $this->idReferral;
    }

    public function getAccountImageFilename()
    {
        return $this->accountImageFilename;
    }

    public function getQualificationFilename()
    {
        return $this->qualificationFilename;
    }

    public function isActive()
    {
        return $this->isActive;
    }

    public function setIdAccount($idAccount)
    {
        $this->idAccount = $idAccount;
        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = ucfirst($firstName);
        return $this;
    }

    public function setLastName($lastName)
    {
        $this->lastName = ucfirst($lastName);
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
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
        $this->city = ucfirst($city);
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
    
    public function setMoveRange($moveRange)
    {
        $this->moveRange = $moveRange;
        return $this;
    }

    public function setBiography($biography)
    {
        $this->biography = $biography;
        return $this;
    }

    public function setIdReferral($idReferral)
    {
        $this->idReferral = $idReferral;
        return $this;
    }

    public function setAccountImageFilename($accountImageFilename)
    {
        $this->accountImageFilename = $accountImageFilename;
        return $this;
    }

    public function setQualificationFilename($qualificationFilename)
    {
        $this->qualificationFilename = $qualificationFilename;
        return $this;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = (bool) $isActive;
        return $this;
    }
}
