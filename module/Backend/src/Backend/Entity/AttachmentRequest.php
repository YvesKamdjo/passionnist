<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class AttachmentRequest
{
    private $idAttachmentRequest;
    private $idSalon;
    private $idManager;
    private $idEmployee;
    private $managerEmail;

    public function getIdAttachmentRequest()
    {
        return $this->idAttachmentRequest;
    }

    public function getIdSalon()
    {
        return $this->idSalon;
    }

    public function getIdManager()
    {
        return $this->idManager;
    }

    public function getIdEmployee()
    {
        return $this->idEmployee;
    }

    public function getManagerEmail()
    {
        return $this->managerEmail;
    }

    public function setIdAttachmentRequest($idAttachmentRequest)
    {
        $this->idAttachmentRequest = $idAttachmentRequest;
        return $this;
    }

    public function setIdSalon($idSalon)
    {
        $this->idSalon = $idSalon;
        return $this;
    }

    public function setIdManager($idManager)
    {
        $this->idManager = $idManager;
        return $this;
    }

    public function setIdEmployee($idEmployee)
    {
        $this->idEmployee = $idEmployee;
        return $this;
    }

    public function setManagerEmail($managerEmail)
    {
        $this->managerEmail = $managerEmail;
        return $this;
    }

}
