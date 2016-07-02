<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class JobServiceImage
{
    private $idJobServiceImage;
    private $filepath;
    private $idJobService;

    public function getIdJobServiceImage()
    {
        return $this->idJobServiceImage;
    }

    public function getFilepath()
    {
        return $this->filepath;
    }

    public function getIdJobService()
    {
        return $this->idJobService;
    }

    public function setIdJobServiceImage($idJobServiceImage)
    {
        $this->idJobServiceImage = $idJobServiceImage;
        return $this;
    }

    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
        return $this;
    }

    public function setIdJobService($idJobService)
    {
        $this->idJobService = $idJobService;
        return $this;
    }
}
