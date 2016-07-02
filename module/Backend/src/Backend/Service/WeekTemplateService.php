<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Mapper\WeekTemplateMapper;
use Zend\Log\Logger;

class WeekTemplateService
{
    /* @var $logger Logger */

    private $logger;

    /* @var $weekTemplateMapper WeekTemplateMapper */
    private $weekTemplateMapper;

    public function __construct($weekTemplateMapper, $logger)
    {
        $this->weekTemplateMapper = $weekTemplateMapper;
        $this->logger = $logger;
    }

    public function findByAccountId($accountId)
    {
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            return $this->weekTemplateMapper->findByAccountId($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
