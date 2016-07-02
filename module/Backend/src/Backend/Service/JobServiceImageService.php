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
use Backend\Mapper\JobServiceImageMapper;
use Exception;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\Rename;
use Zend\Log\Logger;

class JobServiceImageService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $jobServiceImageMapper JobServiceImageMapper */
    private $jobServiceImageMapper;
    
    private $imageStorageDir = 'data/job-service-image';

    public function __construct(
        $jobServiceImageMapper,
        $logger
    ) {
        $this->jobServiceImageMapper = $jobServiceImageMapper;
        $this->logger = $logger;
    }
    
    /**
     * Permet l'enregistrement d'une image de prestation
     * 
     * @param array $jobServiceImageData
     * @return JobServiceImage
     */
    public function saveJobServiceImage(array $jobServiceImageData)
    {
        $filename = $this->storeFile($jobServiceImageData['image'], $this->imageStorageDir);
        
        $jobServiceImage = new JobServiceImage();
        $jobServiceImage->setIdJobService($jobServiceImageData['idJobService']);
        $jobServiceImage->setFilepath($filename);
        
        try {
            $this->jobServiceImageMapper->create($jobServiceImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        return $jobServiceImage;
    }
    
    /**
     * Retourne la liste des images d'une prestation
     * 
     * @param int $idJobService
     * @return ArrayCollection
     */
    public function findAllByIdJobService($idJobService)
    {
        $jobServiceImage = new JobServiceImage();
        $jobServiceImage->setIdJobService($idJobService);
        
        try {
            return $this->jobServiceImageMapper->findAllByIdJobService($jobServiceImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function deleteImageByImageId($imageId)
    {
        $jobServiceImage = new JobServiceImage();
        $jobServiceImage->setIdJobServiceImage($imageId);
        
        try {
            return $this->jobServiceImageMapper->deleteImageByImageId($jobServiceImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findProfessionalLastJobServiceImage($professionalId, $limit)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        try {
            return $this->jobServiceImageMapper
                ->findProfessionalLastJobServiceImage($professional, $limit);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findAllByProfessionalId($professionalId)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        try {
            return $this->jobServiceImageMapper
                ->findAllByProfessionalId($professional);
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
