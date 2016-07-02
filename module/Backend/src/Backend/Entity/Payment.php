<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Payment
{
    const PAYMENT_STATUS_INITIALIZED = 0;
    const PAYMENT_STATUS_CANCELED = 1;
    const PAYMENT_STATUS_SUCCESSED = 2;
    const PAYMENT_STATUS_FAILED = 3;
    
    private $idPayment;
    private $idCustomer;
    private $idFreelance;
    private $idSalon;
    private $amount;
    private $status;
    private $bankReturn;
    private $creationDate;

    public function getIdPayment()
    {
        return $this->idPayment;
    }

    public function getIdCustomer()
    {
        return $this->idCustomer;
    }

    public function getIdFreelance()
    {
        return $this->idFreelance;
    }

    public function getIdSalon()
    {
        return $this->idSalon;
    }

    public function getAmount()
    {
        return $this->amount;
    }
    
    public function getStatus()
    {
        return $this->status;
    }

    public function getBankReturn()
    {
        return $this->bankReturn;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setIdPayment($idPayment)
    {
        $this->idPayment = $idPayment;
        return $this;
    }

    public function setIdCustomer($idCustomer)
    {
        $this->idCustomer = $idCustomer;
        return $this;
    }

    public function setIdFreelance($idFreelance)
    {
        $this->idFreelance = $idFreelance;
        return $this;
    }

    public function setIdSalon($idSalon)
    {
        $this->idSalon = $idSalon;
        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setBankReturn($bankReturn)
    {
        $this->bankReturn = $bankReturn;
        return $this;
    }
    
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

}
