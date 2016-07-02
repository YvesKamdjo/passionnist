<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Entity\Account;
use Backend\Entity\AttachmentRequest;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class AttachmentRequestMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Crée une invitation
     * 
     * @param AttachmentRequest $attachmentRequest
     */
    public function create(AttachmentRequest $attachmentRequest)
    {
        // Ajoute l'invitation
        $insert = '
            INSERT INTO AttachmentRequest (
                    idEmployee,
                    idManager,
                    managerEmail,
                    idSalon
                )
            VALUES (
                :idEmployee,
                :idManager,
                :managerEmail,
                :idSalon
            );';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idEmployee' => $attachmentRequest->getIdEmployee(),
                ':idManager' => $attachmentRequest->getIdManager(),
                ':managerEmail' => $attachmentRequest->getManagerEmail(),
                ':idSalon' => $attachmentRequest->getIdSalon()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une demande de rattachement",
                null,
                $exception
            );
        }
    }
    
    public function flushEmployeeRequest(Account $employee)
    {
        // Suppression 
        $delete = '
            DELETE FROM 
                AttachmentRequest
            WHERE
                idEmployee = :idEmployee;';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idEmployee' => $employee->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression de toutes les demandes de rattachement d'un utilisateur",
                null,
                $exception
            );
        }
    }
    
    private function hydrateEntity(array $row)
    {
        
    }
}
