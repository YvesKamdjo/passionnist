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
use Backend\Entity\TransferRequest;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class TransferRequestMapper
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
    public function create(TransferRequest $transfertRequest)
    {
        // Récupère tous les types de comptes
        $insert = '
            INSERT INTO
                TransferRequest (
                    idApplicant,
                    idSalon,
                    idFreelance,
                    applicantIdentity,
                    iban,
                    bic,
                    amount
                )
            VALUES (
                :idApplicant,
                :idSalon,
                :idFreelance,
                :applicantIdentity,
                :iban,
                :bic,
                :amount
            );';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idApplicant' => $transfertRequest->getIdApplicant(),
                ':idSalon' => $transfertRequest->getIdSalon(),
                ':idFreelance' => $transfertRequest->getIdFreelance(),
                ':applicantIdentity' => $transfertRequest->getApplicantIdentity(),
                ':iban' => $transfertRequest->getIban(),
                ':bic' => $transfertRequest->getBic(),
                ':amount' => $transfertRequest->getAmount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une demande de virement",
                null,
                $exception
            );
        }

        $transfertRequest->setIdTransferRequest($this->db->getDriver()->getLastGeneratedValue());
    } 
    
    public function findAllSalonTransferRequests(Salon $salon)
    {
        // Récupère tous les salons
        $select = '
            SELECT
                TransferRequest.idTransferRequest,
                TransferRequest.idApplicant,
                TransferRequest.idSalon,
                TransferRequest.idFreelance,
                TransferRequest.applicantIdentity,
                TransferRequest.iban,
                TransferRequest.bic,
                TransferRequest.amount,
                TransferRequest.creationDate
            FROM 
                TransferRequest
            WHERE
                idSalon = :idSalon;';

        $statement = $this->db->createStatement($select);
        $result = $statement->execute([
            ':idSalon' => $salon->getIdSalon()
        ]);
        
        $transferRequestCollection = new ArrayCollection();
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return $transferRequestCollection;
        }
        
        // Peuplement de la collection
        foreach ($result as $transferRequestRow) {
            $transferRequest = $this->hydrateEntity($transferRequestRow);
            $transferRequestCollection->add($transferRequest);
        }

        return $transferRequestCollection;
    }
    
    public function findAllFreelanceTransferRequests(Account $freelance)
    {
        // Récupère tous les salons
        $select = '
            SELECT
                TransferRequest.idTransferRequest,
                TransferRequest.idApplicant,
                TransferRequest.idSalon,
                TransferRequest.idFreelance,
                TransferRequest.applicantIdentity,
                TransferRequest.iban,
                TransferRequest.bic,
                TransferRequest.amount,
                TransferRequest.creationDate
            FROM 
                TransferRequest
            WHERE
                idFreelance = :idFreelance;';

        $statement = $this->db->createStatement($select);
        $result = $statement->execute([
            ':idFreelance' => $freelance->getIdAccount()
        ]);
        
        $transferRequestCollection = new ArrayCollection();
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return $transferRequestCollection;
        }
        
        // Peuplement de la collection
        foreach ($result as $transferRequestRow) {
            $transferRequest = $this->hydrateEntity($transferRequestRow);
            $transferRequestCollection->add($transferRequest);
        }

        return $transferRequestCollection;
    }
    
    public function hydrateEntity(array $row)
    {
        $transferRequest = new TransferRequest();
        $transferRequest->setIdTransferRequest($row['idTransferRequest']);
        $transferRequest->setIdApplicant($row['idApplicant']);
        $transferRequest->setIdFreelance($row['idFreelance']);
        $transferRequest->setApplicantIdentity($row['applicantIdentity']);
        $transferRequest->setIban($row['iban']);
        $transferRequest->setBic($row['bic']);
        $transferRequest->setAmount($row['amount']);
        $transferRequest->setCreationDate($row['creationDate']);
        
        return $transferRequest;
    }
}
