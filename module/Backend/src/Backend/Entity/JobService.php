<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class JobService
{
    private $idJobService;
    private $idJobServiceTemplate;
    private $idProfessional;
    private $name;
    private $duration;
    private $description;
    private $price;

    public function getIdJobService()
    {
        return $this->idJobService;
    }

    public function getIdJobServiceTemplate()
    {
        return $this->idJobServiceTemplate;
    }

    public function getIdProfessional()
    {
        return $this->idProfessional;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setIdJobService($idJobService)
    {
        $this->idJobService = $idJobService;
        return $this;
    }

    public function setIdJobServiceTemplate($idJobServiceTemplate)
    {
        $this->idJobServiceTemplate = $idJobServiceTemplate;
        return $this;
    }

    public function setIdProfessional($idProfessional)
    {
        $this->idProfessional = $idProfessional;
        return $this;
    }

    public function setName($name)
    {
        $this->name = ucfirst($name);
        return $this;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
}
