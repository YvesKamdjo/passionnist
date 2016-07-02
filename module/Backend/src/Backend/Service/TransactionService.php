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
use Backend\Mapper\TransactionMapper;
use Zend\Log\Logger;

class TransactionService
{
    /* @var $logger Logger */

    private $logger;

    /* @var $transactionMapper TransactionMapper */
    private $transactionMapper;

    public function __construct($transactionMapper, $logger)
    {
        $this->transactionMapper = $transactionMapper;
        $this->logger = $logger;
    }

    /**
     * Création d'une transaction
     * 
     * @param array $transactionData
     */
    public function createTransaction(array $transactionData)
    {
        $transaction = new Transaction();
        $transaction->setIdSalon($transactionData['idSalon'])
            ->setIdFreelance($transactionData['idFreelance'])
            ->setAmount($transactionData['amount']);

        try {
            $this->transactionMapper->create($transaction);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Récupération du solde total d'un salon
     * 
     * @param int $idSalon
     * @return float
     */
    public function calculateSalonTotalBalance($idSalon)
    {
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        try {
            return $this->transactionMapper->calculateSalonTotalBalance($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupération du solde total d'un freelance
     * 
     * @param type $idFreelance
     * @return float
     */
    public function calculateFreelanceTotalBalance($idFreelance)
    {
        $freelance = new Account();
        $freelance->setIdAccount($idFreelance);
        
        try {
            return $this->transactionMapper->calculateFreelanceTotalBalance($freelance);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function calculateSalonCurrentMonthSales($idSalon)
    {
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        try {
            return $this->transactionMapper->calculateSalonCurrentMonthSales($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function calculateFreelanceCurrentMonthSales($idFreelance)
    {
        $freelance = new Account();
        $freelance->setIdAccount($idFreelance);
        
        try {
            return $this->transactionMapper->calculateFreelanceCurrentMonthSales($freelance);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function calculateSalonGlobalSales($idSalon)
    {
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        try {
            return $this->transactionMapper->calculateSalonGlobalSales($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function calculateFreelanceGlobalSales($idFreelance)
    {
        $freelance = new Account();
        $freelance->setIdAccount($idFreelance);
        
        try {
            return $this->transactionMapper->calculateFreelanceGlobalSales($freelance);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findBySalonId($salonId)
    {
        $salon = new Salon();
        $salon->setIdSalon($salonId);
        
        try {
            return $this->transactionMapper->findBySalonId($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findByFreelanceId($freelanceId)
    {
        $freelance = new Account();
        $freelance->setIdAccount($freelanceId);
        
        try {
            return $this->transactionMapper->findByFreelanceId($freelance);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
