<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Transaction
{

    private $idTransaction;
    private $idSalon;
    private $idFreelance;
    private $amount;
    private $description;
    private $idCreator;
    private $creationDate;

    public function getIdTransaction()
    {
        return $this->idTransaction;
    }

    public function getIdSalon()
    {
        return $this->idSalon;
    }

    public function getIdFreelance()
    {
        return $this->idFreelance;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getDescription()
    {
        return $this->description;
    }
    
    public function getIdCreator()
    {
        return $this->idCreator;
    }
    
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setIdTransaction($idTransaction)
    {
        $this->idTransaction = $idTransaction;
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

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    public function setIdCreator($idCreator)
    {
        $this->idCreator = $idCreator;
        return $this;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

}
