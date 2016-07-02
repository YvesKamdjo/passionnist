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
use Backend\Mapper\JobServiceTypeMapper;
use Zend\Log\Logger;

class JobServiceTypeService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $jobServiceTypeMapper JobServiceTypeMapper */
    private $jobServiceTypeMapper;

    public function __construct($jobServiceTypeMapper, $logger)
    {
        $this->jobServiceTypeMapper = $jobServiceTypeMapper;
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
            return $this->jobServiceTypeMapper->findAll();
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne tous les types de prestation d'une prestation
     * 
     * @param JobService $jobService
     * @return ArrayCollection
     */
    public function findByIdJobService(JobService $jobService)
    {
        try {
            return $this->jobServiceTypeMapper->findByIdJobService($jobService);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne tous les types de prestation d'un modèle
     * 
     * @param JobServiceTemplate $jobServiceTemplate
     * @return ArrayCollection
     */
    public function findByIdJobServiceTemplate(JobServiceTemplate $jobServiceTemplate)
    {
        try {
            return $this->jobServiceTypeMapper->findByIdJobServiceTemplate($jobServiceTemplate);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
