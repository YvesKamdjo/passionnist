<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobServiceTemplate;
use Backend\Entity\JobServiceType;
use Backend\Entity\Salon;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class JobServiceTemplateMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne toutes les prestations d'un salon
     * 
     * @param Salon $salon
     * @return ArrayCollection
     */
    public function findAllByIdSalon(Salon $salon)
    {
        // Récupère tous les templates de prestations
        $select = '
            SELECT
                idJobServiceTemplate,
                idManager,
                idSalon,
                name,
                price
            FROM 
                JobServiceTemplate
            WHERE
                idSalon = :idSalon;';

        $statement = $this->db->createStatement($select);
        $result = $statement->execute([
            ':idSalon' => $salon->getIdSalon()
        ]);
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceTemplateCollection = new ArrayCollection();
        foreach ($result as $jobServiceTemplateRow) {
            $jobServiceTemplate = $this->hydrateEntity($jobServiceTemplateRow);
            $jobServiceTemplateCollection->add($jobServiceTemplate);
        }

        return $jobServiceTemplateCollection;
    }
    
    /**
     * Retourne tous les types de prestations actifs d'un salon
     * 
     * @param Salon $salon
     * @return ArrayCollection
     */
    public function findAllActiveByIdSalon(Salon $salon)
    {
        // Récupère tous les templates de prestations
        $select = '
            SELECT
                idJobServiceTemplate,
                idManager,
                idSalon,
                name,
                price
            FROM 
                JobServiceTemplate
            WHERE
                idSalon = :idSalon
            AND
                price IS NOT NULL;';

        $statement = $this->db->createStatement($select);
        $result = $statement->execute([
            ':idSalon' => $salon->getIdSalon()
        ]);
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceTemplateCollection = new ArrayCollection();
        foreach ($result as $jobServiceTemplateRow) {
            $jobServiceTemplate = $this->hydrateEntity($jobServiceTemplateRow);
            $jobServiceTemplateCollection->add($jobServiceTemplate);
        }

        return $jobServiceTemplateCollection;
    }
    
    /**
     * Retourne un modèle de prestation par son id
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @return JobServiceTemplate
     */
    public function findById(JobServiceTemplate $jobServiceTemplate)
    {
        // Récupère tous les templates de prestations
        $select = '
            SELECT
                idJobServiceTemplate,
                idManager,
                idSalon,
                name,
                price
            FROM 
                JobServiceTemplate
            WHERE
                idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'un template de prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return null;
        }
        
        // Peuplement de la collection
        return $this->hydrateEntity($result->current());
    }
    
    /**
     * Création d'un template de prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     */
    public function create(JobServiceTemplate $jobServiceTemplate)
    {
        // Création d'un template de prestation
        $insert = '
            INSERT INTO JobServiceTemplate (
                idManager,
                idSalon,
                price,
                name
            )
            VALUES (
                :idManager,
                :idSalon,
                :price,
                :name
            )';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idManager' => $jobServiceTemplate->getIdManager(),
                ':idSalon' => $jobServiceTemplate->getIdSalon(),
                ':price' => $jobServiceTemplate->getPrice(),
                ':name' => $jobServiceTemplate->getName(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'un template de prestation",
                null,
                $exception
            );
        }
        
        $jobServiceTemplate->setIdJobServiceTemplate(
            $this->db->getDriver()->getLastGeneratedValue()
        );
    }
    
    /**
     * Modification d'un template de prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     */
    public function edit(JobServiceTemplate $jobServiceTemplate)
    {
        // Edition d'un template de prestation
        $update = '
            UPDATE
                JobServiceTemplate 
            SET
                price = :price,
                name = :name
            WHERE
                idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':price' => $jobServiceTemplate->getPrice(),
                ':name' => $jobServiceTemplate->getName(),
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification d'un template de prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Ajoute un type de prestation à un template de prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @param JobServiceType $jobServiceType
     */
    public function addJobServiceType(
        JobServiceTemplate $jobServiceTemplate, 
        JobServiceType $jobServiceType
    ) {
        // Ajout d'un type de prestation à un template de prestation
        $insert = '
            INSERT INTO JobServiceTemplateJobServiceType (
                idJobServiceTemplate,
                idJobServiceType
            )
            VALUES (
                :idJobServiceTemplate,
                :idJobServiceType
            )';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate(),
                ':idJobServiceType' => $jobServiceType->getIdJobServiceType(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'un type à un template de prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Vide les types d'une prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     */
    public function flushServiceType(JobServiceTemplate $jobServiceTemplate)
    {
        // Supprime les types de prestation
        $delete = '
            DELETE FROM 
                JobServiceTemplateJobServiceType
            WHERE
                idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression des types d'un template de prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Ajoute une caracteristique utilisateur à un template de prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @param CustomerCharacteristic $customerCharacteristic
     */
    public function addCustomerCharacteristic(
        JobServiceTemplate $jobServiceTemplate,
        CustomerCharacteristic $customerCharacteristic
    ) {
        // Ajout une caractéristique utilisateur à une prestation
        $insert = '
            INSERT INTO JobServiceTemplateCustomerCharacteristic (
                idJobServiceTemplate,
                idCustomerCharacteristic
            )
            VALUES (
                :idJobServiceTemplate,
                :idCustomerCharacteristic
            )';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate(),
                ':idCustomerCharacteristic' => $customerCharacteristic->getIdCustomerCharacteristic(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'une caracteristique à un template de prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Vide les caractéristiques utilisateurs
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     */
    public function flushCustomerCharacteristic(JobServiceTemplate $jobServiceTemplate)
    {
        // Supprime les caractéristique utilisateur
        $delete = '
            DELETE FROM 
                JobServiceTemplateCustomerCharacteristic
            WHERE
                idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression des caracteristiques d'un template de prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Supprime un modèle de prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     */
    public function delete(JobServiceTemplate $jobServiceTemplate)
    {
        // Suppression d'un modèle de prestation
        $delete = '
            DELETE FROM 
                JobServiceTemplate
            WHERE
                idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'un template de prestation",
                null,
                $exception
            );
        }
    }
    
    private function hydrateEntity(array $row)
    {
        $jobServiceTemplate = new JobServiceTemplate();
        $jobServiceTemplate->setIdJobServiceTemplate($row['idJobServiceTemplate']);
        $jobServiceTemplate->setIdManager($row['idManager']);
        $jobServiceTemplate->setIdSalon($row['idSalon']);
        $jobServiceTemplate->setName($row['name']);
        $jobServiceTemplate->setPrice($row['price']);
        
        return $jobServiceTemplate;
    }
}
