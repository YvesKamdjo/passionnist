<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobService;
use Backend\Entity\JobServiceTemplate;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class CustomerCharacteristicMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne toutes les caractéristiques utilisateur
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        // Récupère toutes les caractéristiques
        $select = '
            SELECT
                idCustomerCharacteristic,
                name
            FROM 
                CustomerCharacteristic;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de toutes les caractéristiques",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $customerCharacteristicCollection = new ArrayCollection();
        foreach ($result as $customerCharacteristicRow) {
            $customerCharacteristic = $this->hydrateEntity($customerCharacteristicRow);
            $customerCharacteristicCollection->add($customerCharacteristic);
        }

        return $customerCharacteristicCollection;
    }

    public function findByIdJobService(JobService $jobService)
    {
        // Récupère toutes les caractéristiques d'une prestation
        $select = '
            SELECT
                CustomerCharacteristic.idCustomerCharacteristic,
                CustomerCharacteristic.name
            FROM 
                JobServiceCustomerCharacteristic
            INNER JOIN CustomerCharacteristic
                ON CustomerCharacteristic.idCustomerCharacteristic = JobServiceCustomerCharacteristic.idCustomerCharacteristic
            WHERE
                JobServiceCustomerCharacteristic.idJobService = :idJobService;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobService' => $jobService->getIdJobService()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de toutes les caractéristiques",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $customerCharacteristicCollection = new ArrayCollection();
        foreach ($result as $customerCharacteristicRow) {
            $customerCharacteristic = $this->hydrateEntity($customerCharacteristicRow);
            $customerCharacteristicCollection->add($customerCharacteristic);
        }

        return $customerCharacteristicCollection;
    }
    
    /**
     * Retourne toutes les caractéristiques utilisateur selon un template de prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @return ArrayCollection
     */
    public function findByIdJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        // Récupère toutes les caractéristiques selon un template de prestation
        $select = '
            SELECT
                CustomerCharacteristic.idCustomerCharacteristic,
                CustomerCharacteristic.name
            FROM 
                CustomerCharacteristic
            INNER JOIN JobServiceTemplateCustomerCharacteristic
                ON JobServiceTemplateCustomerCharacteristic.idCustomerCharacteristic = CustomerCharacteristic.idCustomerCharacteristic
                AND JobServiceTemplateCustomerCharacteristic.idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de toutes les caractéristiques d'un template de prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $customerCharacteristicCollection = new ArrayCollection();
        foreach ($result as $customerCharacteristicRow) {
            $customerCharacteristic = $this->hydrateEntity($customerCharacteristicRow);
            $customerCharacteristicCollection->add($customerCharacteristic);
        }

        return $customerCharacteristicCollection;
    }
    
    private function hydrateEntity(array $row)
    {
        $customerCharacteristic = new CustomerCharacteristic();
        $customerCharacteristic->setIdCustomerCharacteristic($row['idCustomerCharacteristic']);
        $customerCharacteristic->setName($row['name']);
        
        return $customerCharacteristic;
    }
}
