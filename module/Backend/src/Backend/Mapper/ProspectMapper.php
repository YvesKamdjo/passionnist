<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Prospect;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class ProspectMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Crée le prospect
     * 
     * @param Prospect $prospect
     */
    public function create(Prospect $prospect)
    {
        // Création du prospect
        $insert = '
            INSERT INTO
                Prospect (
                    email,
                    firstName,
                    lastName,
                    phone
                )
            VALUES (
                :email,
                :firstName,
                :lastName,
                :phone
            );';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':email' => $prospect->getEmail(),
                ':firstName' => $prospect->getFirstName(),
                ':lastName' => $prospect->getLastName(),
                ':phone' => $prospect->getPhone(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'un prospect",
                null,
                $exception
            );
        }
    }

    /**
     * Retourne tous les prospects
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        // Récupère tous les prospects
        $select = '
            SELECT
                idProspect,
                email,
                firstName,
                lastName,
                phone
            FROM 
                Prospect;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de tous les prospects",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $prospectCollection = new ArrayCollection();
        foreach ($result as $prospectRow) {
            $prospect = $this->hydrateEntity($prospectRow);
            $prospectCollection->add($prospect);
        }

        return $prospectCollection;
    }
    
    private function hydrateEntity(array $row)
    {
        $prospect = new Prospect();
        $prospect->setIdProspect($row['idProspect']);
        $prospect->setEmail($row['email']);
        $prospect->setFirstName($row['firstName']);
        $prospect->setLastName($row['lastName']);
        $prospect->setPhone($row['phone']);
        
        return $prospect;
    }
}
