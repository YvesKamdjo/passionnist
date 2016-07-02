<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\JobService;
use Backend\Entity\JobServiceTemplate;
use Backend\Mapper\CustomerCharacteristicMapper;
use Zend\Log\Logger;

class CustomerCharacteristicService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $customerCharacteristicMapper CustomerCharacteristicMapper */
    private $customerCharacteristicMapper;

    public function __construct($customerCharacteristicMapper, $logger)
    {
        $this->customerCharacteristicMapper = $customerCharacteristicMapper;
        $this->logger = $logger;
    }

    /**
     * Retourne tous les types de prestation
     * 
     * @return ArrayCollection
     */
    public function listAll()
    {
        try {
            return $this->customerCharacteristicMapper->findAll();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère les caractéristiques pour une prestation
     * 
     * @param JobService $jobService
     * @return ArrayCollection
     */
    public function findByIdJobService(JobService $jobService)
    {
        try {
            return $this->customerCharacteristicMapper->findByIdJobService($jobService);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Récupère les caractéristiques pour un modèle
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @return ArrayCollection
     */
    public function findByIdJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        try {
            return $this->customerCharacteristicMapper->findByIdJobServiceTemplate($jobServiceTemplate);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
