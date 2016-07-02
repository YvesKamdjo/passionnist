<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\SalonImage;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class SalonImageMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Crée une image de salon
     * 
     * @param SalonImage $salonImage
     */
    public function create(SalonImage $salonImage)
    {
        // Crée une image de salon
        $select = '
            INSERT INTO SalonImage (
                filepath,
                idSalon
            )
            VALUES (
                :filepath,
                :idSalon
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':filepath' => $salonImage->getFilepath(),
                ':idSalon' => $salonImage->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une image d'un salon",
                null,
                $exception
            );
        }
        
        $salonImage->setIdSalonImage($this->db->getDriver()->getLastGeneratedValue());
    }
    
    public function deleteImageByImageId(SalonImage $salonImage)
    {
        // Crée une image de prestation
        $select = '
            DELETE FROM 
                SalonImage 
            WHERE
                idSalonImage = :idSalonImage;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idSalonImage' => $salonImage->getIdSalonImage(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'une image de salon",
                null,
                $exception
            );
        }
    }

    /**
     * Crée une image de salon
     * 
     * @param SalonImage $salonImage
     */
    public function findAllBySalonId(SalonImage $salonImage)
    {
        // Crée une image de prestation
        $select = '
            SELECT
                idSalonImage,
                filePath,
                idSalon
            FROM
                SalonImage
            WHERE
                idSalon = :salonId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':salonId' => $salonImage->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des images d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $salonImageCollection = new ArrayCollection();
        foreach ($result as $salonImageRow) {
            $salonImage = $this->hydrateEntity($salonImageRow);
            $salonImageCollection->add($salonImage);
        }

        return $salonImageCollection;
    }

    private function hydrateEntity(array $row)
    {
        $salonImage = new SalonImage();
        $salonImage->setIdSalonImage($row['idSalonImage']);
        $salonImage->setFilepath($row['filePath']);
        $salonImage->setIdSalon($row['idSalon']);
        
        return $salonImage;
    }
}
