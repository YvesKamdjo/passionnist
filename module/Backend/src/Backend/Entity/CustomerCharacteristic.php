<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class CustomerCharacteristic
{
    private $idCustomerCharacteristic;
    private $name;

    public function getIdCustomerCharacteristic()
    {
        return $this->idCustomerCharacteristic;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setIdCustomerCharacteristic($idCustomerCharacteristic)
    {
        $this->idCustomerCharacteristic = $idCustomerCharacteristic;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
