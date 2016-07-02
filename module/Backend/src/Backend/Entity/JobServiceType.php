<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class JobServiceType
{
    private $idJobServiceType;
    private $name;

    public function getIdJobServiceType()
    {
        return $this->idJobServiceType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setIdJobServiceType($idJobServiceType)
    {
        $this->idJobServiceType = $idJobServiceType;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
