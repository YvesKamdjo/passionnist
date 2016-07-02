<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class FashionImage
{   
    private $idFashionImage;
    private $imageUrl;
    private $pinterestId;
    private $creationDate;

    public function getIdFashionImage()
    {
        return $this->idFashionImage;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getPinterestId()
    {
        return $this->pinterestId;
    }

    public function getCreationDate()
    {
        return $this->creationDate;
    }

    public function setIdFashionImage($idFashionImage)
    {
        $this->idFashionImage = $idFashionImage;
        return $this;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function setPinterestId($pinterestId)
    {
        $this->pinterestId = $pinterestId;
        return $this;
    }

    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

}
