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
use Backend\Entity\Discount;
use Backend\Entity\JobService;
use Backend\Entity\Salon;
use Backend\Mapper\DiscountMapper;
use Backend\Mapper\SalonMapper;
use DateTime;
use Zend\Log\Logger;

class DiscountService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $discountMapper DiscountMapper */
    private $discountMapper;

    /* @var $salonMapper SalonMapper */
    private $salonMapper;

    public function __construct($discountMapper, $salonMapper, $logger)
    {
        $this->discountMapper = $discountMapper;
        $this->salonMapper = $salonMapper;
        $this->logger = $logger;
    }

    
    public function editDiscount(
        array $discountData,
        $salonId = null,
        $freelanceId = null
    ) {
        // Création de l'entité Salon
        $salon = new Salon();
        $salon->setIdSalon($salonId);
        
        // Création de l'entité Freelance
        $freelance = new Account();
        $freelance->setIdAccount($freelanceId);
        
        $discountCollection = new ArrayCollection();
        
        // Pour chaque jour
        foreach ($discountData as $dayName => $day) {
            // Pour chaque plage de disponibilité
            foreach ($day as $newDiscount) {
                
                $discount = new Discount();
                
                list($startHour, $startMinute) = explode(':', $newDiscount['start']);
                list($endHour, $endMinute) = explode(':', $newDiscount['end']);
                
                $startTime = date('H:i:s', mktime($startHour, $startMinute, 0, 0, 0, 0));
                $endTime = date('H:i:s', mktime($endHour, $endMinute, 0, 0, 0, 0));
                
                // Définit l'index du jour selon son nom
                $discount->setDay(date('N', strtotime($dayName)));
                $discount->setStartTime($startTime);
                $discount->setEndTime($endTime);
                $discount->setIdSalon($salon->getIdSalon());
                $discount->setIdFreelance($freelance->getIdAccount());
                $discount->setRate($newDiscount['rate']);
                
                // Ajoute la promotion dans la collection à insérer
                $discountCollection->add($discount);
            }
        }

        try {
            if (is_null($salonId)) {
                $this->discountMapper->flushDiscountByFreelanceId($freelance);
            }
            else {
                $this->discountMapper->flushDiscountBySalonId($salon);
            }
            
            $this->discountMapper->createDiscount($discountCollection);            
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
    }
    
    /**
     * Retourne toutes les promotions d'un salon
     * 
     * @return ArrayCollection
     */
    public function findDiscountBySalonId($salonId)
    {
        $salon = new Salon();
        $salon->setIdSalon($salonId);
        
        try {
            return $this->discountMapper->findDiscountBySalonId($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne toutes les promotions d'un freelance
     * 
     * @return ArrayCollection
     */
    public function findDiscountByFreelanceId($freelanceId)
    {
        $freelance = new Account();
        $freelance->setIdAccount($freelanceId);
        
        try {
            return $this->discountMapper->findDiscountByFreelanceId($freelance);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne la promotion maximum d'un salon
     * 
     * @return Discount
     */
    public function findMaxDiscountBySalonId($salonId)
    {
        $salon = new Salon();
        $salon->setIdSalon($salonId);
        
        try {
            return $this->discountMapper->findMaxDiscountBySalonId($salon);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne la promotion maximum d'un freelance
     * 
     * @return Discount
     */
    public function findMaxDiscountByFreelanceId($freelanceId)
    {
        $freelance = new Account();
        $freelance->setIdAccount($freelanceId);
        
        try {
            return $this->discountMapper->findMaxDiscountByFreelanceId($freelance);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function findDayDiscount($professionalId, $expectedDate)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        $salon = $this->salonMapper->findByEmployeeIdAccount($professional);
        
        $date = DateTime::createFromFormat('Y-m-d', $expectedDate);
        
        if (is_null($salon)) {
            return $this->discountMapper->findDayDiscountByFreelanceId($professional, $date);
        }
        else {
            return $this->discountMapper->findDayDiscountBySalonId($salon, $date);
        }
    }
    
    public function findByDiscountId($discountId)
    {        
        $discount = new Discount();
        $discount->setIdDiscount($discountId);
        
        return $this->discountMapper->findByDiscountId($discount);
    }
    
    public function isJobServiceLinked($jobServiceId, $discountId)
    {
        $jobService = new JobService();
        $jobService->setIdJobService($jobServiceId);
        
        $professional = new Account();
        $professional->setIdAccount($jobService->getIdProfessional());
        
        $discount = new Discount();
        $discount->setIdDiscount($discountId);
        
        $storedDiscount = $this->discountMapper->findByDiscountId($discount);
        
        $salon = $this->salonMapper->findByEmployeeIdAccount($professional);
        
        if ($storedDiscount->getIdFreelance() == $professional->getIdAccount()
            || (isset($salon)
            && $storedDiscount->getIdSalon() == $salon->getIdSalon())) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function findBookingDiscount($jobServiceId, $expectedDate)
    {
        $expectedDateTime = new \DateTime($expectedDate);
        
        $jobService = new JobService();
        $jobService->setIdJobService($jobServiceId);
        
        return $this->discountMapper->findBookingDiscount($jobService, $expectedDateTime);
    }
}
