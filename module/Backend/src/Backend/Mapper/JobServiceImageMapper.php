<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\JobServiceImage;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class JobServiceImageMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Crée une image de prestation
     * 
     * @param JobServiceImage $jobServiceImage
     */
    public function create(JobServiceImage $jobServiceImage)
    {
        // Crée une image de prestation
        $select = '
            INSERT INTO JobServiceImage (
                filepath,
                idJobService
            )
            VALUES (
                :filepath,
                :idJobService
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':filepath' => $jobServiceImage->getFilepath(),
                ':idJobService' => $jobServiceImage->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une image d'une prestation",
                null,
                $exception
            );
        }
        
        $jobServiceImage->setIdJobServiceImage($this->db->getDriver()->getLastGeneratedValue());
    }
    
    public function deleteImageByImageId(JobServiceImage $jobServiceImage)
    {
        // Crée une image de prestation
        $select = '
            DELETE FROM 
                JobServiceImage 
            WHERE
                idJobServiceImage = :idJobServiceImage;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idJobServiceImage' => $jobServiceImage->getIdJobServiceImage(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'une image de prestation",
                null,
                $exception
            );
        }
    }

    /**
     * Crée une image de prestation
     * 
     * @param JobServiceImage $jobServiceImage
     */
    public function findAllByIdJobService(JobServiceImage $jobServiceImage)
    {
        // Crée une image de prestation
        $select = '
            SELECT
                idJobServiceImage,
                filePath,
                idJobService
            FROM
                JobServiceImage
            WHERE
                idJobService = :idJobService;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobService' => $jobServiceImage->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des images d'une prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceImageCollection = new ArrayCollection();
        foreach ($result as $jobServiceImageRow) {
            $jobServiceImage = $this->hydrateEntity($jobServiceImageRow);
            $jobServiceImageCollection->add($jobServiceImage);
        }

        return $jobServiceImageCollection;
    }
    
    public function findProfessionalLastJobServiceImage(Account $account ,$limit)
    {
        // Récupère les images
        $select = '
            SELECT
                JobServiceImage.idJobServiceImage,
                JobServiceImage.filePath,
                JobServiceImage.idJobService
            FROM
                JobServiceImage
            INNER JOIN JobService
                ON JobService.idJobService = JobServiceImage.idJobService
                AND JobService.idProfessional = :professionalId
            LIMIT
                0, :limit
        ;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':professionalId' => $account->getIdAccount(),
                ':limit' => $limit,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des images d'une prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceImageCollection = new ArrayCollection();
        foreach ($result as $jobServiceImageRow) {
            $jobServiceImage = $this->hydrateEntity($jobServiceImageRow);
            $jobServiceImageCollection->add($jobServiceImage);
        }

        return $jobServiceImageCollection;
    }
    
    public function findAllByProfessionalId(Account $account)
    {
        // Récupère les images
        $select = '
            SELECT
                JobServiceImage.idJobServiceImage,
                JobServiceImage.filePath,
                JobServiceImage.idJobService
            FROM
                JobServiceImage
            INNER JOIN JobService
                ON JobService.idJobService = JobServiceImage.idJobService
                AND JobService.idProfessional = :professionalId
        ;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':professionalId' => $account->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des toutes les images d'un professionnel",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceImageCollection = new ArrayCollection();
        foreach ($result as $jobServiceImageRow) {
            $jobServiceImage = $this->hydrateEntity($jobServiceImageRow);
            $jobServiceImageCollection->add($jobServiceImage);
        }

        return $jobServiceImageCollection;
    }


    private function hydrateEntity(array $row)
    {
        $jobServiceImage = new JobServiceImage();
        $jobServiceImage->setIdJobServiceImage($row['idJobServiceImage']);
        $jobServiceImage->setFilepath($row['filePath']);
        $jobServiceImage->setIdJobService($row['idJobService']);
        
        return $jobServiceImage;
    }
}
