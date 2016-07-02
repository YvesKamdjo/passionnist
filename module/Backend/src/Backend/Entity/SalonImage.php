<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class SalonImage
{
    private $idSalonImage;
    private $filepath;
    private $idSalon;
    
    public function getIdSalonImage()
    {
        return $this->idSalonImage;
    }

    public function getFilepath()
    {
        return $this->filepath;
    }

    public function getIdSalon()
    {
        return $this->idSalon;
    }

    public function setIdSalonImage($idSalonImage)
    {
        $this->idSalonImage = $idSalonImage;
        return $this;
    }

    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
        return $this;
    }

    public function setIdSalon($idSalon)
    {
        $this->idSalon = $idSalon;
        return $this;
    }

}
