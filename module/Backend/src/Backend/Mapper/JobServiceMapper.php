<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobService;
use Backend\Entity\JobServiceTemplate;
use Backend\Entity\JobServiceType;
use Backend\Entity\Salon;
use Backend\Infrastructure\DataTransferObject\CompleteJobService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class JobServiceMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne toutes les prestations selon un account
     * 
     * @param Account $professional
     * @return ArrayCollection
     */
    public function findAllByIdAccount(Account $professional)
    {
        // Récupère toutes les prestations
        $select = '
            SELECT
                idJobService,
                idJobServiceTemplate,
                idProfessional,
                name,
                duration,
                description,
                price,
                (SELECT count(1) FROM JobServiceImage WHERE JobServiceImage.idJobService = JobService.idJobService) as jobServiceImagesCount
            FROM 
                JobService
            WHERE
                idProfessional = :idProfessional;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idProfessional' => $professional->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des prestations d'un professionnel",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceCollection = new ArrayCollection();
        foreach ($result as $jobServiceRow) {
            $jobService = new CompleteJobService();
            $jobService->idJobService = $jobServiceRow['idJobService'];
            $jobService->idJobServiceTemplate = $jobServiceRow['idJobServiceTemplate'];
            $jobService->idProfessional = $jobServiceRow['idProfessional'];
            $jobService->name = $jobServiceRow['name'];
            $jobService->duration = $jobServiceRow['duration'];
            $jobService->description = $jobServiceRow['description'];
            $jobService->price = $jobServiceRow['price'];
            $jobService->jobServiceImagesCount = $jobServiceRow['jobServiceImagesCount'];
            $jobServiceCollection->add($jobService);
        }

        return $jobServiceCollection;
    }
    
    /**
     * Retourne toutes les prestations selon un salon
     * 
     * @param Salon $salon
     * @return ArrayCollection
     */
    public function findBySalonId(Salon $salon)
    {
        // Récupère toutes les prestations
        $select = '
            SELECT
                JobService.idJobService,
                JobService.idJobServiceTemplate,
                JobService.idProfessional,
                JobService.name,
                JobService.duration,
                JobService.description,
                JobService.price,
                Account.accountImageFilename,
                Account.firstName,
                Account.lastName,
                (SELECT count(1) FROM JobServiceImage WHERE JobServiceImage.idJobService = JobService.idJobService) as jobServiceImagesCount,
                (SELECT GROUP_CONCAT(JobServiceImage.filePath) FROM JobServiceImage WHERE JobServiceImage.idJobService = JobService.idJobService) as jobServiceImages,
                (SELECT GROUP_CONCAT(CustomerCharacteristic.name) FROM JobServiceCustomerCharacteristic INNER JOIN CustomerCharacteristic ON CustomerCharacteristic.idCustomerCharacteristic = JobServiceCustomerCharacteristic.idCustomerCharacteristic WHERE JobServiceCustomerCharacteristic.idJobService = JobService.idJobService) as customerCharacteristicList,
                (SELECT MAX(rate) FROM Discount WHERE Discount.idSalon = Employee.idSalon) as maxDiscount
            FROM 
                JobService
            INNER JOIN Employee
                ON Employee.idEmployee = JobService.idProfessional
            INNER JOIN Account
                ON Account.idAccount = JobService.idProfessional
            WHERE
                Employee.idSalon = :salonId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':salonId' => $salon->getIdSalon()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des prestations d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceCollection = new ArrayCollection();
        foreach ($result as $jobServiceRow) {
            $jobService = new CompleteJobService();
            $jobService->idJobService = $jobServiceRow['idJobService'];
            $jobService->idJobServiceTemplate = $jobServiceRow['idJobServiceTemplate'];
            $jobService->idProfessional = $jobServiceRow['idProfessional'];
            $jobService->name = $jobServiceRow['name'];
            $jobService->duration = $jobServiceRow['duration'];
            $jobService->description = $jobServiceRow['description'];
            $jobService->price = $jobServiceRow['price'];
            $jobService->accountImageFilename = $jobServiceRow['accountImageFilename'];
            $jobService->accountFirstName = $jobServiceRow['firstName'];
            $jobService->accountLastName = $jobServiceRow['lastName'];
            $jobService->jobServiceImagesCount = $jobServiceRow['jobServiceImagesCount'];
            $jobService->jobServiceImages = $jobServiceRow['jobServiceImages'];
            $jobService->customerCharacteristicList = $jobServiceRow['customerCharacteristicList'];
            $jobService->maxDiscount = $jobServiceRow['maxDiscount'];
            $jobServiceCollection->add($jobService);
        }

        return $jobServiceCollection;
    }
    
    /**
     * Retourne toutes les prestations d'un modèle
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @return ArrayCollection
     */
    public function findByIdJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        // Récupère toutes les prestations
        $select = '
            SELECT
                idJobService,
                idJobServiceTemplate,
                idProfessional,
                name,
                duration,
                description,
                price
            FROM 
                JobService
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
                "Erreur lors de la récupération des prestations d'un template",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $jobServiceCollection = new ArrayCollection();
        foreach ($result as $jobServiceRow) {
            $jobService = $this->hydrateEntity($jobServiceRow);
            $jobServiceCollection->add($jobService);
        }

        return $jobServiceCollection;
    }
    
    /**
     * Retourne une prestation grace à son ID
     * 
     * @param JobService $jobService
     * @return JobService
     */
    public function findById(JobService $jobService)
    {
        // Récupère une prestation par son ID
        $select = '
            SELECT
                idJobService,
                idJobServiceTemplate,
                idProfessional,
                name,
                duration,
                description,
                price
            FROM 
                JobService
            WHERE
                idJobService = :idJobService;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idJobService' => $jobService->getIdJobService()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return null;
        }
        
        return $this->hydrateEntity($result->current());
    }
    
    /**
     * Création d'une prestation
     * 
     * @param JobService $jobService
     */
    public function create(JobService $jobService)
    {
        // Création d'une prestation
        $insert = '
            INSERT INTO JobService (
                idJobServiceTemplate,
                idProfessional,
                name,
                duration,
                description,
                price
            )
            VALUES (
                :idJobServiceTemplate,
                :idProfessional,
                :name,
                :duration,
                :description,
                :price
            )';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobService->getIdJobServiceTemplate(),
                ':idProfessional' => $jobService->getIdProfessional(),
                ':name' => $jobService->getName(),
                ':duration' => $jobService->getDuration(),
                ':description' => $jobService->getDescription(),
                ':price' => $jobService->getPrice(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une prestation",
                null,
                $exception
            );
        }
        
        $jobService->setIdJobService($this->db->getDriver()->getLastGeneratedValue());
    }
    
    /**
     * Met à jour une prestation
     * 
     * @param JobService $jobService
     */
    public function edit(JobService $jobService)
    {
        // Edition d'une prestation
        $update = '
            UPDATE
                JobService
            SET
                idJobServiceTemplate = :idJobServiceTemplate,
                name = :name,
                duration = :duration,
                description = :description,
                price = :price
            WHERE
                idJobService = :idJobService;';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobService->getIdJobServiceTemplate(),
                ':name' => $jobService->getName(),
                ':duration' => $jobService->getDuration(),
                ':description' => $jobService->getDescription(),
                ':price' => $jobService->getPrice(),
                ':idJobService' => $jobService->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification d'une prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Met à jour les prestations issues d'un modèle
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     */
    public function editFromJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        // Edition des prestations
        $update = '
            UPDATE
                JobService
            SET
                price = :price
            WHERE
                idJobServiceTemplate = :idJobServiceTemplate;';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':idJobServiceTemplate' => $jobServiceTemplate->getIdJobServiceTemplate(),
                ':price' => $jobServiceTemplate->getPrice(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification d'une prestation depuis un template",
                null,
                $exception
            );
        }
    }
    
    /**
     * Supprime une prestation
     * 
     * @param JobService $jobService
     */
    public function delete(JobService $jobService)
    {
        // Suppression d'une prestation
        $delete = '
            DELETE FROM 
                JobService
            WHERE
                idJobService = :idJobService;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idJobService' => $jobService->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'une prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Supprime toutes les prestations appartenant à un modèle
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     */
    public function deleteByIdJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        // Suppression des prestations d'un modèle
        $delete = '
            DELETE FROM 
                JobService
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
                "Erreur lors de la suppression des prestations d'un template",
                null,
                $exception
            );
        }
    }
    
    /**
     * Ajoute un type de prestation à une prestation
     * 
     * @param JobService $jobService
     * @param JobServiceType $jobServiceType
     */
    public function addJobServiceType(
        JobService $jobService, 
        JobServiceType $jobServiceType
    ) {
        // Ajout d'un type de prestation à une prestation
        $insert = '
            INSERT IGNORE INTO JobServiceJobServiceType (
                idJobService,
                idJobServiceType
            )
            VALUES (
                :idJobService,
                :idJobServiceType
            )';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idJobService' => $jobService->getIdJobService(),
                ':idJobServiceType' => $jobServiceType->getIdJobServiceType(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'un type de prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Vide les types d'une prestation
     * 
     * @param JobService $jobService
     */
    public function flushServiceType(JobService $jobService)
    {
        // Supprime les types de prestation
        $delete = '
            DELETE FROM 
                JobServiceJobServiceType
            WHERE
                idJobService = :idJobService;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idJobService' => $jobService->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression de tous les types d'une prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Ajoute une caracteristique utilisateur à une prestation
     * 
     * @param JobService $jobService
     * @param CustomerCharacteristic $customerCharacteristic
     */
    public function addCustomerCharacteristic(
        JobService $jobService,
        CustomerCharacteristic $customerCharacteristic
    ) {
        // Ajout une caractéristique utilisateur à une prestation
        $insert = '
            INSERT IGNORE INTO JobServiceCustomerCharacteristic (
                idJobService,
                idCustomerCharacteristic
            )
            VALUES (
                :idJobService,
                :idCustomerCharacteristic
            )';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idJobService' => $jobService->getIdJobService(),
                ':idCustomerCharacteristic' => $customerCharacteristic->getIdCustomerCharacteristic(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'une caracteristique à une prestation",
                null,
                $exception
            );
        }
    }
    
    /**
     * Vide les caractéristiques utilisateurs
     * 
     * @param JobService $jobService
     */
    public function flushCustomerCharacteristic(JobService $jobService)
    {
        // Supprime les caractéristique utilisateur
        $delete = '
            DELETE FROM 
                JobServiceCustomerCharacteristic
            WHERE
                idJobService = :idJobService;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idJobService' => $jobService->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression de toutes les caracteristiques d'une prestation",
                null,
                $exception
            );
        }
    }
    
    private function hydrateEntity(array $row)
    {
        $jobService = new JobService();
        $jobService->setIdJobService($row['idJobService']);
        $jobService->setIdJobServiceTemplate($row['idJobServiceTemplate']);
        $jobService->setIdProfessional($row['idProfessional']);
        $jobService->setName($row['name']);
        $jobService->setDuration($row['duration']);
        $jobService->setDescription($row['description']);
        $jobService->setPrice($row['price']);
        
        return $jobService;
    }
}
