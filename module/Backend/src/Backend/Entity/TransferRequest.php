<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class TransferRequest
{

    private $idTransferRequest;
    private $idApplicant;
    private $idSalon;
    private $idFreelance;
    private $amount;
    private $applicantIdentity;
    private $iban;
    private $bic;
    private $creationDate;

    public function getIdTransferRequest()
    {
        return $this->idTransferRequest;
    }

    public function getIdApplicant()
    {
        return $this->idApplicant;
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

    public function getApplicantIdentity()
    {
        return $this->applicantIdentity;
    }

    public function getIban()
    {
        return $this->iban;
    }

    public function getBic()
    {
        return $this->bic;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setIdTransferRequest($idTransferRequest)
    {
        $this->idTransferRequest = $idTransferRequest;
        return $this;
    }

    public function setIdApplicant($idApplicant)
    {
        $this->idApplicant = $idApplicant;
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

    public function setApplicantIdentity($applicantIdentity)
    {
        $this->applicantIdentity = $applicantIdentity;
        return $this;
    }

    public function setIban($iban)
    {
        $this->iban = $iban;
        return $this;
    }

    public function setBic($bic)
    {
        $this->bic = $bic;
        return $this;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

}
