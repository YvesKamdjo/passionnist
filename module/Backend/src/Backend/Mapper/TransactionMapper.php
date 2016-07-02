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
use Backend\Entity\Transaction;
use Backend\Infrastructure\DataTransferObject\TransactionListResult;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class TransactionMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Crée une demande de virement
     * 
     * @return ArrayCollection
     */
    public function create(Transaction $transaction)
    {
        // Récupère tous les types de comptes
        $insert = '
            INSERT INTO
                Transaction (
                    idSalon,
                    idFreelance,
                    amount,
                    description,
                    idCreator
                )
            VALUES (
                :idSalon,
                :idFreelance,
                :amount,
                :description,
                :creatorId
            );';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idSalon' => $transaction->getIdSalon(),
                ':idFreelance' => $transaction->getIdFreelance(),
                ':amount' => $transaction->getAmount(),
                ':description' => $transaction->getDescription(),
                ':creatorId' => $transaction->getIdCreator(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une transaction",
                null,
                $exception
            );
        }
        
        $transaction->setIdTransaction($this->db->getDriver()->getLastGeneratedValue());
    }
    
    /**
     * Retourne le solde total d'un salon
     * 
     * @param Salon $salon
     * @return float
     */
    public function calculateSalonTotalBalance(Salon $salon)
    {
        // Récupère le solde total du salon
        $select = '
            SELECT
                ROUND(SUM(amount), 2) as balance
            FROM 
                Transaction
            WHERE
                idSalon = :idSalon;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors du calcul de la balance d'un salon",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        $row = $result->current();
        
        return (float) $row['balance'];
    }
    
    public function calculateSalonCurrentMonthSales(Salon $salon)
    {
        // Récupère le solde total du salon
        $select = '
            SELECT
                ROUND(SUM(if(amount > 0, amount, 0)), 2) as balance
            FROM 
                Transaction
            WHERE
                idSalon = :idSalon
            AND
                MONTH(creationDate) = MONTH(NOW())
            AND
                YEAR(creationDate) = YEAR(NOW());';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors du calcul de la balance d'un salon",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        $row = $result->current();
        
        return (float) $row['balance'];
    }
    
    public function calculateFreelanceCurrentMonthSales(Account $freelance)
    {
        // Récupère le solde total du freelance
        $select = '
            SELECT
                ROUND(SUM(if(amount > 0, amount, 0)), 2) as balance
            FROM 
                Transaction
            WHERE
                idFreelance = :idFreelance
            AND
                MONTH(creationDate) = MONTH(NOW())
            AND
                YEAR(creationDate) = YEAR(NOW());';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idFreelance' => $freelance->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors du calcul de la balance d'un freelance",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        $row = $result->current();
        
        return (float) $row['balance'];
    }
    
    public function calculateSalonGlobalSales(Salon $salon)
    {
        // Récupère le solde total du salon
        $select = '
            SELECT
                ROUND(SUM(if(amount > 0, amount, 0)), 2) as balance
            FROM 
                Transaction
            WHERE
                idSalon = :idSalon;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idSalon' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors du calcul de la balance d'un salon",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        $row = $result->current();
        
        return (float) $row['balance'];
    }
    
    public function calculateFreelanceGlobalSales(Account $freelance)
    {
        // Récupère le solde total du freelance
        $select = '
            SELECT
                ROUND(SUM(if(amount > 0, amount, 0)), 2) as balance
            FROM 
                Transaction
            WHERE
                idFreelance = :idFreelance;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idFreelance' => $freelance->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors du calcul de la balance d'un freelance",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        $row = $result->current();
        
        return (float) $row['balance'];
    }
    
    /**
     * Retourne le solde total du freelance
     * 
     * @param Account $freelance
     * @return float
     */
    public function calculateFreelanceTotalBalance(Account $freelance)
    {
        // Récupère le solde total du freelance
        $select = '
            SELECT
                ROUND(SUM(amount), 2) as balance
            FROM 
                Transaction
            WHERE
                idFreelance = :idFreelance;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idFreelance' => $freelance->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors du calcul de la balance d'un freelance",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        $row = $result->current();
        
        return (float) $row['balance'];
    }
    
    public function findBySalonId(Salon $salon)
    {
        // Récupère les transactions
        $select = '
            SELECT
                idTransaction,
                idSalon,
                idFreelance,
                amount,
                description,
                idCreator,
                Transaction.creationDate,
                firstName,
                lastName
            FROM 
                Transaction
            LEFT JOIN Account
                ON Account.idAccount = Transaction.idCreator
            WHERE
                idSalon = :salonId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':salonId' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des transactions d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $transactionCollection = new ArrayCollection();
        foreach ($result as $transactionRow) {
            $entity = new TransactionListResult();
            $entity->idTransaction = $transactionRow['idTransaction'];
            $entity->idSalon = $transactionRow['idSalon'];
            $entity->idFreelance = $transactionRow['idFreelance'];
            $entity->amount = $transactionRow['amount'];
            $entity->description = $transactionRow['description'];
            $entity->idCreator = $transactionRow['idCreator'];
            $entity->creationDate = $transactionRow['creationDate'];
            $entity->firstName = $transactionRow['firstName'];
            $entity->lastName = $transactionRow['lastName'];
            
            $transactionCollection->add($entity);
        }

        return $transactionCollection;
    }
    
    public function findByFreelanceId(Account $freelance)
    {
        // Récupère le solde total du freelance
        $select = '
            SELECT
                idTransaction,
                idSalon,
                idFreelance,
                amount,
                description,
                idCreator,
                Transaction.creationDate,
                firstName,
                lastName
            FROM 
                Transaction
            LEFT JOIN Account
                ON Account.idAccount = Transaction.idCreator
            WHERE
                idFreelance = :freelanceId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':freelanceId' => $freelance->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des transactions d'un freelance",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $transactionCollection = new ArrayCollection();
        foreach ($result as $transactionRow) {
            $entity = new TransactionListResult();
            $entity->idTransaction = $transactionRow['idTransaction'];
            $entity->idSalon = $transactionRow['idSalon'];
            $entity->idFreelance = $transactionRow['idFreelance'];
            $entity->amount = $transactionRow['amount'];
            $entity->description = $transactionRow['description'];
            $entity->idCreator = $transactionRow['idCreator'];
            $entity->creationDate = $transactionRow['creationDate'];
            $entity->firstName = $transactionRow['firstName'];
            $entity->lastName = $transactionRow['lastName'];
            
            $transactionCollection->add($entity);
        }

        return $transactionCollection;
    }
}
