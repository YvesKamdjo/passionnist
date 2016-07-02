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
use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobServiceTemplate;
use Backend\Entity\JobServiceType;
use Backend\Entity\Salon;
use Backend\Mapper\JobServiceMapper;
use Backend\Mapper\JobServiceTemplateMapper;
use Backend\Mapper\SalonMapper;
use Zend\Log\Logger;

class JobServiceTemplateService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $jobServiceTemplateMapper JobServiceTemplateMapper */
    private $jobServiceTemplateMapper;

    /* @var $salonMapper SalonMapper */
    private $salonMapper;

    /* @var $jobServiceMapper JobServiceMapper */
    private $jobServiceMapper;

    public function __construct($jobServiceTemplateMapper, $salonMapper, $jobServiceMapper, $logger)
    {
        $this->jobServiceTemplateMapper = $jobServiceTemplateMapper;
        $this->salonMapper = $salonMapper;
        $this->jobServiceMapper = $jobServiceMapper;
        $this->logger = $logger;
    }

    /**
     * Retourne tous les templates de prestation d'un salon
     * 
     * @param int $idSalon
     * @return ArrayCollection
     */
    public function listAllByIdSalon($idSalon)
    {
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        return $this->jobServiceTemplateMapper->findAllByIdSalon($salon);
    }

    /**
     * Retourne tous les templates de prestation actifs d'un salon
     * 
     * @param int $idSalon
     * @return ArrayCollection
     */
    public function findAllActiveByIdSalon($idSalon)
    {
        $salon = new Salon();
        $salon->setIdSalon($idSalon);
        
        return $this->jobServiceTemplateMapper->findAllActiveByIdSalon($salon);
    }
    
    /**
     * Création d'un template de prestation
     * 
     * @param array $jobServiceTemplateData
     */
    public function create(array $jobServiceTemplateData)
    {
        // Création de l'entité de la prestation
        $jobServiceTemplate = new JobServiceTemplate();
        $jobServiceTemplate->setName($jobServiceTemplateData['name']);
        $jobServiceTemplate->setPrice($jobServiceTemplateData['price']);
        $jobServiceTemplate->setIdSalon($jobServiceTemplateData['idSalon']);
        $jobServiceTemplate->setIdManager($jobServiceTemplateData['idManager']);

        
        try {
            // Création du template
            $this->jobServiceTemplateMapper->create($jobServiceTemplate);

            // Pour chaque type de prestation
            foreach ($jobServiceTemplateData['jobServiceType'] as $idJobServiceType) {
                $jobServiceType = new JobServiceType();
                $jobServiceType->setIdJobServiceType($idJobServiceType);

                // Création de la relation entre le type de prestation et le template
                $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $jobServiceType);
            }

            // Pour chaque caractéristique utilisateur
            foreach ($jobServiceTemplateData['customerCharacteristic'] as $idCustomerCharacteristic) {
                $customerCharacteristic = new CustomerCharacteristic();
                $customerCharacteristic->setIdCustomerCharacteristic($idCustomerCharacteristic);

                // Création de la relation entre la caractéristique et le template
                $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $customerCharacteristic);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Modifie d'un template de prestation
     * 
     * @param array $jobServiceTemplateData
     */
    public function edit(array $jobServiceTemplateData)
    {
        // Création de l'entité du modèle de prestation
        $jobServiceTemplate = new JobServiceTemplate();
        $jobServiceTemplate->setName($jobServiceTemplateData['name']);
        $jobServiceTemplate->setPrice($jobServiceTemplateData['price']);
        $jobServiceTemplate->setIdJobServiceTemplate($jobServiceTemplateData['idJobServiceTemplate']);

        try {
            // Modification du template
            $this->jobServiceTemplateMapper->edit($jobServiceTemplate);

            // Suppression des types de la prestation
            $this->jobServiceTemplateMapper->flushServiceType($jobServiceTemplate);

            // Pour chaque type de prestation
            foreach ($jobServiceTemplateData['jobServiceType'] as $idJobServiceType) {
                $jobServiceType = new JobServiceType();
                $jobServiceType->setIdJobServiceType($idJobServiceType);

                // Création de la relation entre le type de prestation et le template
                $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $jobServiceType);
            }

            // Suppression des caractéristiques
            $this->jobServiceTemplateMapper->flushCustomerCharacteristic($jobServiceTemplate);

            // Pour chaque caractéristique utilisateur
            foreach ($jobServiceTemplateData['customerCharacteristic'] as $idCustomerCharacteristic) {
                $customerCharacteristic = new CustomerCharacteristic();
                $customerCharacteristic->setIdCustomerCharacteristic($idCustomerCharacteristic);

                // Création de la relation entre la caractéristique et le template
                $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $customerCharacteristic);
            }

            // Si la synchronisation a été demandée
            if ($jobServiceTemplateData['synchronizeJobService'] === true) {
                // Mise à jour des prestations
                $this->editJobService($jobServiceTemplateData);
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Modifie les prestations d'un modèle
     * 
     * @param array $jobServiceTemplateData
     */
    private function editJobService(array $jobServiceTemplateData)
    {
        // Création de l'entité du modèle de prestation
        $jobServiceTemplate = new JobServiceTemplate();
        $jobServiceTemplate->setName($jobServiceTemplateData['name']);
        $jobServiceTemplate->setPrice($jobServiceTemplateData['price']);
        $jobServiceTemplate->setIdJobServiceTemplate($jobServiceTemplateData['idJobServiceTemplate']);
        
        try {
            // Modification des prestations du modèle
            $this->jobServiceMapper->editFromJobServiceTemplate($jobServiceTemplate);
        
            // Récupération des prestations du modèle
            $jobServiceCollection = $this->jobServiceMapper->findByIdJobServiceTemplate($jobServiceTemplate);
        
            /* @var $jobService = Backend\Entity\JobService */
            foreach ($jobServiceCollection as $jobService) {
                $this->jobServiceMapper->flushServiceType($jobService);

                // Pour chaque type de prestation
                foreach ($jobServiceTemplateData['jobServiceType'] as $idJobServiceType) {
                    $jobServiceType = new JobServiceType();
                    $jobServiceType->setIdJobServiceType($idJobServiceType);

                    // Création de la relation entre le type de prestation et la prestation
                    $this->jobServiceMapper->addJobServiceType($jobService, $jobServiceType);
                }

                // Suppression des caractéristiques
                $this->jobServiceMapper->flushCustomerCharacteristic($jobService);

                // Pour chaque caractéristique utilisateur
                foreach ($jobServiceTemplateData['customerCharacteristic'] as $idCustomerCharacteristic) {
                    $customerCharacteristic = new CustomerCharacteristic();
                    $customerCharacteristic->setIdCustomerCharacteristic($idCustomerCharacteristic);

                    // Création de la relation entre la caractéristique et la prestation
                    $this->jobServiceMapper->addCustomerCharacteristic($jobService, $customerCharacteristic);
                }
            }
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Supprime le modèle de prestation
     * 
     * @param array $deleteData
     */
    public function delete($deleteData)
    {
        // Création du modèle de prestation
        $jobServiceTemplate = new JobServiceTemplate();
        $jobServiceTemplate->setIdJobServiceTemplate($deleteData['idJobServiceTemplate']);
        
        try {
            // Si la synchronisation a été demandée
            if ($deleteData['synchronizeJobService'] === true) {
                // Suppression des prestations de ce modèle
                $this->jobServiceMapper->deleteByIdJobServiceTemplate($jobServiceTemplate);
            }

            // Suppression du modèle
            $this->jobServiceTemplateMapper->delete($jobServiceTemplate);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }

    /**
     * Retourne un modèle de prestation par son id
     * 
     * @param int $idJobServiceTemplate
     * @return JobServiceTemplate
     */
    public function findById($idJobServiceTemplate)
    {
        // Création de l'entité du modèle de prestation
        $jobServiceTemplate = new JobServiceTemplate();
        $jobServiceTemplate->setIdJobServiceTemplate($idJobServiceTemplate);
        
        try {
            // Création du template
            return $this->jobServiceTemplateMapper->findById($jobServiceTemplate);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Vérifie si un account a accès à un template
     * 
     * @param int $idAccount
     * @param int $idJobServiceTemplate
     * @return boolean
     */
    public function isAccountGrantedOnJobServiceTemplate($idAccount, $idJobServiceTemplate)
    {
        // Récupération du template de prestation
        $jobServiceTemplate = $this->findById($idJobServiceTemplate);

        $manager = new Account();
        $manager->setIdAccount($idAccount);
        
        try {
            $salon = $this->salonMapper->findByManagerIdAccount($manager);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        if (isset($jobServiceTemplate)
            && $salon->getIdSalon() == $jobServiceTemplate->getIdSalon()
        ) {
            return true;
        }
        else {
            return false;
        }
    }
}
