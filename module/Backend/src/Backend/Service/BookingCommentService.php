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
use Backend\Entity\BookingComment;
use Backend\Entity\JobService;
use Backend\Entity\Salon;
use Backend\Mapper\BookingCommentMapper;
use Zend\Log\Logger;

class BookingCommentService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $bookingCommentMapper BookingCommentMapper */
    private $bookingCommentMapper;

    public function __construct($bookingCommentMapper, $logger)
    {
        $this->bookingCommentMapper = $bookingCommentMapper;
        $this->logger = $logger;
    }
    
    /**
     * Ajoute un commentaire
     * 
     * @param array $bookingCommentData
     * ["bookingId"]
     * ["rate"]
     * ["comment"]
     * @return BookingComment
     * @throws ServiceException
     */
    public function add(array $bookingCommentData)
    {
        $bookingComment = new BookingComment();
        $bookingComment->setIdBooking($bookingCommentData["bookingId"]);
        $bookingComment->setRate($bookingCommentData["rate"]);
        $bookingComment->setComment($bookingCommentData["comment"]);
        
        try {
            $this->bookingCommentMapper->create($bookingComment);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        return $bookingComment;
    }
    
    public function findByBookingId($bookingId)
    {
        $bookingComment = new BookingComment();
        $bookingComment->setIdBooking($bookingId);
        
        $storedBookingComment = $this->bookingCommentMapper->findByBookingId($bookingComment);
        
        return $storedBookingComment;
    }
    
    public function findByJobServiceId($jobServiceId)
    {
        $jobService = new JobService();
        $jobService->setIdJobService($jobServiceId);
        
        $storedBookingCommentCollection = $this->bookingCommentMapper
            ->findByJobServiceId($jobService);
        
        return $storedBookingCommentCollection;
    }
    
    /**
     * Récupère tous les commentaires sur un professionnel
     * 
     * @param int $professionalId
     * @return ArrayCollection
     */
    public function findByProfessionalId($professionalId)
    {
        $professional = new Account();
        $professional->setIdAccount($professionalId);
        
        $storedBookingCommentCollection = $this->bookingCommentMapper
            ->findByProfessionalId($professional);
        
        return $storedBookingCommentCollection;
    }
    
    /**
     * Récupère tous les commentaires sur un salon
     * 
     * @param int $salonId
     * @return ArrayCollection
     */
    public function findBySalonId($salonId)
    {
        $salon = new Salon();
        $salon->setIdSalon($salonId);
        
        $storedBookingCommentCollection = $this->bookingCommentMapper
            ->findBySalonId($salon);
        
        return $storedBookingCommentCollection;
    }
}
