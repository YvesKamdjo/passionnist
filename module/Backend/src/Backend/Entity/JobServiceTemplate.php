<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class JobServiceTemplate
{
    private $idJobServiceTemplate;
    private $idManager;
    private $idSalon;
    private $name;
    private $price;

    public function getIdJobServiceTemplate()
    {
        return $this->idJobServiceTemplate;
    }

    public function getIdManager()
    {
        return $this->idManager;
    }

    public function getIdSalon()
    {
        return $this->idSalon;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setIdJobServiceTemplate($idJobServiceTemplate)
    {
        $this->idJobServiceTemplate = $idJobServiceTemplate;
        return $this;
    }

    public function setIdManager($idManager)
    {
        $this->idManager = $idManager;
        return $this;
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

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
}
