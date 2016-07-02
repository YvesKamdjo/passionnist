<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\Salon;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class SalonMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Active un salon
     * 
     * @param Salon $salon
     */
    public function activate(Salon $salon)
    {
        // Activation du salon
        $update = '
            UPDATE
                Salon
            SET
                isActive = true
            WHERE
                idSalon = :idSalon';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'activation d'un salon",
                null,
                $exception
            );
        }
    }

    /**
     * Désactive un salon
     * 
     * @param Salon $salon
     */
    public function deactivate(Salon $salon)
    {
        // Activation du salon
        $update = '
            UPDATE
                Salon
            SET
                isActive = false
            WHERE
                idSalon = :idSalon';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la désactivation d'un salon",
                null,
                $exception
            );
        }
    }
    
    /**
     * Retourne un salon par l'idAccount de son manager
     * 
     * @param Account $manager
     * @return Salon|null
     */
    public function findByManagerIdAccount(Account $manager)
    {
        // Récupère tous les tests
        $select = '
            SELECT
                Salon.idSalon,
                Salon.name,
                Salon.address,
                Salon.zipcode,
                Salon.city,
                Salon.latitude,
                Salon.longitude,
                Salon.certificateFilename,
                Salon.isActive
            FROM 
                Salon
            INNER JOIN Manager
                ON Manager.idSalon = Salon.idSalon
                AND Manager.idManager = :idManager;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idManager' => $manager->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération du salon d'un manager",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }

    /**
     * Retourne un salon par l'idAccount de son employé
     * 
     * @param Account $employee
     * @return Salon|null
     */
    public function findByEmployeeIdAccount(Account $employee)
    {
        // Récupère tous les tests
        $select = '
            SELECT
                Salon.idSalon,
                Salon.name,
                Salon.address,
                Salon.zipcode,
                Salon.city,
                Salon.latitude,
                Salon.longitude,
                Salon.certificateFilename,
                Salon.isActive
            FROM 
                Salon
            INNER JOIN Employee
                ON Employee.idSalon = Salon.idSalon
                AND Employee.idEmployee = :idEmployee;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idEmployee' => $employee->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération du salon d'un employé",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    /**
     * Récupère un salon par son ID
     * 
     * @param Salon $salon
     * @return Salon
     */
    public function findById(Salon $salon)
    {
        // Récupère le salon
        $select = '
            SELECT
                Salon.idSalon,
                Salon.name,
                Salon.address,
                Salon.zipcode,
                Salon.city,
                Salon.latitude,
                Salon.longitude,
                Salon.certificateFilename,
                Salon.isActive
            FROM 
                Salon
            WHERE
                Salon.idSalon = :idSalon;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'un salon",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    /**
     * Permet de créer un salon
     * 
     * @param Salon $salon
     */
    public function createSalon(Salon $salon)
    {
        // Création du salon
        $select = '
            INSERT INTO Salon (
                name,
                address,
                zipcode,
                city,
                latitude,
                longitude
            )
            VALUES (
                :name,
                :address,
                :zipcode,
                :city,
                :latitude,
                :longitude
            )';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':name' => $salon->getName(),
                ':address' => $salon->getAddress(),
                ':zipcode' => $salon->getZipcode(),
                ':city' => $salon->getCity(),
                ':latitude' => $salon->getLatitude(),
                ':longitude' => $salon->getLongitude(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'un salon",
                null,
                $exception
            );
        }
        
        $salon->setIdSalon($this->db->getDriver()->getLastGeneratedValue());
    }
    
    /**
     * Récupère tous les salons
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        // Récupère tous les salons
        $select = '
            SELECT
                Salon.idSalon,
                Salon.name,
                Salon.address,
                Salon.zipcode,
                Salon.city,
                Salon.latitude,
                Salon.longitude,
                Salon.certificateFilename,
                Salon.isActive
            FROM 
                Salon;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de tous les salons",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $salonCollection = new ArrayCollection();
        foreach ($result as $salonRow) {
            $salon = $this->hydrateEntity($salonRow);
            $salonCollection->add($salon);
        }

        return $salonCollection;
    }
    
    /**
     * Récupère tous les salons actifs
     * 
     * @return ArrayCollection
     */
    public function findAllActive()
    {
        // Récupère tous les salons
        $select = '
            SELECT
                Salon.idSalon,
                Salon.name,
                Salon.address,
                Salon.zipcode,
                Salon.city,
                Salon.latitude,
                Salon.longitude,
                Salon.certificateFilename,
                Salon.isActive
            FROM 
                Salon
            WHERE
                Salon.isActive = true;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de tous les salons actifs",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $salonCollection = new ArrayCollection();
        foreach ($result as $salonRow) {
            $salon = $this->hydrateEntity($salonRow);
            $salonCollection->add($salon);
        }

        return $salonCollection;
    }
    
    /**
     * Récupère tous les salons inactifs
     * 
     * @return ArrayCollection
     */
    public function findAllInactive()
    {
        // Récupère tous les salons
        $select = '
            SELECT
                Salon.idSalon,
                Salon.name,
                Salon.address,
                Salon.zipcode,
                Salon.city,
                Salon.latitude,
                Salon.longitude,
                Salon.certificateFilename,
                Salon.isActive
            FROM 
                Salon
            WHERE
                Salon.isActive = false;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de tous les salons inactifs",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $salonCollection = new ArrayCollection();
        foreach ($result as $salonRow) {
            $salon = $this->hydrateEntity($salonRow);
            $salonCollection->add($salon);
        }

        return $salonCollection;
    }
    
    /**
     * Modifie les données d'un salon
     * 
     * @param Salon $salon
     */
    public function editSalon(Salon $salon)
    {
        // Edition du salon
        $select = '
            UPDATE
                Salon
            SET
                name = :name,
                address = :address,
                zipcode = :zipcode,
                city = :city,
                latitude = :latitude,
                longitude = :longitude
            WHERE
                idSalon = :idSalon;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
                ':name' => $salon->getName(),
                ':address' => $salon->getAddress(),
                ':zipcode' => $salon->getZipcode(),
                ':city' => $salon->getCity(),
                ':latitude' => $salon->getLatitude(),
                ':longitude' => $salon->getLongitude(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification d'un salon",
                null,
                $exception
            );
        }
    }
    
    /**
     * Permet d'ajouter un gérant à un salon
     * 
     * @param Salon $salon
     * @param Account $manager
     */
    public function addManager(Salon $salon, Account $manager)
    {
        // Création de la relation Salon <-> Gérant
        $select = '
            INSERT INTO Manager (
                idSalon,
                idManager
            )
            VALUES (
                :idSalon,
                :idManager
            )';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
                ':idManager' => $manager->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'un manager à un salon",
                null,
                $exception
            );
        }
    }
        
    /**
     * Enregistre le nom de fichier du k-bis
     * 
     * @param Salon $salon
     */
    public function saveCertificate(Salon $salon)
    {
        // Met à jour le k-bis
        $update = '
            UPDATE 
                Salon
            SET
                certificateFilename = :certificateFilename
            WHERE
                idSalon = :idSalon;';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':certificateFilename' => $salon->getCertificateFilename(),
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'enregistrement du k-bis d'un salon",
                null,
                $exception
            );
        }
    }
    
    /**
     * Permet d'ajouter un employé à un salon
     * 
     * @param Salon $salon
     * @param Account $employee
     */
    public function addEmployee(Salon $salon, Account $employee)
    {
        // Création de la relation Salon <-> Employé
        $select = '
            INSERT INTO Employee (
                idSalon,
                idEmployee
            )
            VALUES (
                :idSalon,
                :idEmployee
            )';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
                ':idEmployee' => $employee->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors du rattachement d'un employé à un salon",
                null,
                $exception
            );
        }
    }
    
    private function hydrateEntity(array $row)
    {
        $account = new Salon();
        $account->setIdSalon($row['idSalon']);
        $account->setName($row['name']);
        $account->setAddress($row['address']);
        $account->setZipcode($row['zipcode']);
        $account->setCity($row['city']);
        $account->setLatitude($row['latitude']);
        $account->setLongitude($row['longitude']);
        $account->setCertificateFilename($row['certificateFilename']);
        $account->setIsActive($row['isActive']);
        
        return $account;
    }
}
