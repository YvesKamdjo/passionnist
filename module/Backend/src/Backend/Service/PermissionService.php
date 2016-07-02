<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Mapper\PermissionMapper;
use Zend\Log\Logger;

class PermissionService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $permissionMapper PermissionMapper */
    private $permissionMapper;

    public function __construct($permissionMapper, $logger)
    {
        $this->permissionMapper = $permissionMapper;
        $this->logger = $logger;
    }    
    
    public function findByIdAccount($accountId) {
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            return $this->permissionMapper->findByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
