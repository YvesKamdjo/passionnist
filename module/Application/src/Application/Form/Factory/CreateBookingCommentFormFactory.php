<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form\Factory;

use Application\Form\CreateBookingCommentForm;
use Backend\Entity\BookingComment;
use Backend\Service\BookingCommentService;

class CreateBookingCommentFormFactory
{    
    /**
     * @var BookingCommentService
     */
    private $bookingCommentService;
    
    public function __construct(BookingCommentService $bookingCommentService) {
        $this->bookingCommentService = $bookingCommentService;
    }

    public function CreateBookingCommentForm($bookingId)
    {        
        $storedBookingComment = $this->bookingCommentService->findByBookingId((int) $bookingId);
        
        if ($storedBookingComment === null) {
            $storedBookingComment = new BookingComment();
        }
        
        return new CreateBookingCommentForm($storedBookingComment);
    }
}