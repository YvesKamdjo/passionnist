<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\PhoneHasWrongFormatException;
use Application\Exception\ServiceException;
use Application\Service\EmailService;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Prospect;
use Backend\Mapper\ProspectMapper;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Zend\Log\Logger;

class ProspectService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $prospectMapper ProspectMapper */
    private $prospectMapper;
    
    /* @var $emailService EmailService */
    private $emailService;

    public function __construct($prospectMapper, $emailService, $logger)
    {
        $this->prospectMapper = $prospectMapper;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }

    /**
     * Retourne tous les prospects
     * 
     * @return ArrayCollection
     */
    public function listAll()
    {
        try {
            return $this->prospectMapper->findAll();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    
    public function createProspect(array $prospectData) {
        $prospect = new Prospect();
        $prospect->setEmail($prospectData['email'])
                 ->setFirstName($prospectData['firstName'])
                 ->setLastName($prospectData['lastName'])
                 ->setPhone($prospectData['phone']);
        
        // Création du prospect
        $this->create($prospect);
    }
    
    /**
     * Vérifie le format du numéro de téléphone envoyé
     * 
     * @param Prospect $prospect
     * @return boolean
     */
    public function checkPhoneFormat(Prospect $prospect)
    {
        // Crée l'instance du numéro
        $phoneUtil = PhoneNumberUtil::getInstance();
        
        try {
            $phoneNumber = $phoneUtil->parse($prospect->getPhone(), "FR");
        }
        catch (NumberParseException $exception) {
            return false;
        }
        
        // Si le numéro de téléphone est valide
        return $phoneUtil->isValidNumber($phoneNumber);
    }
    
    /**
     * Formate un numéro de téléphone
     * 
     * @param Prospect $prospect
     * @return type
     */
    public function formatPhone(Prospect $prospect)
    {
        // Crée l'instance du numéro
        $phoneUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneUtil->parse($prospect->getPhone(), "FR");
        
        // Formate le numéro de téléphone
        $prospect->setPhone(
            $phoneUtil->format(
                $phoneNumber, 
                PhoneNumberFormat::NATIONAL
            )
        );
    }
    
    private function create(Prospect $prospect)
    {
        // Si le numéro de téléphone est incorrect
        if ((strlen($prospect->getPhone()) > 0)
            && $this->checkPhoneFormat($prospect) === false
        ) {
            throw new PhoneHasWrongFormatException();
        }
        elseif ((strlen($prospect->getPhone()) > 0)
            && $this->checkPhoneFormat($prospect) === true
        ) {
            // Formate le numéro de téléphone
            $this->formatPhone($prospect);
        }
        
        try {
            // Création du prospect
            $this->prospectMapper->create($prospect);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
