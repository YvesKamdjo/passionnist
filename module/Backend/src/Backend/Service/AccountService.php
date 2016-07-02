<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\BadCredentialsException;
use Application\Exception\EmailIsAlreadyUsedException;
use Application\Exception\MapperException;
use Application\Exception\PhoneHasWrongFormatException;
use Application\Exception\ServiceException;
use Application\Service\AuthorizationService;
use Application\Service\EmailService;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\AccountType;
use Backend\Entity\Salon;
use Backend\Mapper\AccountMapper;
use Backend\Mapper\PermissionMapper;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Zend\File\Transfer\Adapter\Http;
use Zend\Filter\File\Rename;
use Zend\Log\Logger;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class AccountService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $accountMapper AccountMapper */
    private $accountMapper;

    /* @var $permissionMapper PermissionMapper */
    private $permissionMapper;
    
    /* @var $emailService EmailService */
    private $emailService;
    
    private $accountImageStorageDir = 'data/account-image';
    private $qualificationStorageDir = 'data/qualification';

    public function __construct(
        $accountMapper,
        $permissionMapper,
        $weekTemplateMapper,
        $emailService,
        $logger
    ) {
        $this->accountMapper = $accountMapper;
        $this->permissionMapper = $permissionMapper;
        $this->weekTemplateMapper = $weekTemplateMapper;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }

    /**
     * Récupère tous les comptes
     * 
     * @return ArrayCollection
     */
    public function findAll()
    {
        try {
            // Récupération des comptes
            return $this->accountMapper->findAll();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère tous les employés d'un salon
     * 
     * @param int $salonId
     * @return ArrayCollection
     */
    public function findEmployeeBySalonId($salonId)
    {
        $salon = new Salon();
        $salon->setIdSalon($salonId);
        
        try {
            // Récupération des employés
            return $this->accountMapper->findEmployeeBySalonId($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findBestProfessionalByCustomerId($customerId)
    {
        $account = new Account();
        $account->setIdAccount($customerId);
        
        try {
            $storedAccount = $this->accountMapper->findByIdAccount($account);
            
            return $this->accountMapper->findBestProfessionalByCustomerId($storedAccount);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère tous les comptes selon leur type de compte
     * 
     * @param int $accountTypeId
     * @return ArrayCollection
     */
    public function findAllByAccountTypeId($accountTypeId)
    {
        $accountType = new AccountType();
        $accountType->setIdAccountType($accountTypeId);
        
        try {
            // Récupération des comptes
            return $this->accountMapper->findAllByAccountTypeId($accountType);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère tous les professionels aimés par un compte
     * 
     * @param int $accountId
     * @return ArrayCollection
     */
    public function findAllLikedByAccountId($accountId)
    {
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            // Récupération des professionnels
            return $this->accountMapper->findAllLikedByAccountId($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Active un compte
     * 
     * @param int $idAccount
     */
    public function activate($idAccount)
    {
        // Création de l'entité Salon
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            $this->accountMapper->activate($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function passwordLost($email)
    {
        $account = new Account();
        $account->setEmail($email);
        
        if (!$this->checkEmailUsage($account)) {
            throw new ServiceException();
        }
        
        try {
            $creationDate = date("Y-m-d H:i:s");
            $this->accountMapper->addNewPasswordRequest($account, $creationDate);
            
            $this->emailService->setTemplateName('password-lost');
            $this->emailService->addTo($account->getEmail());
            $this->emailService->setSubject('Vous avez demandé un nouveau mot de passe');
            $this->emailService->setTemplateVariables(['hash' => md5($creationDate)]);

            $this->emailService->send();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function newPassword($password, $hash) {
        try {
            $email = $this->accountMapper->checkNewPasswordRequest($hash);

            if (!is_null($email)) {
                $account = new Account();
                $account->setEmail($email)
                    ->setPassword($this->hashPassword($password));

                $this->accountMapper->editPasswordByEmail($account);
                $this->accountMapper->deletePasswordRequestEmail($email);
            }
            else {
                throw new ServiceException();
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
        
    /**
     * Désactive un compte
     * 
     * @param int $idAccount
     */
    public function deactivate($idAccount)
    {
        // Création de l'entité Salon
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            $this->accountMapper->deactivate($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère un utilisateur grace à son ID
     * 
     * @param int $idAccount
     * @return Account
     */
    public function findByIdAccount($idAccount)
    {
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            // Récupère les données
            return $this->accountMapper->findByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère un professionnel grace à son ID
     * 
     * @param int $idAccount
     * @return Account $account
     */
    public function findProfessionalByAccountId($idAccount)
    {
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            // Récupère les données
            return $this->accountMapper->findProfessionalByAccountId($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Identifie un utilisateur
     * 
     * @param array $credentials
     */
    public function logIn(array $credentials)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setEmail($credentials['email']);
        $account->setPassword($credentials['password']);
        
        // Si les identifiants sont incorrects
        if ($this->checkCredentials($account) === false) {
            throw new BadCredentialsException();
        }
        
        try {
            // Récupération des données du compte
            $populatedAccount = $this->accountMapper->findByEmail($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        // Création de la session
        $this->createAccountSession($populatedAccount);
    }
    
    /**
     * Initialise une prise de contrôle
     * 
     * @param int $accountId
     */
    public function takeOver($accountId)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            $storedAccount = $this->accountMapper->findByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        // Mise en mémoire de la session courante
        $this->saveInitialAccountSession();
        
        // Création de la session
        $this->createAccountSession($storedAccount);
    }
    
    public function endTakeOver()
    {        
        // Suppression de la session courante
        $this->deleteAccountSession();
        
        // Réstauration de la session sauvegardée
        $this->restoreAccountSession();
    }
    
    /**
     * Déconnecte un utilisateur
     */
    public function logOut()
    {        
        // Suppression de la session
        $this->deleteAccountSession();
        $this->deleteSavedAccountSession();
    }
    
    /**
     * Inscrit un utilisateur
     * 
     * @param array $signUpData
     * @return Account
     * @throws EmailIsAlreadyUsedException
     */
    public function signUp(array $signUpData)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setEmail($signUpData['email']);
        $account->setFirstName($signUpData['firstName']);
        $account->setLastName($signUpData['lastName']);
        $account->setPhone($signUpData['phone']);
        $account->setPassword($signUpData['password']);
        $account->setAddress($signUpData['address']);
        $account->setZipcode($signUpData['zipcode']);
        $account->setCity($signUpData['city']);
        $account->setLatitude($signUpData['latitude']);
        $account->setLongitude($signUpData['longitude']);
        
        // Création de l'entité AccountType
        $accountType = new AccountType();
        $accountType->setIdAccountType($signUpData['accountType']);
        
        // Si l'adresse email est déjà utilisée
        if ($this->checkEmailUsage($account) === true) {
            throw new EmailIsAlreadyUsedException();
        }
        
        // Si le numéro de téléphone est présent et incorrect
        if ($account->getPhone() != null) {
            if ($this->checkPhoneFormat($account) === false) {
                throw new PhoneHasWrongFormatException();
            }
            
            // Formate le numéro de téléphone
            $this->formatPhone($account);
        }
        
        // Création de l'utilisateur
        $this->createAccount($account);
        
        // Ajoute le role à l'utilisateur
        $this->addRole($account, $accountType);
        
        // Définition des permissions de l'utilisateur
        $this->definePermissions($account, $accountType);
        
        //Si l'utilisateur est un manager, on lui ajoute également le rôle d'employé
        if ($accountType->getIdAccountType() == AccountType::ACCOUNT_TYPE_MANAGER)
        {
            $accountType->setIdAccountType(AccountType::ACCOUNT_TYPE_EMPLOYEE);
            $this->addRole($account, $accountType);
        }
        
        //Si l'utilisateur est un employé, un manager ou un freelance, on lui crée un template de dispo
        if ($accountType->getIdAccountType() == AccountType::ACCOUNT_TYPE_EMPLOYEE
            || $accountType->getIdAccountType() == AccountType::ACCOUNT_TYPE_FREELANCE
            || $accountType->getIdAccountType() == AccountType::ACCOUNT_TYPE_MANAGER
        ) {
            try {
                $this->weekTemplateMapper->create($account);
            } catch (MapperException $exception) {
                $this->logger->alert($exception);
                throw new ServiceException();
            }
        }
        
        // Connecte l'utilisateur
        $this->logIn([
            'email' => $signUpData['email'],
            'password' => $signUpData['password']
        ]);
        
        return $account;
    }
    
    /**
     * Modification du profile
     * 
     * @param array $editedProfileData
     * @throws EmailIsAlreadyUsedException
     * @throws PhoneHasWrongFormatException
     */
    public function editProfile(array $editedProfileData)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setIdAccount($editedProfileData['idAccount']);
        
        try {
            // Récupération de l'Account actuel
            $savedAccount = $this->accountMapper->findByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        $editedAccount = clone $savedAccount;
        
        // Application des modifications à l'entité
        $editedAccount->setFirstName($editedProfileData['firstName']);
        $editedAccount->setLastName($editedProfileData['lastName']);
        $editedAccount->setEmail($editedProfileData['email']);
        $editedAccount->setPhone($editedProfileData['phone']);
        $editedAccount->setAddress($editedProfileData['address']);
        $editedAccount->setZipcode($editedProfileData['zipcode']);
        $editedAccount->setCity($editedProfileData['city']);
        $editedAccount->setLatitude($editedProfileData['latitude']);
        $editedAccount->setLongitude($editedProfileData['longitude']);
        $editedAccount->setMoveRange($editedProfileData['moveRange']);
        $editedAccount->setBiography($editedProfileData['biography']);
        
        // Si l'adresse email est déjà utilisée
        // et est différente de l'adresse email actuelle
        if (
            $this->checkEmailUsage($editedAccount) === true
            && $savedAccount->getEmail() != $editedAccount->getEmail()
        ) {
            throw new EmailIsAlreadyUsedException();
        }
        
        // Si le numéro de téléphone est incorrect
        if ($this->checkPhoneFormat($editedAccount) === false) {
            throw new PhoneHasWrongFormatException();
        }
        
        // Formate le numéro de téléphone
        $this->formatPhone($editedAccount);
                
        try {
            // Modifie de l'utilisateur
            $this->accountMapper->edit($editedAccount);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function editLocation(array $editedData, $accountId)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            // Récupération de l'Account actuel
            $savedAccount = $this->accountMapper->findByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        $editedAccount = clone $savedAccount;
        
        // Application des modifications à l'entité
        $editedAccount->setCity($editedData['city']);
        $editedAccount->setLatitude($editedData['latitude']);
        $editedAccount->setLongitude($editedData['longitude']);
        
        try {
            // Modifie de l'utilisateur
            $this->accountMapper->edit($editedAccount);
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
        // Configuration de l'adapter HTTP
        $transferAdapter = new Http();
        $transferAdapter->setDestination($destination);
        
        // Création du nom du fichier
        $filename = sprintf(
            '%s.%s',
            uniqid(),
            strtolower(pathinfo($imageInfo['name'], PATHINFO_EXTENSION))
        );
        
        // Création du chemin du fichier
        $filepath = $transferAdapter->getDestination()
            . DIRECTORY_SEPARATOR
            . $filename;
        
        // Création de l'action à effectuer sur le fichier
        $transferAdapter->addFilter(
            new Rename([
                'target' => $filepath,
                'overwrite' => true
            ])
        );
        
        // Application du renommage
        if ($transferAdapter->receive($imageInfo['name']) === false) {
            throw new Exception(
                implode('; ', $transferAdapter->getMessages())
            );
        }
        
        return $filename;
    }
    
    /**
     * Télécharger un fichier et le stocke
     * 
     * @param string $imageUrl
     * @param string $destination
     * @return string
     */
    private function saveFileFromUrl($imageUrl, $destination)
    {        
        // Création du nom du fichier
        $filename = sprintf(
            '%s.%s',
            uniqid(),
            'jpg'
        );
        
        // Création du chemin du fichier
        $filepath = $destination
            . DIRECTORY_SEPARATOR
            . $filename;
        
        // Téléchargement de l'image
        $file = file_get_contents($imageUrl);
        
        // Enregistrement de l'image
        file_put_contents($filepath, $file);
        
        return $filename;
    }
    
    /**
     * Modifie la photo de profil d'un utilisateur
     * 
     * @param int $idAccount
     * @param array $imageInfo
     */
    public function editAccountImage($idAccount, array $imageInfo)
    {
        // Enregistrement de l'image sur le serveur
        $filename = $this->storeFile($imageInfo, $this->accountImageStorageDir);
        
        // Création de l'entité à mettre à jour
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            // Récupération des infos de l'entité à mettre à jour
            $storedAccount = $this->accountMapper->findByIdAccount($account);
            // Modification de l'image de profil
            $storedAccount->setAccountImageFilename($filename);
            
            // Mise à jour de l'entité
            $this->accountMapper->edit($storedAccount);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
    }
    
    /**
     * Ajoute une photo de profil Facebook à un utilisateur
     * 
     * @param int $idAccount
     * @param string $imageUrl
     */
    public function addFacebookAccountImage($idAccount, $imageUrl)
    {
        // Enregistrement de l'image sur le serveur
        $filename = $this->saveFileFromUrl($imageUrl, $this->accountImageStorageDir);
        
        // Création de l'entité à mettre à jour
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            // Récupération des infos de l'entité à mettre à jour
            $storedAccount = $this->accountMapper->findByIdAccount($account);
            // Modification de l'image de profil
            $storedAccount->setAccountImageFilename($filename);
            
            // Mise à jour de l'entité
            $this->accountMapper->edit($storedAccount);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
    }
    
    public function addQualification($idAccount, array $imageInfo)
    {
        $filename = $this->storeFile($imageInfo, $this->qualificationStorageDir);
        
        // Création de l'entité à mettre à jour
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            // Récupération des infos de l'entité à mettre à jour
            $storedAccount = $this->accountMapper->findByIdAccount($account);
            // Modification du diplôme
            $storedAccount->setQualificationFilename($filename);
            
            // Mise à jour de l'entité
            $this->accountMapper->edit($storedAccount);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
    }
    
    /**
     * Vérifie les identifiants
     * 
     * @param Account $account
     * @return boolean
     */
    public function checkCredentials(Account $account)
    {
        try {
            // Vérifie l'éxistance de l'account
            $storedAccount = $this->accountMapper->findByEmail($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        if (is_null($storedAccount)) {
            return false;
        }
        
        // Vérifie le mot de passe
        return password_verify($account->getPassword(), $storedAccount->getPassword());
    }
    
    /**
     * Vérifie l'utilisation d'une adresse email
     * 
     * @param Account $account
     * @return boolean
     */
    public function checkEmailUsage(Account $account)
    {
        try {
            // Vérification l'existance de l'adresse email
            $storedAccount = $this->accountMapper->findByEmail($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        if (is_null($storedAccount)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifie le format du numéro de téléphone envoyé
     * 
     * @param Account $account
     * @return boolean
     */
    public function checkPhoneFormat(Account $account)
    {
        // Crée l'instance du numéro
        $phoneUtil = PhoneNumberUtil::getInstance();
        
        try {
            $phoneNumber = $phoneUtil->parse($account->getPhone(), "FR");
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
     * @param Account $account
     * @return type
     */
    public function formatPhone(Account $account)
    {
        // Crée l'instance du numéro
        $phoneUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneUtil->parse($account->getPhone(), "FR");
        
        // Formate le numéro de téléphone
        $account->setPhone(
            $phoneUtil->format(
                $phoneNumber, 
                PhoneNumberFormat::NATIONAL
            )
        );
    }
    
    /**
     * Création de la session de connexion
     * 
     * @param Account $account
     */
    public function createAccountSession(Account $account)
    {
        // Création du conteneur de session
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('hairlov', $sessionManager);
        
        // Ajout des données du compte dans la session
        $sessionContainer->account = $account;
    }
    
    /**
     * Enregistre la session actuelle afin de la restaurer plus tard
     */
    public function saveInitialAccountSession()
    {
        // Création du conteneur de session
        $sessionManager = new SessionManager();
        $sessionContainer = new Container('hairlov', $sessionManager);
        
        // Récupération des données du compte
        $accountSession = $sessionContainer->account;
        // Ajout des données du compte dans la session
        $sessionContainer->savedAccount = $accountSession;
    }
    
    /**
     * Crée un compte utilisateur
     * 
     * @param Account $account
     */
    public function createAccount(Account $account)
    {
        // Application du hash sur le mot de passe
        $account->setPassword($this->hashPassword($account->getPassword()));
        
        try {
            // Création du compte
            $this->accountMapper->create($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Ajoute un rôle
     * 
     * @param Account $account
     * @param AccountType $accountType
     */
    public function addRole(Account $account, AccountType $accountType)
    {
        try {
            // Ajout du rôle
            $this->accountMapper->addAccountType($account, $accountType);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Définit les permissions de l'utilisateur
     * 
     * @param Account $account
     * @param AccountType $accountType
     */
    public function definePermissions(Account $account, AccountType $accountType)
    {
        $permissions = [];
        // Récupération des permissions
        switch ($accountType->getIdAccountType()) {
            case AccountType::ACCOUNT_TYPE_EMPLOYEE :
                $permissions = AuthorizationService::getEmployeePermissions();
                break;
            case AccountType::ACCOUNT_TYPE_MANAGER :
                $managerPermissions = AuthorizationService::getManagerPermissions();
                $employeePermissions = AuthorizationService::getEmployeePermissions();
                
                $permissions = array_merge($managerPermissions, $employeePermissions);
                
                // Suppression des permissions inadaptées
                unset($permissions[array_search('create-attachment-request', $permissions)]);
                
                break;
            case AccountType::ACCOUNT_TYPE_FREELANCE :
                $permissions = AuthorizationService::getFreelancePermissions();
                break;
            case AccountType::ACCOUNT_TYPE_ADMIN :
                $permissions = AuthorizationService::getAdminPermissions();
                break;
            case AccountType::ACCOUNT_TYPE_CUSTOMER :
                $permissions = AuthorizationService::getCustomerPermissions();
                break;
        }
        
        try {
            // Affectation des permissions
            foreach ($permissions as $idPermission) {
                $this->permissionMapper->addAccountPermission($account, $idPermission);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retire une permission à un utilisateur
     * 
     * @param int $idAccount
     * @param string $permissionKey
     */
    public function removePermission($idAccount, $permissionKey)
    {
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            // Suppression de la permission
            $this->accountMapper->removePermission($idAccount, $permissionKey);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Suppression de la session de connexion
     */
    public function deleteAccountSession()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Suppression la session utilisateur
        unset($sessionContainer->account);
    }

    /**
     * Suppression de la session de connexion sauvegardée
     */
    public function deleteSavedAccountSession()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Suppression la session utilisateur sauvegardée
        unset($sessionContainer->savedAccount);
    }
    
    public function restoreAccountSession()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Récupération des données du compte
        $accountSession = $sessionContainer->savedAccount;
        // Ajout des données du compte dans la session
        $sessionContainer->account = $accountSession;
        
        // Suppression la session sauvegardée utilisateur
        unset($sessionContainer->savedAccount);
    }

    public function findAttachmentRequestByManagerInformations(array $managerData)
    {
        $manager = new Account();
        $manager->setIdAccount($managerData['managerId']);
        $manager->setEmail($managerData['managerEmail']);
        
        $salon = new Salon();
        $salon->setIdSalon($managerData['salonId']);
        
        try {
            return $this->accountMapper
                ->findAttachmentRequestByManagerInformations($manager, $salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère les pro likés par un customer
     * 
     * @param int $customerId
     * @return ArrayCollection
     * @throws ServiceException
     */
    public function findProfessionalLikedByCustomerId($customerId)
    {
        $customer = new Account();
        $customer->setIdAccount($customerId);
        
        try {
            return $this->accountMapper
                ->findProfessionalLikedByCustomerId($customer);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findCustomerWhoLikeByProfessionalId($professionalId)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        try {
            return $this->accountMapper
                ->findCustomerWhoLikeByProfessionalId($professional);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function addLikeOnProfessional($professionalId, $customerId)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        $customer = new Account();
        $customer->setIdAccount($customerId);
        
        try {
            return $this->accountMapper
                ->addLikeOnProfessional($professional, $customer);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function removeLikeOnProfessional($professionalId, $customerId)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        $customer = new Account();
        $customer->setIdAccount($customerId);
        
        try {
            return $this->accountMapper
                ->removeLikeOnProfessional($professional, $customer);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function logInFromFacebook($email, $accessToken)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setEmail($email);
        $account->setPassword($accessToken);
        
        try {
            // Récupération des données du compte
            $storedAccount = $this->accountMapper->findByEmail($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        if (is_null($storedAccount)) {
            $this->createAccount($account);
            
            $accountType = new AccountType();
            $accountType->setIdAccountType(AccountType::ACCOUNT_TYPE_CUSTOMER);
            
            // Ajoute le role à l'utilisateur
            $this->addRole($account, $accountType);

            // Définition des permissions de l'utilisateur
            $this->definePermissions($account, $accountType);
            
            // Création de la session
            $this->createAccountSession($account);
        }
        else {
            // Création de la session
            $this->createAccountSession($storedAccount);
        }
        
    }
    
    public function isProfessionalLikedByCustomer($customerId, $professionalId)
    {
        $customer = new Account();
        $customer->setIdAccount($customerId);
        
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        return $this->accountMapper->isProfessionalLikedByCustomer($customer, $professional);
    }
    
    /**
     * Applique un hash sur un mot de passe
     * 
     * @param string $password
     * @return string
     */
    private function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    public function getQualificationStorageDir() {
        return $this->qualificationStorageDir;
    }
    
}
