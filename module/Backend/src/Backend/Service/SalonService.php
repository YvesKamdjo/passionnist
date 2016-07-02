<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Application\Service\EmailService;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\AttachmentRequest;
use Backend\Entity\Salon;
use Backend\Mapper\AccountMapper;
use Backend\Mapper\AttachmentRequestMapper;
use Backend\Mapper\PermissionMapper;
use Backend\Mapper\SalonMapper;
use Exception;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\Rename;
use Zend\Log\Logger;

class SalonService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $salonMapper SalonMapper */
    private $salonMapper;

    /* @var $accountMapper AccountMapper */
    private $accountMapper;
 
    /* @var $permissionMapper PermissionMapper */
    private $permissionMapper;

    /* @var $attachmentRequestMapper AttachmentRequestMapper */
    private $attachmentRequestMapper;

    /* @var $salonConfiguratorService SalonConfiguratorService */
    private $salonConfiguratorService;

    /* @var $emailService EmailService */
    private $emailService;
    
    private $certificateStorageDir = 'data/certificate';

    public function __construct(
        $salonMapper, 
        $accountMapper,
        $permissionMapper,
        $attachmentRequestMapper,
        $salonConfiguratorService,
        $emailService, 
        $logger
    ) {
        $this->salonMapper = $salonMapper;
        $this->accountMapper = $accountMapper;
        $this->permissionMapper = $permissionMapper;
        $this->attachmentRequestMapper = $attachmentRequestMapper;
        $this->salonConfiguratorService = $salonConfiguratorService;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }

    /**
     * Active un salon
     * 
     * @param type $idSalon
     */
    public function activate($idSalon)
    {
        // Création de l'entité Salon
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        try {
            $this->salonMapper->activate($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Désactive un salon
     * 
     * @param type $idSalon
     */
    public function deactivate($idSalon)
    {
        // Création de l'entité Salon
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        try {
            $this->salonMapper->deactivate($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function createSalon(array $data)
    {
        // Création de l'entité du salon
        $salon = new Salon();
        $salon->setName($data['name']);
        $salon->setAddress($data['address']);
        $salon->setZipcode($data['zipcode']);
        $salon->setCity($data['city']);
        $salon->setLatitude($data['latitude']);
        $salon->setLongitude($data['longitude']);

        // Création de l'entité du manager
        $manager = new Account();
        $manager->setIdAccount($data['idAccount']);

        try {
            // Création du salon
            $this->salonMapper->createSalon($salon);

            // Ajout du gérant au salon
            $this->salonMapper->addManager($salon, $manager);
            // Ajout du gérant aux employés du salon
            $this->salonMapper->addEmployee($salon, $manager);

            // Création des templates de prestation par defaut
            $this->salonConfiguratorService->configure($salon, $manager);
            
            // Supprime la permission de créer un salon au manager
            $this->accountMapper->removePermission($manager, 'create-salon');
            
            // Ajoute les droits sur le salon
            $this->permissionMapper->addAccountPermission($manager, 'edit-salon');
            $this->permissionMapper->addAccountPermission($manager, 'access-job-service-template-list');
            $this->permissionMapper->addAccountPermission($manager, 'create-job-service-template');
            $this->permissionMapper->addAccountPermission($manager, 'edit-job-service-template');
            $this->permissionMapper->addAccountPermission($manager, 'create-transfer-request');
            $this->permissionMapper->addAccountPermission($manager, 'list-transfer-request');
            $this->permissionMapper->addAccountPermission($manager, 'list-transaction');
            $this->permissionMapper->addAccountPermission($manager, 'edit-discount');
            // Ajoute les permissions d'un employé
            $this->permissionMapper->addAccountPermission($manager, 'access-job-service-list');
            $this->permissionMapper->addAccountPermission($manager, 'create-job-service');
            $this->permissionMapper->addAccountPermission($manager, 'edit-job-service');
            
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère tous les salons
     * 
     * @param string $filter
     * @return ArrayCollection
     */
    public function findAll($filter)
    {
        try {
            // Récupération des salons selon le filtre
            if ($filter === 'active') {
                return $this->salonMapper->findAllActive();
            }
            elseif ($filter === 'inactive') {
                return $this->salonMapper->findAllInactive();
            }
            else {
                return $this->salonMapper->findAll();
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Retrouve un salon avec son ID
     * 
     * @param int $idSalon
     * @return Salon
     */
    public function findById($idSalon)
    {
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        try {
            return $this->salonMapper->findById($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Récupère la demande pour rejoindre un salon
     * 
     * @param array $data
     */
    public function joinSalon(array $data)
    {
        // Création du manager
        $manager = new Account();
        $manager->setEmail($data['managerEmail']);

        try {
            // Récupère le compte par son adresse email
            $storedManager = $this->accountMapper->findByEmail($manager);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }

        // Création du demander
        $account = new Account();
        $account->setIdAccount($data['idAccount']);

        try {
            // Récupère le compte par son adresse email
            $storedAccount = $this->accountMapper->findByIdAccount($account);
            
            // Vérifie si l'adresse email existe
            if ($storedManager !== null) {
                // Récupère le salon par l'idAccount du gérant
                $salon = $this->salonMapper->findByManagerIdAccount($storedManager);

                // Vérifie que le salon existe
                if ($salon !== null) {
                    $this->createSalonJoiningInvite(
                        $storedManager, 
                        $storedAccount,
                        $salon
                    );
                } else {
                    $this->createSalonCreationInvite($storedManager, $storedAccount);
                }
            } else {
                $this->createAccountCreationInvite($manager, $storedAccount);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }

    }

    /**
     * Envoie un email pour permettre à un professionnel de rejoindre un salon
     * 
     * @param Account $manager
     * @param Account $professionnal
     * @param Salon $salon
     */
    private function createSalonJoiningInvite(
        Account $manager,
        Account $professionnal,
        Salon $salon
    ) {
        $attachmentRequest = new AttachmentRequest();
        $attachmentRequest->setIdEmployee($professionnal->getIdAccount());
        $attachmentRequest->setIdSalon($salon->getIdSalon());
        
        try {
            $this->attachmentRequestMapper->create($attachmentRequest);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        $this->emailService->setTemplateName('joining-invite');
        $this->emailService->addTo($manager->getEmail(), $manager->getFirstName() . ' ' . $manager->getLastName());
        $this->emailService->setSubject('Un employé souhaiterait rejoindre votre salon');

        $this->emailService->send();
    }

    /**
     * Envoie un email pour inviter à créer un salon
     * 
     * @param Account $manager
     * @param Account $professionnal
     */
    private function createSalonCreationInvite(
        Account $manager,
        Account $professionnal
    ) {
        $attachmentRequest = new AttachmentRequest();
        $attachmentRequest->setIdEmployee($professionnal->getIdAccount());
        $attachmentRequest->setIdManager($manager->getIdAccount());
        
        try {
            $this->attachmentRequestMapper->create($attachmentRequest);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        $this->emailService->setTemplateName('salon-creation-invite');
        $this->emailService->setTemplateVariables([
            'ceciEstUneVariable' => 'ceci est une valeur'
        ]);
        $this->emailService->addTo($manager->getEmail(), $manager->getFirstName() . ' ' . $manager->getLastName());
        $this->emailService->setSubject('Un utilisateur vous invite à créer votre salon');

        $this->emailService->send();
    }

    /**
     * Envoie un email pour inviter le manager à créer un compte
     * 
     * @param Account $manager
     * @param Account $professionnal
     */
    private function createAccountCreationInvite(
        Account $manager,
        Account $professionnal
    ) {
        $attachmentRequest = new AttachmentRequest();
        $attachmentRequest->setIdEmployee($professionnal->getIdAccount());
        $attachmentRequest->setManagerEmail($manager->getEmail());
        
        try {
            $this->attachmentRequestMapper->create($attachmentRequest);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        $this->emailService->setTemplateName('account-creation-invite');
        $this->emailService->setTemplateVariables([
            'manager' => $manager,
            'professionnal' => $professionnal,
        ]);
        $this->emailService->addTo($manager->getEmail());
        $this->emailService->setSubject($professionnal->getFirstName().' '.$professionnal->getLastName().' vous invite à le rejoindre sur HAIRLOV.com');

        $this->emailService->send();
    }

    /**
     * Permet de retourner un salon selon son gérant
     * 
     * @param int $idManager
     * @return Salon
     */
    public function findByManagerIdAccount($idManager)
    {
        $manager = new Account();
        $manager->setIdAccount($idManager);
        
        try {
            return $this->salonMapper->findByManagerIdAccount($manager);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Permet de retourner un salon selon l'id de l'employé
     * 
     * @param int $idEmployee
     * @return Salon
     */
    public function findByEmployeeIdAccount($idEmployee)
    {
        
        $employee = new Account();
        $employee->setIdAccount($idEmployee);
        
        return $this->salonMapper->findByEmployeeIdAccount($employee);
    }

    /**
     * Permet d'éditer un salon
     * 
     * @param array $editedSalonData
     */
    public function editSalon(array $editedSalonData)
    {
        // Création de l'entité Account
        $salon = new Salon();
        $salon->setIdSalon($editedSalonData['idSalon']);
        $salon->setName($editedSalonData['name']);
        $salon->setAddress($editedSalonData['address']);
        $salon->setZipcode($editedSalonData['zipcode']);
        $salon->setCity($editedSalonData['city']);
        $salon->setLatitude($editedSalonData['latitude']);
        $salon->setLongitude($editedSalonData['longitude']);

        try {
            // Création de l'utilisateur
            $this->salonMapper->editSalon($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Permet l'enregistrement du k-bis
     * 
     * @param array $certificateData
     */
    public function saveCertificate(array $certificateData)
    {
        $filename = $this->storeFile($certificateData['certificate'], $this->certificateStorageDir);
        
        $salon = new Salon();
        $salon->setIdSalon($certificateData['idSalon']);
        $salon->setCertificateFilename($filename);
        
        try {
            $this->salonMapper->saveCertificate($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Stocke un fichier uploadé
     * 
     * ["name"]     => string(11) "myimage.png"
     * ["type"]     => string(9)  "image/png"
     * ["tmp_name"] => string(22) "/private/tmp/phpgRXd58"
     * ["error"]    => int(0)
     * ["size"]     => int(14908679)
     * 
     * @param array $imageInfo
     * @return string
     */
    private function storeFile(array $imageInfo, $destination)
    {
        $transferAdapter = new Http();
        $transferAdapter->setDestination($destination);
        
        $filename = sprintf(
            '%s.%s',
            uniqid(),
            strtolower(pathinfo($imageInfo['name'], PATHINFO_EXTENSION))
        );
        
        $filepath = $transferAdapter->getDestination()
            . DIRECTORY_SEPARATOR
            . $filename;
        
        $transferAdapter->addFilter(
            new Rename([
                'target' => $filepath,
                'overwrite' => true
            ])
        );
        
        if ($transferAdapter->receive($imageInfo['name']) === false) {
            throw new Exception(
                implode('; ', $transferAdapter->getMessages())
            );
        }
        
        return $filename;
    }
    
    public function getCertificateStorageDirectory()
    {
        return $this->certificateStorageDir;
    }
}
