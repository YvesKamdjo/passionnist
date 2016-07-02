<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Prospect
{    
    private $idProspect;
    private $email;
    private $firstName;
    private $lastName;
    private $phone;

    public function getIdProspect() {
        return $this->idProspect;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getFirstName() {
        return $this->firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setIdProspect($idProspect) {
        $this->idProspect = $idProspect;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
        return $this;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }


}
