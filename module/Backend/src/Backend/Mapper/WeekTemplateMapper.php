<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Entity\Account;
use Backend\Entity\WeekTemplate;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class WeekTemplateMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Récupère le template de semaine d'un compte
     * 
     * @param Account $account
     * @return WeekTemplate
     */
    public function findByAccountId(Account $account)
    {
        // Récupère le template
        $select = '
            SELECT
                idWeekTemplate,
                idAccount
            FROM
                WeekTemplate
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
                "Erreur lors de la récupération d'un template de dispo",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    public function create(Account $account)
    {
        // Récupère le template
        $select = '
            INSERT INTO 
                WeekTemplate (
                    idAccount
                )
            VALUES (
                :idAccount
            )
        ;';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idAccount' => $account->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'un template de dispo",
                null,
                $exception
            );
        }
    }
    
    private function hydrateEntity(array $row)
    {
        $entity = new WeekTemplate();
        $entity->setIdWeekTemplate($row['idWeekTemplate']);
        $entity->setIdAccount($row['idAccount']);
        
        return $entity;
    }
}
