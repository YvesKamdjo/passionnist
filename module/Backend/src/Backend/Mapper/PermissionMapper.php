<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Entity\Account;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class PermissionMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Récupère les permission d'un utilisateur à partir de son id
     * 
     * @param Account $account
     * @return array
     */
    public function findByIdAccount(Account $account)
    {        
        // Récupère toutes les permissions d'un utilisateur
        $select = '
            SELECT
                idAccount,
                idPermission
            FROM 
                AccountPermission
            WHERE
                idAccount = :idAccount;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idAccount' => $account->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des permissions d'un account",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return [];
        }
        
        // Peuplement de la liste des permissions
        $accountPermissions = [];
        foreach ($result as $permissionRow) {
            $accountPermissions[] = $permissionRow['idPermission'];
        }

        return $accountPermissions;
    }
    
    /**
     * Ajoute une permission à un utilisateur
     * 
     * @param Account $account
     * @param string $idPermission
     */
    public function addAccountPermission(Account $account, $idPermission)
    {
        // Ajoute une permission à l'utilisateur
        $select = '
            INSERT IGNORE INTO AccountPermission (
                idAccount,
                idPermission
            )
            VALUES (
                :idAccount,
                :idPermission
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
                ':idPermission' => $idPermission
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'ajout d'une permissions à un account",
                null,
                $exception
            );
        }
    }
}
