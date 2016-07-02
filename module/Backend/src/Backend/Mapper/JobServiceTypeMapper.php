<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\JobService;
use Backend\Entity\JobServiceTemplate;
use Backend\Entity\JobServiceType;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class JobServiceTypeMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne tous les types de prestation
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        // Récupère tous les types de prestation
        $select = '
            SELECT
                idJobServiceType,
                name
            FROM 
                JobServiceType;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des types de prestations",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceTypeCollection = new ArrayCollection();
        foreach ($result as $jobServiceTypeRow) {
            $jobServiceType = $this->hydrateEntity($jobServiceTypeRow);
            $jobServiceTypeCollection->add($jobServiceType);
        }

        return $jobServiceTypeCollection;
    }
    
    /**
     * Retourne la liste des types de prestation pour un ID de prestation donné
     * 
     * @param JobService $jobService
     * @return ArrayCollection
     */
    public function findByIdJobService(JobService $jobService)
    {
        // Récupère tous les types de prestation d'une prestation
        $select = '
            SELECT
                JobServiceType.idJobServiceType,
                JobServiceType.name
            FROM 
                JobServiceJobServiceType
            INNER JOIN JobServiceType
                ON JobServiceType.idJobServiceType = JobServiceJobServiceType.idJobServiceType
            WHERE 
                JobServiceJobServiceType.idJobService = :idJobService;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobService' => $jobService->getIdJobService()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des types d'une prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceTypeCollection = new ArrayCollection();
        foreach ($result as $jobServiceTypeRow) {
            $jobServiceType = $this->hydrateEntity($jobServiceTypeRow);
            $jobServiceTypeCollection->add($jobServiceType);
        }

        return $jobServiceTypeCollection;
    }
    
    /**
     * Retourne tous les types de prestation d'un modèle
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @return ArrayCollection
     */
    public function findByIdJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        // Récupère tous les types de prestation d'un modèle
        $select = '
            SELECT
                JobServiceType.idJobServiceType,
                JobServiceType.name
            FROM 
                JobServiceTemplateJobServiceType
            INNER JOIN JobServiceType
                ON JobServiceType.idJobServiceType = JobServiceTemplateJobServiceType.idJobServiceType
            WHERE 
                JobServiceTemplateJobServiceType.idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des types d'un template",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceTypeCollection = new ArrayCollection();
        foreach ($result as $jobServiceTypeRow) {
            $jobServiceType = $this->hydrateEntity($jobServiceTypeRow);
            $jobServiceTypeCollection->add($jobServiceType);
        }

        return $jobServiceTypeCollection;
    }

    /**
     * Retourne tous les types de prestation selon un template de prestation
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @return ArrayCollection
     */
    public function findAllByIdJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        // Récupère tous les types de prestation selon un template de prestation
        $select = '
            SELECT
                JobServiceType.idJobServiceType,
                JobServiceType.name
            FROM 
                JobServiceType
            INNER JOIN JobServiceTemplateJobServiceType
                ON JobServiceTemplateJobServiceType.idJobServiceType = JobServiceType.idJobServiceType
                AND JobServiceTemplateJobServiceType.idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des types d'un template",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceTypeCollection = new ArrayCollection();
        foreach ($result as $jobServiceTypeRow) {
            $jobServiceType = $this->hydrateEntity($jobServiceTypeRow);
            $jobServiceTypeCollection->add($jobServiceType);
        }

        return $jobServiceTypeCollection;
    }
    
    private function hydrateEntity(array $row)
    {
        $jobServiceType = new JobServiceType();
        $jobServiceType->setIdJobServiceType($row['idJobServiceType']);
        $jobServiceType->setName($row['name']);
        
        return $jobServiceType;
    }
}
