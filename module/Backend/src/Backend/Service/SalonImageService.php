<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\JobServiceImage;
use Backend\Entity\SalonImage;
use Backend\Mapper\SalonImageMapper;
use Exception;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\Rename;
use Zend\Log\Logger;

class SalonImageService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $salonImageMapper SalonImageMapper */
    private $salonImageMapper;
    
    private $imageStorageDir = 'data/salon-image';

    public function __construct(
        $salonImageMapper,
        $logger
    ) {
        $this->salonImageMapper = $salonImageMapper;
        $this->logger = $logger;
    }
    
    /**
     * Permet l'enregistrement d'une image de salon
     * 
     * @param array $salonImageData
     * @return SalonImage
     */
    public function saveSalonImage(array $salonImageData)
    {
        $filename = $this->storeFile($salonImageData['image'], $this->imageStorageDir);
        
        $salonImage = new SalonImage();
        $salonImage->setIdSalon($salonImageData['idSalon']);
        $salonImage->setFilepath($filename);
        
        try {
            $this->salonImageMapper->create($salonImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        return $salonImage;
    }
    
    public function deleteImageByImageId($imageId)
    {
        $salonImage = new SalonImage();
        $salonImage->setIdSalonImage($imageId);
        
        try {
            return $this->salonImageMapper->deleteImageByImageId($salonImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne la liste des images d'une salon
     * 
     * @param int $salonId
     * @return ArrayCollection
     */
    public function findAllBySalonId($salonId)
    {
        $salonImage = new SalonImage();
        $salonImage->setIdSalon($salonId);
        
        try {
            return $this->salonImageMapper->findAllBySalonId($salonImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Stocke un fichier uploadé
     * 
     * ["name"]     => string(11) "myimage.png"
     * ["type"]     => string(9)  "image/png"
     * ["tmp_name"] => string(22) "/private/tmp/phpgRXd58"
     * ["error"]    => int(0)
     * ["size"]     => int(14908679)
     * 
     * @param array $imageInfo
     * @return string
     */
    private function storeFile(array $imageInfo, $destination)
    {
        $transferAdapter = new Http();
        $transferAdapter->setDestination($destination);
        
        $filename = sprintf(
            '%s.%s',
            uniqid(),
            strtolower(pathinfo($imageInfo['name'], PATHINFO_EXTENSION))
        );
        
        $filepath = $transferAdapter->getDestination()
            . DIRECTORY_SEPARATOR
            . $filename;
        
        $transferAdapter->addFilter(
            new Rename([
                'target' => $filepath,
                'overwrite' => true
            ])
        );
        
        if ($transferAdapter->receive($imageInfo['name']) === false) {
            throw new Exception(
                implode('; ', $transferAdapter->getMessages())
            );
        }
        
        return $filename;
    }
}
