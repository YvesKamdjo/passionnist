<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\FashionImage;
use Backend\Infrastructure\DataTransferObject\FashionImage as DTOFashionImage;
use DateTime;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class FashionImageMapper
{
    private $db;
    
    private $pinterestConfig;

    public function __construct(
        Adapter $db,
        array $pinterestConfig
    ) {
        $this->db = $db;
        $this->pinterestConfig = $pinterestConfig;
    }

    /**
     * Retourne les 100 dernières images d'un board
     * 
     * @return ArrayCollection
     */
    public function findLastFromPinterest()
    {
        $board = $this->pinterestConfig['board'];
        $apiKey = $this->pinterestConfig['access_token'];
        
        // Génération de la requète
        $request = "https://api.pinterest.com/v1/boards/$board/pins/?access_token=$apiKey&fields=id%2Clink%2Cnote%2Curl%2Ccreated_at%2Cimage";

        $result = json_decode(file_get_contents($request));
        
        $fashionImageCollection = new ArrayCollection();
        foreach ($result->data as $fashionImageRow) {
            $date = new DateTime($fashionImageRow->created_at);
            $fashionImage = new FashionImage();
            $fashionImage->setPinterestId($fashionImageRow->id)
                ->setCreationDate($date->format('Y-m-d H:i:s'))
                ->setImageUrl($fashionImageRow->image->original->url);
            
            $fashionImageCollection->add($fashionImage);
        }
        
        return $fashionImageCollection;
    }
    
    public function findFromPinterestId(FashionImage $fashionImage)
    {
        // Récupère l'image
        $select = '
            SELECT
                idFashionImage,
                imageUrl,
                pinterestId,
                creationDate
            FROM 
                FashionImage
            WHERE
                pinterestId = :pinterestId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':pinterestId' => $fashionImage->getPinterestId(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une image de mode",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    public function findAll()
    {
        // Récupère les images
        $select = '
            SELECT
                idFashionImage,
                imageUrl,
                pinterestId,
                creationDate
            FROM 
                FashionImage;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des images de mode",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $fashionImageCollection = new ArrayCollection();
        foreach ($result as $fashionImageRow) {
            $fashionImage = $this->hydrateEntity($fashionImageRow);
            $fashionImageCollection->add($fashionImage);
        }

        return $fashionImageCollection;
    }
    
    public function findWeeks()
    {
        // Récupère les images
        $select = '
            SELECT
                WEEK(creationDate) as week,
                YEAR(creationDate) as year,
                count(1) as count
            FROM 
                FashionImage
            WHERE
                CONCAT(YEAR(creationDate), "-", WEEK(creationDate)) != CONCAT(YEAR(NOW()), "-", WEEK(NOW()))
            GROUP BY
                week, year
            HAVING
                count > 4
            ORDER BY
                year DESC, week DESC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des semaines des images de mode",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return [];
        }
        
        // Peuplement de la collection
        $fashionImageWeeks = [];
        foreach ($result as $fashionImageWeekRow) {
            $fashionImageWeeks[] = $fashionImageWeekRow;
        }

        
        
        return $fashionImageWeeks;
    }
    
    public function findByWeek(array $week, $page = 1, $limit = 5)
    {
        
        // Récupère les images
        $select = '
            SELECT
                idFashionImage,
                imageUrl,
                pinterestId,
                creationDate,
                :page,
                (SELECT COUNT(1) FROM FashionImage WHERE WEEK(creationDate) = :week AND YEAR(creationDate) = :year) as totalCount
            FROM 
                FashionImage
            WHERE
                WEEK(creationDate) = :week
            AND
                YEAR(creationDate) = :year
            LIMIT
                :from, :limit;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':week' => $week['week'],
                ':year' => $week['year'],
                ':page' => $page,
                ':from' => $limit * ($page - 1),
                ':limit' => $limit,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des images de mode d'une semaine",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $fashionImageCollection = new ArrayCollection();
        foreach ($result as $fashionImageRow) {
            $fashionImage = new DTOFashionImage();
            $fashionImage->creationDate = $fashionImageRow['creationDate'];
            $fashionImage->imageUrl = $fashionImageRow['imageUrl'];
            $fashionImage->pinterestId = $fashionImageRow['pinterestId'];
            $fashionImage->idFashionImage = $fashionImageRow['idFashionImage'];
            $fashionImage->page = $page;
            $fashionImage->totalCount = $fashionImageRow['totalCount'];
            
            $fashionImageCollection->add($fashionImage);
        }
        
        return $fashionImageCollection;
    }
    
    public function create(FashionImage $fashionImage)
    {
        // Enregistre l'image
        $insert = '
            INSERT INTO FashionImage (
                imageUrl,
                pinterestId,
                creationDate
            )
            VALUES (
                :imageUrl,
                :pinterestId,
                :creationDate
            );';

        $statement = $this->db->createStatement($insert);
        
        try {
            $result = $statement->execute([
                ':imageUrl' => $fashionImage->getImageUrl(),
                ':pinterestId' => $fashionImage->getPinterestId(),
                ':creationDate' => $fashionImage->getCreationDate(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une image de mode",
                null,
                $exception
            );
        }
    }
    
    private function hydrateEntity(array $row)
    {
        $fashionImage = new FashionImage();
        $fashionImage->setIdFashionImage($row['idFashionImage'])
            ->setImageUrl($row['imageUrl'])
            ->setPinterestId($row['pinterestId'])
            ->setCreationDate($row['creationDate']);
        
        return $fashionImage;
    }
}
