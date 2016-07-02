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
use Backend\Entity\Availability;
use Backend\Entity\AvailabilityException;
use Backend\Entity\WeekTemplate;
use Backend\Infrastructure\DataTransferObject\DayAvailability;
use Backend\Mapper\AvailabilityMapper;
use Backend\Mapper\WeekTemplateMapper;
use DateInterval;
use DateTime;
use Zend\Log\Logger;

class AvailabilityService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $availabilityMapper AvailabilityMapper */
    private $availabilityMapper;

    /* @var $weekTemplateMapper WeekTemplateMapper */
    private $weekTemplateMapper;

    public function __construct($availabilityMapper, $weekTemplateMapper, $logger)
    {
        $this->availabilityMapper = $availabilityMapper;
        $this->weekTemplateMapper = $weekTemplateMapper;
        $this->logger = $logger;
    }
    
    public function editAvailablilities(array $availabilityData, $accountId)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            $weekTemplate = $this->weekTemplateMapper->findByAccountId($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        $availabilityCollection = new ArrayCollection();
        
        // Pour chaque jour
        foreach ($availabilityData as $dayName => $day) {
            // Pour chaque plage de disponibilité
            foreach ($day as $newAvailability) {
                
                $availability = new Availability();
                
                list($startHour, $startMinute) = explode(':', $newAvailability['start']);
                list($endHour, $endMinute) = explode(':', $newAvailability['end']);
                
                $startTime = date('H:i:s', mktime($startHour, $startMinute, 0, 0, 0, 0));
                $endTime = date('H:i:s', mktime($endHour, $endMinute, 0, 0, 0, 0));
                
                // Définit l'index du jour selon son nom
                $availability->setDay(date('N', strtotime($dayName)));
                $availability->setStartTime($startTime);
                $availability->setEndTime($endTime);
                $availability->setIdWeekTemplate($weekTemplate->getIdWeekTemplate());
                
                // Ajoute la disponibilité dans la collection à insérer
                $availabilityCollection->add($availability);
            }
        }

        try {
            $this->availabilityMapper->flushAvailabilities($weekTemplate);
            $this->availabilityMapper->createAvailabilities($availabilityCollection);            
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
    }
    
    public function findByWeekTemplateId($weekTemplateId)
    {
        $weekTemplate = new WeekTemplate();
        $weekTemplate->setIdWeekTemplate($weekTemplateId);
        
        try {
            return $this->availabilityMapper->findByWeekTemplateId($weekTemplate);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * @param int $accountId
     * @return ArrayCollection
     * @throws ServiceException
     */
    public function findAvailabilityByAccountId($accountId)
    {
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            return $this->availabilityMapper->findAvailabilityByAccountId($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function createAvailabilityException(array $availabilityData, $accountId)
    {
        // Création de l'entité Account
        $account = new Account();
        $account->setIdAccount($accountId);
        
        
        $availabilityException = new AvailabilityException();
        $availabilityException->setIsAvailability($availabilityData['is-availability'])
            ->setDetails($availabilityData['details'])
            ->setIdAccount($accountId);

        list($startHour, $startMinute) = explode(':', $availabilityData['start-time']);
        list($endHour, $endMinute) = explode(':', $availabilityData['end-time']);

        $startDateTime = date('Y-m-d H:i:s', mktime(
            $startHour, 
            $startMinute, 
            0, 
            $availabilityData['start-month'], 
            $availabilityData['start-day'], 
            $availabilityData['start-year']
        ));
        $endDateTime = date('Y-m-d H:i:s', mktime(
            $endHour, 
            $endMinute, 
            0, 
            $availabilityData['end-month'], 
            $availabilityData['end-day'], 
            $availabilityData['end-year']
        ));

        $availabilityException->setStartDatetime($startDateTime);
        $availabilityException->setEndDatetime($endDateTime);

        try {
            $this->availabilityMapper->createAvailabilityException($availabilityException);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findDayAvailabilityByAccountId($accountId, $expectedDate)
    {
        $account = new Account();
        $account->setIdAccount($accountId);
     
        $date = DateTime::createFromFormat('Y-m-d', $expectedDate);
        
        try {
            $weekTemplate = $this->weekTemplateMapper->findByAccountId($account);
            // Récupération des exceptions de la journée
            $expectedDayAvailabilityExceptions = $this->availabilityMapper->
                findDayAvailabilityExceptionByAccountId($account, $date);
            
            // Récupération de la semaine type
            $expectedDayAvailabilities = $this->availabilityMapper->
                findDayAvailabilityByWeekTemplateId($weekTemplate, $date);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        // Conversion des dispo au même format que les exceptions
        foreach ($expectedDayAvailabilities as $availability) {
            
            $availabilityDate = clone $date;
            
            $dayDifference = $availability->getDay() - ($date->format('w') + 1);
            if ($dayDifference < 0) {
                $availabilityDate->sub(new DateInterval('P'. abs($dayDifference) . 'D'));
            }
           else {
                $availabilityDate->add(new DateInterval('P'. abs($dayDifference) . 'D'));
            }
            
            /* @var $availability Availability */
            $exception = new DayAvailability();
            $exception->startTime = $availabilityDate->format('Y-m-d') . ' ' . $availability->getStartTime();
            $exception->endTime = $availabilityDate->format('Y-m-d') . ' ' .  $availability->getEndTime();
            $exception->day = $availability->getDay();
            $exception->isAvailability = 1;
            
            $expectedDayAvailabilityExceptions->add($exception);
        }
        
        // Tri des disponibilités selon l'heure de départ
        $sortedDayAvailabilities = $this->sortDayAvailabilities(
            iterator_to_array($expectedDayAvailabilityExceptions)
        );
        
        // Calcul des disponibilités en applicant les exceptions
        $calculatedDayAvailabilities = $this->calculateRealDayAvailabilities(
            $sortedDayAvailabilities
        );
        
        // Retourne une liste de disponibilités divisées en tranches de 15 minutes
        return $this->explodeAvailabilities($calculatedDayAvailabilities, 15);
    }
    
    public function findAvailabilityExceptionByAccountId($accountId)
    {
        $account = new Account();
        $account->setIdAccount($accountId);
        
        try {
            return $this->availabilityMapper->findAvailabilityExceptionByAccountId($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findAvailabilityExceptionByAvailabilityExceptionId($availabilityExceptionId)
    {
        $availabilityException = new AvailabilityException();
        $availabilityException->setIdAvailabilityException($availabilityExceptionId);
        
        try {
            return $this->availabilityMapper->findAvailabilityExceptionByAvailabilityExceptionId($availabilityException);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function deleteException($availabilityExceptionId)
    {
        $availabilityException = new AvailabilityException();
        $availabilityException->setIdAvailabilityException($availabilityExceptionId);
        
        try {
            return $this->availabilityMapper->deleteException($availabilityException);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    private function sortDayAvailabilities(array $availabilityList)
    {        
        // Tri des disponibilités
        usort($availabilityList, function($a, $b) {
            $aDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $a->startTime);
            $bDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $b->startTime);
            
            if ($a->day > $b->day) {
                return 1;
            }
            if ($a->day == $b->day
                && $aDateTime > $bDateTime 
            ) {
                return 1;
            }
            else {
                return -1;
            }
        });
        
        return $availabilityList;
    }
    
    private function calculateRealDayAvailabilities(array $availabilityList)
    {
        // Déduction des indisponibilités
        $arrayCount = count($availabilityList);
        for($i = 0; $i < $arrayCount; $i++) {
            $currentAvailability = &$availabilityList[$i];
            if (isset($availabilityList[$i - 1])) {
                $previousAvailability = &$availabilityList[$i - 1];
            }
            else {
                $previousAvailability = new DayAvailability();
            }
            
            // Si les dispo/indispo ne sont pas dans la même journée
            if ($previousAvailability->day != $currentAvailability->day
            ) {
                if ($previousAvailability->isAvailability == false) {
                    unset($availabilityList[$i - 1]);
                }
                
                continue;
            }
            
            // Si l'indispo courante est dans la dispo précédente
            if ($currentAvailability->isAvailability == false
                && $previousAvailability->isAvailability == true
                && strtotime($currentAvailability->startTime) >= strtotime($previousAvailability->startTime)
                && strtotime($currentAvailability->endTime) <= strtotime($previousAvailability->endTime)
            ) {
                $temp = $currentAvailability->endTime;
                $currentAvailability->endTime = $previousAvailability->endTime;
                $previousAvailability->endTime = $currentAvailability->startTime;
                $currentAvailability->startTime = $temp;
                
                if ($currentAvailability->startTime != $currentAvailability->endTime) {
                    $currentAvailability->isAvailability = true;
                }
                
                if ($previousAvailability->startTime == $previousAvailability->endTime) {
                    unset($availabilityList[$i - 1]);
                }
            }
            
            // Si la dispo courante chevauche la dispo précédente
            if ($currentAvailability->isAvailability == true
                && $previousAvailability->isAvailability == true
                && strtotime($currentAvailability->startTime) <= strtotime($previousAvailability->endTime)
            ) {
                $currentAvailability->startTime = $previousAvailability->startTime;
                unset($availabilityList[$i - 1]);
            }
            
            // Si la dispo courante est chevauchée par une indispo
            if ($currentAvailability->isAvailability == true
                && $previousAvailability->isAvailability == false
                && strtotime($currentAvailability->startTime) <= strtotime($previousAvailability->endTime)
            ) {
                $currentAvailability->startTime = $previousAvailability->endTime;
                unset($availabilityList[$i - 1]);
            }
            
            // Si l'indispo courante est chevauchée par une dispo
            if ($currentAvailability->isAvailability == false
                && $previousAvailability->isAvailability == true
                && strtotime($currentAvailability->startTime) <= strtotime($previousAvailability->endTime)
            ) {
                $previousAvailability->endTime = $currentAvailability->startTime;
            }
        }
        
        return $availabilityList;
    }
    
    private function explodeAvailabilities(array $availabilityList, $partDuration)
    {
        // Création de la liste de disponibilités
        $explodedAvailabilityList = [];
        
        // Boucle sur toutes les plages de disponibilité
        foreach ($availabilityList as $availability) {
            /* @var $availability DayAvailability */
            
            // Création de la première partie de la plage
            $partStartTime = strtotime($availability->startTime);
            $partEndTime = strtotime('+'.$partDuration.' minutes', strtotime($availability->startTime));
            // Création de l'heure de fin de la plage
            $availabilityEndTime = strtotime($availability->endTime);
            
            // Tant que l'heure de la fin de la partie n'est pas égale à la 
            // fin de la plage
            while($partEndTime <= $availabilityEndTime) {
                // Création de la partie de disponibilité
                $partAvailability = new DayAvailability();
                $partAvailability->day = $availability->day;
                $partAvailability->isAvailability = $availability->isAvailability;
                $partAvailability->startTime = date('Y-m-d H:i:s', $partStartTime);
                $partAvailability->endTime = date('Y-m-d H:i:s', $partEndTime);
                
                $explodedAvailabilityList[] = $partAvailability;
                
                // Incrémentation du temps
                $partStartTime = strtotime('+'.$partDuration.' minutes', $partStartTime);
                $partEndTime = strtotime('+'.$partDuration.' minutes', $partStartTime);
            }
        }
        
        return $explodedAvailabilityList;
    }
}
