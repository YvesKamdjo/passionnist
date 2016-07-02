<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class AccountImage
{
    private $idAccountImage;
    private $filePath;
    private $idAccount;

    public function getIdAccountImage()
    {
        return $this->idAccountImage;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getIdAccount()
    {
        return $this->idAccount;
    }

    public function setIdAccountImage($idAccountImage)
    {
        $this->idAccountImage = $idAccountImage;
        return $this;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function setIdAccount($idAccount)
    {
        $this->idAccount = $idAccount;
        return $this;
    }
}
