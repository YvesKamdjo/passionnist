<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\AccountType;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class AccountTypeMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne tous les types de compte
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        // Récupère tous les types de comptes
        $select = '
            SELECT
                idAccountType,
                `key`
            FROM 
                AccountType;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de tous les types de compte",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountTypeCollection = new ArrayCollection();
        foreach ($result as $accountTypeRow) {
            $accountType = $this->hydrateEntity($accountTypeRow);
            $accountTypeCollection->add($accountType);
        }

        return $accountTypeCollection;
    }
    
    /**
     * Retourne tous les types de compte d'un compte donné
     * 
     * @return ArrayCollection
     */
    public function findAllByIdAccount(Account $account)
    {
        // Récupère tous les types de comptes du compte donné
        $select = '
            SELECT
                AccountType.idAccountType,
                AccountType.`key`
            FROM 
                AccountType
            INNER JOIN Role
                ON Role.idAccountType = AccountType.idAccountType
                AND Role.idAccount = :idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idAccount' => $account->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de tous les types de compte d'un compte",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $accountTypeCollection = new ArrayCollection();
        foreach ($result as $accountTypeRow) {
            $accountType = $this->hydrateEntity($accountTypeRow);
            $accountTypeCollection->add($accountType);
        }

        return $accountTypeCollection;
    }
    
    private function hydrateEntity(array $row)
    {
        $accountType = new AccountType();
        $accountType->setIdAccountType($row['idAccountType']);
        $accountType->setKey($row['key']);
        
        return $accountType;
    }
}
