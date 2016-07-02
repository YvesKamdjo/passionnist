<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\FashionImage;
use Backend\Mapper\FashionImageMapper;
use Zend\Log\Logger;

class FashionImageService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $fashionImageMapper FashionImageMapper */
    private $fashionImageMapper;

    public function __construct($fashionImageMapper, $logger)
    {
        $this->fashionImageMapper = $fashionImageMapper;
        $this->logger = $logger;
    }

    /**
     * Retourne toutes les dernières images de Pinterest
     * 
     * @return ArrayCollection
     */
    public function findLastFromPinterest()
    {
        try {
            return $this->fashionImageMapper->findLastFromPinterest();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findFromPinterestId($pinterestId)
    {
        $fashionImage = new FashionImage();
        $fashionImage->setPinterestId($pinterestId);
        
        try {
            return $this->fashionImageMapper->findFromPinterestId($fashionImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findAll()
    {        
        try {
            return $this->fashionImageMapper->findAll();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findLastWeeks($numberOfWeeksToFetch)
    {        
        try {
            $weekList = $this->fashionImageMapper->findWeeks();
            
            $weeks = [];
            for ($i = 0; $i < $numberOfWeeksToFetch && $i < count($weekList); $i++) {
                $weeks[] = $this->fashionImageMapper->findByWeek($weekList[$i]);
            }
            
            return $weeks;
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function loadMoreFashionImages($week, $year, $page)
    {        
        try {            
            return $this->fashionImageMapper->findByWeek([
                    'week' => $week,
                    'year' => $year,
                ], $page + 1);
            
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function create(array $fashionImageData)
    {
        $fashionImage = new FashionImage();
        $fashionImage->setPinterestId($fashionImageData['pinterestId'])
            ->setCreationDate($fashionImageData['creationDate'])
            ->setImageUrl($fashionImageData['imageUrl']);
        
        try {
            return $this->fashionImageMapper->create($fashionImage);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function updateFashionImages()
    {
        // Récupère les images de Pinterest
        $newFashionImages = $this->findLastFromPinterest();
        
        foreach ($newFashionImages as $newFashionImage) {
            $savedImage = $this->findFromPinterestId($newFashionImage->getPinterestId());
            
            if (!is_null($savedImage)) {
                break;
            }
            
            $this->create([
                'pinterestId' => $newFashionImage->getPinterestId(),
                'creationDate' => $newFashionImage->getCreationDate(),
                'imageUrl' => $newFashionImage->getImageUrl(),
            ]);
        }
    }
}
