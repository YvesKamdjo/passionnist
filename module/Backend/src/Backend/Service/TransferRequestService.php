<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Entity\Salon;
use Backend\Entity\Transaction;
use Backend\Entity\TransferRequest;
use Backend\Mapper\TransactionMapper;
use Backend\Mapper\TransferRequestMapper;
use Professionnal\Exception\TransferRequestAmountIsTooHigh;
use Zend\Log\Logger;

class TransferRequestService
{
    /* @var $logger Logger */

    private $logger;

    /* @var $transferRequestMapper TransferRequestMapper */
    private $transferRequestMapper;

    /* @var $transactionMapper TransactionMapper */
    private $transactionMapper;

    public function __construct($transferRequestMapper, $transactionMapper, $logger)
    {
        $this->transferRequestMapper = $transferRequestMapper;
        $this->transactionMapper = $transactionMapper;
        $this->logger = $logger;
    }

    /**
     * Création d'une demande de virement
     * 
     * @param array $transferRequestData
     */
    public function createTransferRequest(array $transferRequestData)
    {
        // Si le montant demandé dépasse le solde total
        if ($transferRequestData['amount'] > $transferRequestData['balance']) {
            throw new TransferRequestAmountIsTooHigh();
        }
        
        $transferRequest = new TransferRequest();
        $transferRequest->setIdApplicant($transferRequestData['idApplicant'])
            ->setApplicantIdentity($transferRequestData['applicantIdentity'])
            ->setIban($transferRequestData['iban'])
            ->setBic($transferRequestData['bic'])
            ->setAmount($transferRequestData['amount']);
        
        $transaction = new Transaction();
        $transaction->setAmount(-1 * ($transferRequestData['amount']));
        $transaction->setDescription('Demande de transfert');
        $transaction->setIdCreator($transferRequestData['idApplicant']);
        
        if (isset($transferRequestData['idSalon'])) {
            $transferRequest->setIdSalon($transferRequestData['idSalon']);
            $transaction->setIdSalon($transferRequestData['idSalon']);
        }
        elseif (isset($transferRequestData['idFreelance'])) {
            $transferRequest->setIdFreelance($transferRequestData['idFreelance']);
            $transaction->setIdFreelance($transferRequestData['idFreelance']);
        }
        else {
            return false;
        }

        try {
            $this->transactionMapper->create($transaction);
            $this->transferRequestMapper->create($transferRequest);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    public function findAllSalonTransferRequests($idSalon)
    {
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        return $this->transferRequestMapper->findAllSalonTransferRequests($salon);
    }
    
    public function findAllFreelanceTransferRequests($idFreelance)
    {
        $freelance = new Account();
        $freelance->setIdAccount($idFreelance);
        
        return $this->transferRequestMapper->findAllFreelanceTransferRequests($freelance);
    }
}
