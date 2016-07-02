<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Mapper\AccountTypeMapper;
use Zend\Log\Logger;

class AccountTypeService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $accountTypeMapper AccountTypeMapper */
    private $accountTypeMapper;

    public function __construct($accountTypeMapper, $logger)
    {
        $this->accountTypeMapper = $accountTypeMapper;
        $this->logger = $logger;
    }

    /**
     * Retourne tous les types de comptes
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        
        try {
            return $this->accountTypeMapper->findAll();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne tous les types de comptes d'un compte donné
     * 
     * @param int $idAccount
     * @return ArrayCollection
     */
    public function findAllByIdAccount($idAccount)
    {
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            return $this->accountTypeMapper->findAllByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
