<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Referral;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class ReferralMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne tous les referral
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        // Récupère tous les types de comptes
        $select = '
            SELECT
                idReferral,
                label
            FROM 
                Referral;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de tous les referral",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $referralCollection = new ArrayCollection();
        foreach ($result as $referralRow) {
            $referral = $this->hydrateEntity($referralRow);
            $referralCollection->add($referral);
        }

        return $referralCollection;
    }
    
    private function hydrateEntity(array $row)
    {
        $referral = new Referral();
        $referral->setIdReferral($row['idReferral']);
        $referral->setLabel($row['label']);
        
        return $referral;
    }
}
