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
use Backend\Entity\AccountType;
use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobService;
use Backend\Entity\JobServiceTemplate;
use Backend\Entity\JobServiceType;
use Backend\Entity\Salon;
use Backend\Mapper\AccountMapper;
use Backend\Mapper\CustomerCharacteristicMapper;
use Backend\Mapper\JobServiceMapper;
use Backend\Mapper\JobServiceTemplateMapper;
use Backend\Mapper\JobServiceTypeMapper;
use Backend\Mapper\SalonMapper;
use Backend\Mapper\SearchMapper;
use Professionnal\Exception\SalonJobServiceDoesntExistsException;
use Professionnal\Exception\SalonJobServiceTemplateDoesntExistsException;
use Zend\Log\Logger;

class JobServiceService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $jobServiceMapper JobServiceMapper */
    private $jobServiceMapper;
    
    /* @var $jobServiceTemplateMapper JobServiceTemplateMapper */
    private $jobServiceTemplateMapper;
    
    /* @var $jobServiceTypeMapper JobServiceTypeMapper */
    private $jobServiceTypeMapper;
    
    /* @var $customerCharacteristicMapper CustomerCharacteristicMapper */
    private $customerCharacteristicMapper;

    /* @var $salonMapper SalonMapper */
    private $salonMapper;
    
    /* @var $searchMapper SearchMapper */
    private $searchMapper;
    
    /* @var $accountMapper AccountMapper */
    private $accountMapper;

    public function __construct(
        $jobServiceMapper,
        $jobServiceTemplateMapper,
        $jobServiceTypeMapper,
        $customerCharacteristicMapper,
        $salonMapper,
        $searchMapper,
        $accountMapper,
        $logger
    ) {
        $this->jobServiceMapper = $jobServiceMapper;
        $this->jobServiceTemplateMapper = $jobServiceTemplateMapper;
        $this->jobServiceTypeMapper = $jobServiceTypeMapper;
        $this->customerCharacteristicMapper = $customerCharacteristicMapper;
        $this->salonMapper = $salonMapper;
        $this->searchMapper = $searchMapper;
        $this->accountMapper = $accountMapper;
        $this->logger = $logger;
    }

    /**
     * Retourne toutes les prestations d'un professionnel
     * 
     * @param int $idAccount
     * @return ArrayCollection
     */
    public function listAllByIdAccount($idAccount)
    {
        $account = new Account();
        $account->setIdAccount($idAccount);
        
        try {
            return $this->jobServiceMapper->findAllByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * @param int $idJobService
     * @return JobService
     * @throws ServiceException
     */
    public function findById($idJobService)
    {
        $jobService = new JobService();
        $jobService->setIdJobService($idJobService);
        
        try {
            return $this->jobServiceMapper->findById($jobService);
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
            return $this->jobServiceMapper->findBySalonId($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function saveSearchLocation($searchData, $accountId)
    {
        $account = new Account();
        $account->setIdAccount($accountId);
        $account->setAddress($searchData['location']['address']);
        $account->setLatitude($searchData['location']['latitude']);
        $account->setLongitude($searchData['location']['longitude']);
        
        try {
            return $this->accountMapper->saveLocation($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function searchJobService(array $searchData)
    {
        try {
            if (isset($searchData['idAccountType'])
                && is_array($searchData['idAccountType'])
            ) {
                
                if (array_search(AccountType::ACCOUNT_TYPE_EMPLOYEE, $searchData['idAccountType']) !== false
                    && array_search(AccountType::ACCOUNT_TYPE_FREELANCE, $searchData['idAccountType']) === false
                ) {
                    $jobServiceCollection = $this->searchMapper->searchSalonJobService($searchData);
                }
                elseif (array_search(AccountType::ACCOUNT_TYPE_FREELANCE, $searchData['idAccountType']) !== false
                    && array_search(AccountType::ACCOUNT_TYPE_EMPLOYEE, $searchData['idAccountType']) === false
                ) {
                    $jobServiceCollection = $this->searchMapper->searchFreelanceJobService($searchData);
                }
                else {
                    $freelanceJobService = $this->searchMapper->searchFreelanceJobService($searchData);
                    $salonJobService = $this->searchMapper->searchSalonJobService($searchData);

                    $jobServiceCollection = ArrayCollection::merge($freelanceJobService, $salonJobService);
                }
                
            }
            else {
                $freelanceJobService = $this->searchMapper->searchFreelanceJobService($searchData);
                $salonJobService = $this->searchMapper->searchSalonJobService($searchData);
                
                $jobServiceCollection = ArrayCollection::merge($freelanceJobService, $salonJobService);
            }
            
            $jobServiceArray = iterator_to_array($jobServiceCollection);
            
            usort($jobServiceArray, function($a, $b) {
                if ($a->likeCount >= $b->likeCount) {
                    return -1;
                }
                else {
                    return 1;
                }
            });
            
            $jobServiceCollection = ArrayCollection::createFromArray($jobServiceArray);
            
            return $jobServiceCollection;
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function searchProfessional(array $searchData)
    {
        try {
            if (isset($searchData['idAccountType'])
                && is_array($searchData['idAccountType'])
            ) {
                
                if (array_search(AccountType::ACCOUNT_TYPE_EMPLOYEE, $searchData['idAccountType']) !== false
                    && array_search(AccountType::ACCOUNT_TYPE_FREELANCE, $searchData['idAccountType']) === false
                ) {
                    $professionalCollection = $this->searchMapper->searchSalonProfessional($searchData);
                }
                elseif (array_search(AccountType::ACCOUNT_TYPE_FREELANCE, $searchData['idAccountType']) !== false
                    && array_search(AccountType::ACCOUNT_TYPE_EMPLOYEE, $searchData['idAccountType']) === false
                ) {
                    $professionalCollection = $this->searchMapper->searchFreelanceProfessional($searchData);
                }
                else {
                    $freelanceProfessional = $this->searchMapper->searchFreelanceProfessional($searchData);
                    $salonProfessional = $this->searchMapper->searchSalonProfessional($searchData);

                    $professionalCollection = ArrayCollection::merge($freelanceProfessional, $salonProfessional);
                }
                
            }
            else {
                $freelanceProfessional = $this->searchMapper->searchFreelanceProfessional($searchData);
                $salonProfessional = $this->searchMapper->searchSalonProfessional($searchData);
                
                $professionalCollection = ArrayCollection::merge($freelanceProfessional, $salonProfessional);
            }
            
            $professionalArray = iterator_to_array($professionalCollection);
            
            usort($professionalArray, function($a, $b) use ($searchData) {
                if ( isset($searchData['sort']) && $searchData['sort'] == 'like' ) {
                    $aData = $a->accountLike;
                    $bData = $b->accountLike;
                }
                elseif ( isset($searchData['sort']) && $searchData['sort'] == 'rate') {
                    $aData = $a->accountRate;
                    $bData = $b->accountRate;
                }
                elseif ( isset($searchData['sort']) && $searchData['sort'] == 'discount') {
                    $aData = $a->accountMaxDiscount;
                    $bData = $b->accountMaxDiscount;
                }
                elseif ( isset($searchData['sort']) && $searchData['sort'] == 'latest') {
                    $aData = $a->accountCreationDate;
                    $bData = $b->accountCreationDate;
                }
                else {
                    $aData = $a->accountLike;
                    $bData = $b->accountLike;
                }
                
                if ( isset($searchData['order']) && $searchData['order'] == 'asc') {
                    if ($aData >= $bData) {
                        return 1;
                    }
                    else {
                        return -1;
                    }
                }
                else {
                    if ($aData >= $bData) {
                        return -1;
                    }
                    else {
                        return 1;
                    }
                } 
            });
            
            $professionalCollection = ArrayCollection::createFromArray($professionalArray);
            
            return $professionalCollection;
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Crée une prestation
     * 
     * @param array $jobServiceData
     * @throws SalonJobServiceDoesntExistsException
     */
    public function createAsEmployee(array $jobServiceData)
    {
        // Création de l'entité de la prestation
        $jobService = new JobService();
        $jobService->setIdJobServiceTemplate($jobServiceData['idJobServiceTemplate']);
        $jobService->setIdProfessional($jobServiceData['idProfessional']);
        $jobService->setName($jobServiceData['name']);
        $jobService->setDuration($jobServiceData['duration']);
        $jobService->setDescription($jobServiceData['description']);
        
        // Création de l'entité du professionnel
        $professional = new Account();
        $professional->setIdAccount($jobServiceData['idProfessional']);
        
        try {
            $salon = $this->salonMapper->findByEmployeeIdAccount($professional);

            // Récupération des templates de prestation du salon
            $jobServiceTemplateCollection = $this->jobServiceTemplateMapper->findAllActiveByIdSalon($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        // Vérification de l'existence du template lié à la prestation
        $exists = false;
        /* @var $jobServiceTemplate JobServiceTemplate */
        foreach ($jobServiceTemplateCollection as $jobServiceTemplate) {
            if ($jobServiceTemplate->getIdJobServiceTemplate() === $jobService->getIdJobServiceTemplate()) {
                $exists = $jobServiceTemplate;
                break;
            }
        }
        
        // Si le template de prestation n'existe pas, on lève une exception
        if($exists === false) {
            throw new SalonJobServiceTemplateDoesntExistsException();
        }
        
        // Duplication du prix du template
        $jobService->setPrice($jobServiceTemplate->getPrice());
        
        try {
            // Création de la prestation
            $this->jobServiceMapper->create($jobService);

            // Duplique les caractéristiques
            $this->duplicateCustomerCharacteristicFromJobServiceTemplate($jobService, $jobServiceTemplate);

            // Duplique les types de prestation
            $this->duplicateJobServiceTypeFromJobServiceTemplate($jobService, $jobServiceTemplate);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Modifie une prestation
     * 
     * @param array $jobServiceData
     * @throws SalonJobServiceTemplateDoesntExistsException
     */
    public function editAsEmployee(array $jobServiceData)
    {
        // Création de l'entité de la prestation
        $jobService = new JobService();
        $jobService->setIdJobService($jobServiceData['idJobService']);
        $jobService->setIdJobServiceTemplate($jobServiceData['idJobServiceTemplate']);
        $jobService->setName($jobServiceData['name']);
        $jobService->setDuration($jobServiceData['duration']);
        $jobService->setDescription($jobServiceData['description']);
        
        // Création de l'entité du professionnel
        $professional = new Account();
        $professional->setIdAccount($jobServiceData['idProfessional']);
        $salon = $this->salonMapper->findByEmployeeIdAccount($professional);
        
        // Récupération des templates de prestation du salon
        $jobServiceTemplateCollection = $this->jobServiceTemplateMapper->findAllActiveByIdSalon($salon);
        
        // Vérification de l'existence du template lié à la prestation
        $exists = false;
        /* @var $jobServiceTemplate JobServiceTemplate */
        foreach ($jobServiceTemplateCollection as $jobServiceTemplate) {
            if ($jobServiceTemplate->getIdJobServiceTemplate() === $jobService->getIdJobServiceTemplate()) {
                $exists = $jobServiceTemplate;
                break;
            }
        }
        
        // Si le template de prestation n'existe pas, on lève une exception
        if($exists === false) {
            throw new SalonJobServiceTemplateDoesntExistsException();
        }
        
        // Duplication du prix du template
        $jobService->setPrice($jobServiceTemplate->getPrice());
        
        try {
            // Création de la prestation
            $this->jobServiceMapper->edit($jobService);

            // Duplique les caractéristiques
            $this->duplicateCustomerCharacteristicFromJobServiceTemplate($jobService, $jobServiceTemplate);

            // Duplique les types de prestation
            $this->duplicateJobServiceTypeFromJobServiceTemplate($jobService, $jobServiceTemplate);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Crée une prestation
     * 
     * @param array $jobServiceData
     * @throws SalonJobServiceDoesntExistsException
     */
    public function createAsFreelance(array $jobServiceData)
    {
        // Création de l'entité de la prestation
        $jobService = new JobService();
        $jobService->setIdProfessional($jobServiceData['idProfessional']);
        $jobService->setName($jobServiceData['name']);
        $jobService->setDuration($jobServiceData['duration']);
        $jobService->setPrice($jobServiceData['price']);
        $jobService->setDescription($jobServiceData['description']);
        
        
        try {
            // Création de la prestation
            $this->jobServiceMapper->create($jobService);

            // Pour chaque type de prestation
            foreach ($jobServiceData['jobServiceType'] as $idJobServiceType) {
                $jobServiceType = new JobServiceType();
                $jobServiceType->setIdJobServiceType($idJobServiceType);

                // Création de la relation entre le type de prestation et le template
                $this->jobServiceMapper->addJobServiceType($jobService, $jobServiceType);
            }

            // Pour chaque caractéristique utilisateur
            foreach ($jobServiceData['customerCharacteristic'] as $idCustomerCharacteristic) {
                $customerCharacteristic = new CustomerCharacteristic();
                $customerCharacteristic->setIdCustomerCharacteristic($idCustomerCharacteristic);

                // Création de la relation entre la caractéristique et le template
                $this->jobServiceMapper->addCustomerCharacteristic($jobService, $customerCharacteristic);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function editAsFreelance(array $jobServiceData)
    {
        // Création de l'entité de la prestation
        $jobService = new JobService();
        $jobService->setIdJobService($jobServiceData['idJobService']);
        $jobService->setName($jobServiceData['name']);
        $jobService->setDuration($jobServiceData['duration']);
        $jobService->setPrice($jobServiceData['price']);
        $jobService->setDescription($jobServiceData['description']);
        
        try {
            // Création de la prestation
            $this->jobServiceMapper->edit($jobService);

            // Suppression des types de la prestation
            $this->jobServiceMapper->flushServiceType($jobService);

            // Pour chaque type de prestation
            foreach ($jobServiceData['jobServiceType'] as $idJobServiceType) {
                $jobServiceType = new JobServiceType();
                $jobServiceType->setIdJobServiceType($idJobServiceType);

                // Création de la relation entre le type de prestation et le template
                $this->jobServiceMapper->addJobServiceType($jobService, $jobServiceType);
            }

            // Suppression des caractéristiques
            $this->jobServiceMapper->flushCustomerCharacteristic($jobService);

            // Pour chaque caractéristique utilisateur
            foreach ($jobServiceData['customerCharacteristic'] as $idCustomerCharacteristic) {
                $customerCharacteristic = new CustomerCharacteristic();
                $customerCharacteristic->setIdCustomerCharacteristic($idCustomerCharacteristic);

                // Création de la relation entre la caractéristique et le template
                $this->jobServiceMapper->addCustomerCharacteristic($jobService, $customerCharacteristic);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Supprime une prestation
     * 
     * @param int $idJobService
     */
    public function delete($idJobService)
    {
        $jobService = new JobService();
        $jobService->setIdJobService($idJobService);
        
        try {
            $this->jobServiceMapper->delete($jobService);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Vérifie si un account a accès à une prestation
     * 
     * @param int $idAccount
     * @param int $idJobService
     * @return boolean
     */
    public function isAccountGrantedOnJobService($idAccount, $idJobService)
    {
        // Récupération de la prestation
        $jobService = $this->findById($idJobService);
        
        if (isset($jobService)
            && $idAccount == $jobService->getIdProfessional()
        ) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Duplique les caractéristique utilisateur depuis le template
     * 
     * @param JobService $jobService
     * @param JobServiceTemplate $jobServiceTemplate
     */
    private function duplicateCustomerCharacteristicFromJobServiceTemplate(JobService $jobService, JobServiceTemplate $jobServiceTemplate)
    {
        
        try {
            // Suppression des caractéristiques
            $this->jobServiceMapper->flushCustomerCharacteristic($jobService);
            
            // Récupération des caractéristiques du template
            $jobServiceTemplateCustomerCharacteristicList = $this->customerCharacteristicMapper->findByIdJobServiceTemplate($jobServiceTemplate);
            
            // Pour chaque caractéristique
            foreach ($jobServiceTemplateCustomerCharacteristicList as $customerCharacteristic) {
                // Création de la relation entre la caractéristique et la prestation
                $this->jobServiceMapper->addCustomerCharacteristic($jobService, $customerCharacteristic);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
    }
    
    /**
     * Duplique les types de prestation depuis le template
     * 
     * @param JobService $jobService
     * @param JobServiceTemplate $jobServiceTemplate
     */
    private function duplicateJobServiceTypeFromJobServiceTemplate(JobService $jobService, JobServiceTemplate $jobServiceTemplate)
    {
        
        try {
            // Suppression des types de la prestation
            $this->jobServiceMapper->flushServiceType($jobService);

            // Récupération des types de prestation du template
            $jobServiceTemplateJobServiceTypeList = $this->jobServiceTypeMapper->findAllByIdJobServiceTemplate($jobServiceTemplate);

            // Pour chaque type de prestation
            foreach ($jobServiceTemplateJobServiceTypeList as $jobServiceType) {
                // Création de la relation entre le type et la prestation
                $this->jobServiceMapper->addJobServiceType($jobService, $jobServiceType);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
