<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Mapper\FacebookAccountMapper;
use Zend\Log\Logger;

class FacebookAccountService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $facebookAccountMapper FacebookAccountMapper */
    private $facebookAccountMapper;

    public function __construct($facebookAccountMapper, $logger)
    {
        $this->facebookAccountMapper = $facebookAccountMapper;
        $this->logger = $logger;
    }

    /**
     * Retourne l'adresse email d'un utilisateur Facebook
     * 
     * @return string
     */
    public function findEmail($accessToken)
    {
        try {
            $email = $this->facebookAccountMapper->findEmail($accessToken);
            
            if (is_null($email)) {
                throw new ServiceException();
            }
            
            return $email;
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Retourne l'image de profil d'un utilisateur Facebook
     * 
     * @return string
     */
    public function findAccountImage($accessToken)
    {
        try {
            $accountImage = $this->facebookAccountMapper->findAccountImage($accessToken);
            
            if (is_null($accountImage)) {
                throw new ServiceException();
            }
            
            return $accountImage;
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
