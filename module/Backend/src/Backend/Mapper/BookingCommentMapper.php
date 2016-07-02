<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\BookingComment;
use Backend\Entity\JobService;
use Backend\Entity\Salon;
use Backend\Infrastructure\DataTransferObject\CompleteBookingComment;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class BookingCommentMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }
    
    public function create(BookingComment $bookingComment)
    {
        // Création du commentaire
        $insert = '
            INSERT INTO BookingComment (
                idBooking,
                rate,
                comment
            )
            VALUES (
                :idBooking,
                :rate,
                :comment
            )
            ON DUPLICATE KEY UPDATE
                rate = :rate,
                comment = :comment;';
        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idBooking' => $bookingComment->getIdBooking(),
                ':rate' => $bookingComment->getRate(),
                ':comment' => $bookingComment->getComment(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'un commentaire",
                null,
                $exception
            );
        }
    }

    public function findByBookingId(BookingComment $bookingComment)
    {
        // Récupère le commentaire
        $select = '
            SELECT
                idBooking,
                comment,
                rate
            FROM 
                BookingComment
            WHERE
                idBooking = :idBooking;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idBooking' => $bookingComment->getIdBooking(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'un commentaire",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }

    public function findByJobServiceId(JobService $jobService)
    {
        // Récupère le commentaire
        $select = '
            SELECT
                BookingComment.idBooking,
                BookingComment.comment,
                BookingComment.rate,
                Booking.start
            FROM 
                BookingComment
            INNER JOIN Booking
                ON Booking.idBooking = BookingComment.idBooking
                AND Booking.idJobService = :jobServiceId
            ORDER BY
                Booking.start DESC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':jobServiceId' => $jobService->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des commentaires d'une prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $bookingCommentCollection = new ArrayCollection();
        foreach ($result as $bookingCommentRow) {
            $completeBookingComment = new CompleteBookingComment();
            $completeBookingComment->idBooking = $bookingCommentRow['idBooking'];
            $completeBookingComment->comment = $bookingCommentRow['comment'];
            $completeBookingComment->rate = $bookingCommentRow['rate'];
            $completeBookingComment->bookingStart = $bookingCommentRow['start'];
            $bookingCommentCollection->add($completeBookingComment);
        }

        return $bookingCommentCollection;
    }
    
    public function findByProfessionalId(Account $professional)
    {
        // Récupère les customer
        $select = '                
            SELECT
                BookingComment.idBooking,
                BookingComment.comment,
                BookingComment.rate,
                Booking.start
            FROM 
                BookingComment
            INNER JOIN Booking
                ON Booking.idBooking = BookingComment.idBooking
            INNER JOIN JobService
                ON JobService.idJobService = Booking.idJobService
                AND JobService.idProfessional = :professionalId
            GROUP BY
                BookingComment.idBooking
            ORDER BY
                Booking.start DESC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':professionalId' => $professional->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des commentaires d'un pro",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $bookingCommentCollection = new ArrayCollection();
        foreach ($result as $bookingCommentRow) {
            $completeBookingComment = new CompleteBookingComment();
            $completeBookingComment->idBooking = $bookingCommentRow['idBooking'];
            $completeBookingComment->comment = $bookingCommentRow['comment'];
            $completeBookingComment->rate = $bookingCommentRow['rate'];
            $completeBookingComment->bookingStart = $bookingCommentRow['start'];
            $bookingCommentCollection->add($completeBookingComment);
        }

        return $bookingCommentCollection;
    }
    
    public function findBySalonId(Salon $salon)
    {
        // Récupère les commentaires
        $select = '                
            SELECT
                BookingComment.idBooking,
                BookingComment.comment,
                BookingComment.rate,
                Booking.start
            FROM 
                BookingComment
            INNER JOIN Booking
                ON Booking.idBooking = BookingComment.idBooking
            INNER JOIN JobService
                ON JobService.idJobService = Booking.idJobService
            INNER JOIN Employee
                ON Employee.idEmployee = JobService.idProfessional
            WHERE
                Employee.idSalon = :salonId
            GROUP BY
                BookingComment.idBooking
            ORDER BY
                Booking.start DESC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':salonId' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des commentaires d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $bookingCommentCollection = new ArrayCollection();
        foreach ($result as $bookingCommentRow) {
            $completeBookingComment = new CompleteBookingComment();
            $completeBookingComment->idBooking = $bookingCommentRow['idBooking'];
            $completeBookingComment->comment = $bookingCommentRow['comment'];
            $completeBookingComment->rate = $bookingCommentRow['rate'];
            $completeBookingComment->bookingStart = $bookingCommentRow['start'];
            $bookingCommentCollection->add($completeBookingComment);
        }

        return $bookingCommentCollection;
    }
    
    /**
     * Peuple un commentaire de prestation
     * 
     * @param array $row
     * @return BookingComment
     */
    private function hydrateEntity(array $row)
    {
        $bookingComment = new BookingComment();
        $bookingComment->setIdBooking($row['idBooking']);
        $bookingComment->setComment($row['comment']);
        $bookingComment->setRate($row['rate']);
        
        return $bookingComment;
    }
}
