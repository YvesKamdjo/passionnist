<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Administration\Infrastructure\DataTransferObject\AccountListItem;
use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\AccountType;
use Backend\Entity\Salon;
use Backend\Infrastructure\DataTransferObject\CompleteProfessional;
use Backend\Infrastructure\DataTransferObject\ProfessionalSearchResult;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class AccountMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retrouve un account grace à sont adresse email
     * 
     * @param Account $account
     * @return Account|null
     */
    public function findByEmail(Account $account)
    {
        // Récupère tous les tests
        $select = '
            SELECT
                idAccount,
                firstName,
                lastName,
                email,
                phone,
                password,
                address,
                zipcode,
                city,
                latitude,
                longitude,
                moveRange,
                biography,
                idReferral,
                accountImageFilename,
                qualificationFilename,
                isActive
            FROM 
                Account
            WHERE
                email = :email;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':email' => $account->getEmail(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'un compte",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    public function addNewPasswordRequest(Account $account, $creationDate)
    {
        // Création du compte
        $select = '
            INSERT INTO PasswordRequest (
                email,
                hash,
                creationDate
            )
            VALUES (
                :email,
                :hash,
                :creationDate
            )';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':email' => $account->getEmail(),
                ':hash' => md5($creationDate),
                ':creationDate' => $creationDate,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une demande de nouveau mot de passe",
                null,
                $exception
            );
        }
    }
    
    public function checkNewPasswordRequest($hash)
    {
        // Création du compte
        $select = '
            SELECT
                email
            FROM 
                PasswordRequest
            WHERE
                hash = :hash;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':hash' => $hash,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une demande de nouveau mot de passe",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }
        
        $row = $result->current();
        
        return $row['email'];
    }
    
    /**
     * Retourne un account grace à son idAccount
     * 
     * @param Account $account
     * @return type
     */
    public function findByIdAccount(Account $account)
    {
        // Récupère l'account
        $select = '
            SELECT
                idAccount,
                firstName,
                lastName,
                email,
                phone,
                password,
                address,
                zipcode,
                city,
                latitude,
                longitude,
                moveRange,
                biography,
                idReferral,
                accountImageFilename,
                qualificationFilename,
                isActive
            FROM 
                Account
            WHERE
                idAccount = :idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idAccount' => $account->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'un compte",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    public function saveLocation(Account $account)
    {
        // Mise à jour du compte
        $select = '
            UPDATE
                Account
            SET
                address = :address,
                latitude = :latitude,
                longitude = :longitude
            WHERE
                idAccount = :idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
                ':address' => $account->getAddress(),
                ':latitude' => $account->getLatitude(),
                ':longitude' => $account->getLongitude(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification de la localisation d'un compte",
                null,
                $exception
            );
        }
    }
    
    /**
     * Retourne les employés d'un salon
     * 
     * @param Salon $salon
     * @return ArrayCollection
     */
    public function findEmployeeBySalonId(Salon $salon)
    {
        // Récupère l'account
        $select = '
            SELECT
                Account.idAccount,
                Account.firstName,
                Account.lastName,
                Account.email,
                Account.phone,
                Account.password,
                Account.address,
                Account.zipcode,
                Account.city,
                Account.latitude,
                Account.longitude,
                Account.moveRange,
                Account.biography,
                Account.accountImageFilename,
                (SELECT 
                    COUNT(`Like`.`idProfessionnal`) 
                FROM 
                    `Like` 
                WHERE 
                    `Like`.`idProfessionnal` = `Account`.`idAccount`
                ) as likeCount,
                ROUND((SELECT 
                    AVG(`BookingComment`.`rate`) 
                FROM 
                    `BookingComment` 
                INNER JOIN `Booking` 
                    ON `Booking`.`idBooking` = `BookingComment`.`idBooking` 
                INNER JOIN `JobService` 
                    ON `JobService`.`idJobService` = `Booking`.`idJobService` 
                WHERE 
                    `JobService`.`idProfessional` = `Account`.`idAccount`)
                ) as rateAverage,
                (SELECT 
                    GROUP_CONCAT(filePath) 
                FROM 
                    `JobServiceImage` 
                INNER JOIN `JobService` 
                    ON `JobService`.`idJobService` = `JobServiceImage`.`idJobService` 
                WHERE 
                    `JobService`.`idProfessional` = `Account`.`idAccount` 
                LIMIT 
                    0, 4
                ) as jobServiceImageFilenameList
            FROM 
                Account
            INNER JOIN Employee
                ON Employee.idEmployee = Account.idAccount
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
                "Erreur lors de la récupération des employés d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountCollection = new ArrayCollection();
        foreach ($result as $accountRow) {
            $account = new CompleteProfessional();
            $account->idProfessional = $accountRow['idAccount'];
            $account->firstName = $accountRow['firstName'];
            $account->lastName = $accountRow['lastName'];
            $account->email = $accountRow['email'];
            $account->phone = $accountRow['phone'];
            $account->password = $accountRow['password'];
            $account->address = $accountRow['address'];
            $account->zipcode = $accountRow['zipcode'];
            $account->city = $accountRow['city'];
            $account->accountImageFilename = $accountRow['accountImageFilename'];
            $account->like = $accountRow['likeCount'];
            $account->rate = $accountRow['rateAverage'];
            $account->jobServiceImageFilenameList = $accountRow['jobServiceImageFilenameList'];
            
            $accountCollection->add($account);
        }

        return $accountCollection;
    }
    
    public function findBestProfessionalByCustomerId(Account $account)
    {
        // Récupère l'account
        $select = "
            SELECT
                Account.idAccount,
                Account.firstName,
                Account.lastName,
                Account.email,
                Account.phone,
                Account.password,
                Account.address,
                Account.zipcode,
                Account.city,
                Account.latitude,
                Account.longitude,
                Account.accountImageFilename,
                Account.biography,
                Account.isActive,
                Account.moveRange,
                (SELECT 
                    COUNT(`Like`.`idProfessionnal`) 
                FROM 
                    `Like` 
                WHERE
                    `Like`.`idProfessionnal` = `Account`.`idAccount`
                ) as likeCount,
                CONCAT(
                    ROUND(
                        ST_Distance(
                            geomfromtext(CONCAT('POINT(', Account.latitude, ' ', Account.longitude, ')')),
                            geomfromtext(CONCAT('POINT(', :latitude, ' ', :longitude, ')'))
                        ) * PI() / 180 * 6371 # Rayon de la Terre
                    )
                ) as distance
            FROM 
                Account
            LEFT JOIN Employee
                ON Employee.idEmployee = Account.idAccount
            LEFT JOIN Salon
                ON Salon.idSalon = Employee.idSalon
                AND Salon.isActive = true
            INNER JOIN Role
                ON Role.idAccount = Account.idAccount
                AND (
                       Role.idAccountType = :idAccountTypeEmployee
                    OR
                       Role.idAccountType = :idAccountTypeFreelance
                )
            WHERE
                Account.isActive = true
            HAVING (
                    distance < 40
                OR
                    distance < Account.moveRange
            )
            ORDER BY
                likeCount DESC
            LIMIT 0, 3;";

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':latitude' => $account->getLatitude(),
                ':longitude' => $account->getLongitude(),
                ':idAccountTypeEmployee' => AccountType::ACCOUNT_TYPE_EMPLOYEE,
                ':idAccountTypeFreelance' => AccountType::ACCOUNT_TYPE_FREELANCE,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des meilleurs employés autour d'un customer",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountCollection = new ArrayCollection();
        foreach ($result as $accountRow) {
            $account = new CompleteProfessional();
            $account->idProfessional = $accountRow['idAccount'];
            $account->firstName = $accountRow['firstName'];
            $account->lastName = $accountRow['lastName'];
            $account->email = $accountRow['email'];
            $account->phone = $accountRow['phone'];
            $account->password = $accountRow['password'];
            $account->address = $accountRow['address'];
            $account->zipcode = $accountRow['zipcode'];
            $account->city = $accountRow['city'];
            $account->accountImageFilename = $accountRow['accountImageFilename'];
            $account->biography = $accountRow['biography'];
            $account->like = $accountRow['likeCount'];
            
            $accountCollection->add($account);
        }
        
        return $accountCollection;
    }
    
    /**
     * Retourne un professionnel grace à son idAccount
     * 
     * @param Account $account
     * @return Account
     */
    public function findProfessionalByAccountId(Account $account)
    {
        // Récupère l'account
        $select = '
            SELECT
                Account.idAccount,
                Account.firstName,
                Account.lastName,
                Account.email,
                Account.phone,
                Account.password,
                Account.address,
                Account.zipcode,
                Account.city,
                Account.latitude,
                Account.longitude,
                Account.moveRange,
                Account.biography,
                Account.idReferral,
                Account.accountImageFilename,
                Account.qualificationFilename,
                Account.isActive
            FROM 
                Account
            INNER JOIN Role
                ON Role.idAccount = Account.idAccount
                AND (
                    idAccountType = :idEmployee
		OR
                    idAccountType = :idFreelance
                )
            WHERE 
                Account.idAccount = :idAccount
            GROUP BY
                Account.idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idAccount' => $account->getIdAccount(),
                ':idEmployee' => AccountType::ACCOUNT_TYPE_EMPLOYEE,
                ':idFreelance' => AccountType::ACCOUNT_TYPE_FREELANCE,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'un compte",
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
     * Récupère tous les comptes
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        // Récupère tous les salons
        $select = '
            SELECT
                Account.idAccount,
                firstName,
                lastName,
                email,
                phone,
                password,
                address,
                zipcode,
                city,
                Referral.label as referral,
                qualificationFilename,
                isActive,
                GROUP_CONCAT(
                    CONCAT(
                        AccountType.idAccountType,
                        ":",
                        AccountType.key
                    )
                ) as roles
            FROM 
                Account
            LEFT JOIN Referral
                ON Referral.idReferral = Account.idReferral
            INNER JOIN Role
                ON Role.idAccount = Account.idAccount
            INNER JOIN AccountType
                ON AccountType.idAccountType = Role.idAccountType
            GROUP BY
                Account.idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des comptes",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountCollection = new ArrayCollection();
        foreach ($result as $accountRow) {
            $account = new AccountListItem();
            $account->idAccount = $accountRow['idAccount'];
            $account->firstName = $accountRow['firstName'];
            $account->lastName = $accountRow['lastName'];
            $account->email = $accountRow['email'];
            $account->phone = $accountRow['phone'];
            $account->password = $accountRow['password'];
            $account->address = $accountRow['address'];
            $account->zipcode = $accountRow['zipcode'];
            $account->city = $accountRow['city'];
            $account->referral = $accountRow['referral'];
            $account->qualificationFileName = $accountRow['qualificationFilename'];
            $account->isActive = $accountRow['isActive'];
            
            $roleRow = explode(',', $accountRow['roles']);
            $account->roleList = [];
            
            foreach ($roleRow as $role) {
                $explodedRole = explode(':', $role);
                $account->roleList[$explodedRole[0]] = $explodedRole[1]; 
            }
            
            $accountCollection->add($account);
        }

        return $accountCollection;
    }
    
    /**
     * Récupère tous les comptes selon leur type de compte
     * 
     * @param AccountType $accountType
     * @return ArrayCollection
     */
    public function findAllByAccountTypeId(AccountType $accountType)
    {
        // Récupère tous les salons
        $select = '
            SELECT
                Account.idAccount,
                firstName,
                lastName,
                email,
                phone,
                password,
                address,
                zipcode,
                city,
                Referral.label as referral,
                qualificationFilename,
                isActive,
                group_concat(AccountType.idAccountType, ":", AccountType.key) as roles
            FROM 
                Account
            LEFT JOIN Referral
                ON Referral.idReferral = Account.idReferral
            INNER JOIN Role
                ON Role.idAccount = Account.idAccount
            INNER JOIN AccountType
                ON AccountType.idAccountType = Role.idAccountType
                AND AccountType.idAccountType = :idAccountType
            GROUP BY
                Account.idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idAccountType' => $accountType->getIdAccountType()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des comptes d'un type de compte",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountCollection = new ArrayCollection();
        foreach ($result as $accountRow) {
            $account = new AccountListItem();
            $account->idAccount = $accountRow['idAccount'];
            $account->firstName = $accountRow['firstName'];
            $account->lastName = $accountRow['lastName'];
            $account->email = $accountRow['email'];
            $account->phone = $accountRow['phone'];
            $account->password = $accountRow['password'];
            $account->address = $accountRow['address'];
            $account->zipcode = $accountRow['zipcode'];
            $account->city = $accountRow['city'];
            $account->referral = $accountRow['referral'];
            $account->qualificationFileName = $accountRow['qualificationFilename'];
            $account->isActive = $accountRow['isActive'];
            
            $roleRow = explode(',', $accountRow['roles']);
            $account->roleList = [];
            
            foreach ($roleRow as $role) {
                $explodedRole = explode(':', $role);
                $account->roleList[$explodedRole[0]] = $explodedRole[1]; 
            }
            
            $accountCollection->add($account);
        }

        return $accountCollection;
    }
    
    /**
     * Récupère tous les professionels aimés par un compte
     * 
     * @param Account $account
     * @return ArrayCollection
     */
    public function findAllLikedByAccountId(Account $account)
    {
        // Récupère les professionels
        $select = '
            SELECT
                Account.idAccount,
                Account.firstName,
                Account.lastName,
                IF(Salon.city IS NOT NULL, Salon.city, Account.city) as city,
                ROUND((
                    SELECT 
                        AVG(`BookingComment`.`rate`) 
                    FROM 
                        `BookingComment` 
                    INNER JOIN `Booking` 
                        ON `Booking`.`idBooking` = `BookingComment`.`idBooking` 
                    INNER JOIN `JobService` 
                        ON `JobService`.`idJobService` = `Booking`.`idJobService` 
                    WHERE 
                        `JobService`.`idProfessional` = `Account`.`idAccount`
                )) AS rateAverage,
                (
                    SELECT 
                        COUNT(`Like`.`idProfessionnal`) 
                    FROM 
                        `Like` 
                    WHERE 
                        `Like`.`idProfessionnal` = `Account`.`idAccount`
                ) AS likeCount,
                Account.accountImageFilename,
                (
                    SELECT 
                        GROUP_CONCAT(filePath) 
                    FROM 
                        `JobServiceImage` 
                    INNER JOIN `JobService` 
                        ON `JobService`.`idJobService` = `JobServiceImage`.`idJobService` 
                    WHERE 
                        `JobService`.`idProfessional` = `Account`.`idAccount` 
                    LIMIT 
                        0, 4
                ) AS jobServiceImageFilenameList,
                Account.creationDate
            FROM 
                Account
            INNER JOIN `Like`
                ON `Like`.idProfessionnal = Account.idAccount
                AND `Like`.idCustomer = :idCustomer
            LEFT JOIN Employee
                ON Employee.idEmployee = Account.idAccount
            LEFT JOIN Salon
                ON Salon.idSalon = Employee.idSalon;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idCustomer' => $account->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des professionnels lovés",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $searchResultCollection = new ArrayCollection();
        foreach ($result as $searchResultRow) {    
            $searchResult = new ProfessionalSearchResult();
            
            $searchResult->accountId = $searchResultRow['idAccount'];
            $searchResult->accountLike = $searchResultRow['likeCount'];
            $searchResult->accountLocation = $searchResultRow['city'];
            $searchResult->accountRate = $searchResultRow['rateAverage'];
            $searchResult->accountCreationDate = $searchResultRow['creationDate'];
            $searchResult->accountName = $searchResultRow['firstName']. ' ' .$searchResultRow['lastName'];
            $searchResult->accountImageFilename = $searchResultRow['accountImageFilename'];
            $searchResult->jobServiceImageFilenameList = $searchResultRow['jobServiceImageFilenameList'];
            
            $searchResultCollection->add($searchResult);
        }
        
        return $searchResultCollection;
    }
    
    /**
     * Active un compte
     * 
     * @param Account $account
     */
    public function activate(Account $account)
    {
        // Activation du compte
        $update = '
            UPDATE
                Account
            SET
                isActive = true
            WHERE
                idAccount = :idAccount';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'activation d'un compte",
                null,
                $exception
            );
        }
    }
    
    /**
     * Désactive un compte
     * 
     * @param Account $account
     */
    public function deactivate(Account $account)
    {
        // Activation du compte
        $update = '
            UPDATE
                Account
            SET
                isActive = false
            WHERE
                idAccount = :idAccount';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la désactivation d'un compte",
                null,
                $exception
            );
        }
    }
    
    /**
     * Crée le compte de l'utilisateur
     * 
     * @param Account $account
     */
    public function create(Account $account)
    {
        // Création du compte
        $select = '
            INSERT INTO Account (
                firstName,
                lastName,
                email,
                phone,
                password,
                idReferral,
                address,
                zipcode,
                city,
                latitude,
                longitude,
                isActive
            )
            VALUES (
                :firstName,
                :lastName,
                :email,
                :phone,
                :password,
                :idReferral,
                :address,
                :zipcode,
                :city,
                :latitude,
                :longitude,
                false
            )';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':firstName' => $account->getFirstName(),
                ':lastName' => $account->getLastName(),
                ':email' => $account->getEmail(),
                ':phone' => $account->getPhone(),
                ':password' => $account->getPassword(),
                ':idReferral' => $account->getIdReferral(),
                ':address' => $account->getAddress(),
                ':zipcode' => $account->getZipcode(),
                ':city' => $account->getCity(),
                ':latitude' => $account->getLatitude(),
                ':longitude' => $account->getLongitude(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'un compte",
                null,
                $exception
            );
        }
        
        $account->setIdAccount($this->db->getDriver()->getLastGeneratedValue());
    }
    
    /**
     * Mise à jour des données d'un account
     * 
     * @param Account $account
     */
    public function edit(Account $account)
    {
        // Mise à jour du compte
        $select = '
            UPDATE
                Account
            SET
                firstName = :firstName,
                lastName = :lastName,
                email = :email,
                phone = :phone,
                address = :address,
                zipcode = :zipcode,
                city = :city,
                latitude = :latitude,
                longitude = :longitude,
                moveRange = :moveRange,
                biography = :biography,
                accountImageFilename = :accountImageFilename,
                qualificationFilename = :qualificationFilename
            WHERE
                idAccount = :idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
                ':firstName' => $account->getFirstName(),
                ':lastName' => $account->getLastName(),
                ':email' => $account->getEmail(),
                ':phone' => $account->getPhone(),
                ':address' => $account->getAddress(),
                ':zipcode' => $account->getZipcode(),
                ':city' => $account->getCity(),
                ':latitude' => $account->getLatitude(),
                ':longitude' => $account->getLongitude(),
                ':moveRange' => $account->getMoveRange(),
                ':biography' => $account->getBiography(),
                ':accountImageFilename' => $account->getAccountImageFilename(),
                ':qualificationFilename' => $account->getQualificationFilename(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification d'un compte",
                null,
                $exception
            );
        }
    }
    
    public function editPasswordByEmail(Account $account)
    {
        // Mise à jour du compte
        $select = '
            UPDATE
                Account
            SET
                password = :password
            WHERE
                email = :email;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':password' => $account->getPassword(),
                ':email' => $account->getEmail(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification d'un mot de passe",
                null,
                $exception
            );
        }
    }
    
    /**
     * Ajoute un type de compte à un account
     * 
     * @param Account $account
     * @param AccountType $accountType
     */
    public function addAccountType(Account $account, AccountType $accountType)
    {
        // Récupère tous les tests
        $select = '
            INSERT INTO Role (
                idAccount,
                idAccountType
            )
            VALUES (
                :idAccount,
                :idAccountType
            )';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
                ':idAccountType' => $accountType->getIdAccountType(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'un type de compte à un compte",
                null,
                $exception
            );
        }
    }
    
    /**
     * Enregistre le nom du fichier du diplome
     * 
     * @param Account $account
     */
    public function saveQualification(Account $account)
    {
        // Récupère tous les tests
        $select = '
            UPDATE 
                Account
            SET
                qualificationFilename = :qualificationFilename
            WHERE
                idAccount = :idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':qualificationFilename' => $account->getQualificationFilename(),
                ':idAccount' => $account->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'un diplome",
                null,
                $exception
            );
        }
    }
    
    /**
     * Enregistre le nom de l'image de profil
     * 
     * @param Account $account
     */
    public function saveAccountImage(Account $account)
    {
        // Récupère tous les tests
        $select = '
            UPDATE 
                Account
            SET
                accountImageFilename = :accountImageFilename
            WHERE
                idAccount = :idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':accountImageFilename' => $account->getAccountImageFilename(),
                ':idAccount' => $account->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'une image de profil",
                null,
                $exception
            );
        }
    }
    
    /**
     * Retire une permission à un utilisateur
     * 
     * @param Account $account
     * @param string $permissionKey
     */
    public function removePermission(Account $account, $permissionKey)
    {
        // Récupère tous les tests
        $delete = '
            DELETE FROM
                AccountPermission
            WHERE
                idAccount = :idAccount
            AND
                idPermission = :idPermission;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
                ':idPermission' => $permissionKey,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'une permission",
                null,
                $exception
            );
        }
    }
    
    /**
     * Récupère la liste des professionnels likés par un customer
     * 
     * @param Account $customer
     * @return ArrayCollection
     * @throws MapperException
     */
    public function findProfessionalLikedByCustomerId(Account $customer)
    {
        // Récupère les professionnels
        $select = '                
            SELECT
                Account.idAccount,
                Account.firstName,
                Account.lastName,
                Account.email,
                Account.phone,
                Account.password,
                Account.address,
                Account.zipcode,
                Account.city,
                Account.latitude,
                Account.longitude,
                Account.moveRange,
                Account.biography,
                Account.idReferral,
                Account.accountImageFilename,
                Account.qualificationFilename,
                Account.isActive
            FROM
                Account
            INNER JOIN `Like` 
                ON 
                    `Like`.idProfessionnal = Account.idAccount
                AND 
                    `Like`.idCustomer = :customerId
            GROUP BY
                Account.idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':customerId' => $customer->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des professionnels favoris d'un customer",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountCollection = new ArrayCollection();
        foreach ($result as $accountRow) {
            $account = $this->hydrateEntity($accountRow);
            $accountCollection->add($account);
        }

        return $accountCollection;
    }

    /**
     * Récupère la liste des customers qui ont liké un pro
     * 
     * @param Account $professional
     * @return ArrayCollection
     * @throws MapperException
     */
    public function findCustomerWhoLikeByProfessionalId(Account $professional)
    {
        // Récupère les customer
        $select = '                
            SELECT
                Account.idAccount,
                Account.firstName,
                Account.lastName,
                Account.email,
                Account.phone,
                Account.password,
                Account.address,
                Account.zipcode,
                Account.city,
                Account.latitude,
                Account.longitude,
                Account.moveRange,
                Account.biography,
                Account.idReferral,
                Account.accountImageFilename,
                Account.qualificationFilename,
                Account.isActive
            FROM
                Account
            INNER JOIN `Like` 
                ON 
                    `Like`.idCustomer = Account.idAccount
                AND 
                    `Like`.idProfessionnal = :professionalId
            GROUP BY
                Account.idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':professionalId' => $professional->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des customer ayant mis en favoris un pro",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountCollection = new ArrayCollection();
        foreach ($result as $accountRow) {
            $account = $this->hydrateEntity($accountRow);
            $accountCollection->add($account);
        }

        return $accountCollection;
    }

    public function findAttachmentRequestByManagerInformations(Account $manager, Salon $salon)
    {
        // Ajoute l'invitation
        $select = '
            SELECT
                Account.idAccount,
                Account.firstName,
                Account.lastName,
                Account.email,
                Account.phone,
                Account.password,
                Account.address,
                Account.zipcode,
                Account.city,
                Account.latitude,
                Account.longitude,
                Account.moveRange,
                Account.biography,
                Account.idReferral,
                Account.accountImageFilename,
                Account.qualificationFilename,
                Account.isActive
            FROM
                AttachmentRequest
            INNER JOIN Account
                ON Account.idAccount = AttachmentRequest.idEmployee
            WHERE
                AttachmentRequest.idManager = :idManager
            OR
                AttachmentRequest.managerEmail = :emailManager
            OR
                AttachmentRequest.idSalon = :idSalon
            GROUP BY
                Account.idAccount;
            ';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idManager' => $manager->getIdAccount(),
                ':emailManager' => $manager->getEmail(),
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des demandes de rattachement à un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountCollection = new ArrayCollection();
        foreach ($result as $accountRow) {
            $account = $this->hydrateEntity($accountRow);
            $accountCollection->add($account);
        }

        return $accountCollection;
    }

    public function addLikeOnProfessional(Account $professional, Account $customer)
    {
        // Ajoute l'invitation
        $select = '
            INSERT IGNORE INTO `Like` (
                idProfessionnal,
                idCustomer
            )
            VALUES (
                :professionalId,
                :customerId
            );
            ';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':professionalId' => $professional->getIdAccount(),
                ':customerId' => $customer->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'un like",
                null,
                $exception
            );
        }
    }

    public function removeLikeOnProfessional(Account $professional, Account $customer)
    {
        // Ajoute l'invitation
        $select = '
            DELETE FROM 
                `Like`
            WHERE
                idProfessionnal = :professionalId
            AND
                idCustomer = :customerId;
            ';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':professionalId' => $professional->getIdAccount(),
                ':customerId' => $customer->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'un like",
                null,
                $exception
            );
        }
    }

    public function deletePasswordRequestEmail($email)
    {
        // Ajoute l'invitation
        $select = '
            DELETE FROM 
                PasswordRequest
            WHERE
                email = :email;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':email' => $email,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'une requete de nouveau mot de passe",
                null,
                $exception
            );
        }
    }
    
    public function isProfessionalLikedByCustomer(
        Account $customer, 
        Account $professional
    ) {
        // Récupère tous les tests
        $select = '
            SELECT
                idProfessionnal,
                idCustomer
            FROM 
                `Like`
            WHERE
                idCustomer = :customerId
            AND
                idProfessionnal = :professionalId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':customerId' => $customer->getIdAccount(),
                ':professionalId' => $professional->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la vérification d'un like",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return false;
        }

        return true;
    }
    
    private function hydrateEntity(array $row)
    {
        $account = new Account();
        $account->setIdAccount($row['idAccount']);
        $account->setFirstName($row['firstName']);
        $account->setLastName($row['lastName']);
        $account->setEmail($row['email']);
        $account->setPhone($row['phone']);
        $account->setPassword($row['password']);
        $account->setAddress($row['address']);
        $account->setZipcode($row['zipcode']);
        $account->setCity($row['city']);
        $account->setLatitude($row['latitude']);
        $account->setLongitude($row['longitude']);
        $account->setMoveRange($row['moveRange']);
        $account->setBiography($row['biography']);
        $account->setIdReferral($row['idReferral']);
        $account->setAccountImageFilename($row['accountImageFilename']);
        $account->setQualificationFilename($row['qualificationFilename']);
        $account->setIsActive($row['isActive']);
        
        return $account;
    }
}
