<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Mapper\ReferralMapper;
use Zend\Log\Logger;

class ReferralService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $referralMapper ReferralMapper */
    private $referralMapper;

    public function __construct($referralMapper, $logger)
    {
        $this->referralMapper = $referralMapper;
        $this->logger = $logger;
    }

    /**
     * Retourne tous les referral
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        try {
            return $this->referralMapper->findAll();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
