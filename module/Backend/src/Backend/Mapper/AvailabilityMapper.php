<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\Availability;
use Backend\Entity\AvailabilityException;
use Backend\Entity\WeekTemplate;
use Backend\Infrastructure\DataTransferObject\DayAvailability;
use DateTime;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class AvailabilityMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Création des disponibilités depuis une collection
     * 
     * @param ArrayCollection $availabilityCollection
     */
    public function createAvailabilities(ArrayCollection $availabilityCollection)
    {
        // Création des disponibilités
        $select = '
            INSERT INTO Availability (
                idWeekTemplate,
                startTime,
                endTime,
                day
            )
            VALUES (
                :idWeekTemplate,
                :startTime,
                :endTime,
                :day
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            /* @var $availability Availability */
            foreach($availabilityCollection as $availability) {
                $statement->execute([
                    ':idWeekTemplate' => $availability->getIdWeekTemplate(),
                    ':startTime' => $availability->getStartTime(),
                    ':endTime' => $availability->getEndTime(),
                    ':day' => $availability->getDay(),
                ]);
            }
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création de disponibilités",
                null,
                $exception
            );
        }
    }
    
    /**
     * Suppression de toutes les disponibilités d'un template
     * 
     * @param WeekTemplate $weekTemplate
     */
    public function flushAvailabilities(WeekTemplate $weekTemplate)
    {
        // Création du compte
        $delete = '
            DELETE FROM
                Availability
            WHERE
                idWeekTemplate = :idWeekTemplate';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idWeekTemplate' => $weekTemplate->getIdWeekTemplate(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression de toutes disponibilités d'un template",
                null,
                $exception
            );
        }
    }
    
    public function findByWeekTemplateId(WeekTemplate $weekTemplate)
    {
        // Récupère toutes les disponibilités
        $select = '
            SELECT
                idAvailability,
                idWeekTemplate,
                startTime,
                endTime,
                day
            FROM 
                Availability
            WHERE
                idWeekTemplate = :idWeekTemplate
            ORDER BY
                day ASC,
                idAvailability ASC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idWeekTemplate' => $weekTemplate->getIdWeekTemplate()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des disponibilités d'un template",
                null,
                $exception
            );
        }
        
        $availabilityCollection = new ArrayCollection();
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return $availabilityCollection;
        }
        
        // Peuplement de la collection
        foreach ($result as $availabilityRow) {
            $availability = $this->hydrateEntity($availabilityRow);
            $availabilityCollection->add($availability);
        }

        return $availabilityCollection;
    }
    
    /**
     * @param Account $account
     * @return ArrayCollection
     * @throws MapperException
     */
    public function findAvailabilityByAccountId(Account $account)
    {
        // Récupère toutes les disponibilités
        $select = '
            SELECT
                Availability.idAvailability,
                Availability.idWeekTemplate,
                Availability.startTime,
                Availability.endTime,
                Availability.day
            FROM 
                Availability
            INNER JOIN WeekTemplate
                ON WeekTemplate.idWeekTemplate = Availability.idWeekTemplate
                AND WeekTemplate.idAccount = :accountId
            ORDER BY
                day ASC,
                idAvailability ASC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':accountId' => $account->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des disponibilités d'un utilisateur",
                null,
                $exception
            );
        }
        
        $availabilityCollection = new ArrayCollection();
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return $availabilityCollection;
        }
        
        // Peuplement de la collection
        foreach ($result as $availabilityRow) {
            $availability = $this->hydrateEntity($availabilityRow);
            $availabilityCollection->add($availability);
        }

        return $availabilityCollection;
    }
    
    public function findDayAvailabilityByWeekTemplateId(
        WeekTemplate $weekTemplate,
        DateTime $expectedDay
    ) {
        // Récupère toutes les disponibilités
        $select = '
            SELECT
                idAvailability,
                idWeekTemplate,
                startTime,
                endTime,
                day
            FROM 
                Availability
            WHERE
                idWeekTemplate = :idWeekTemplate
            AND
                day = :expectedDayIndex
            ORDER BY
                day ASC,
                idAvailability ASC;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idWeekTemplate' => $weekTemplate->getIdWeekTemplate(),
                ':expectedDayIndex' => $expectedDay->format('w') + 1,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des disponibilités d'un template pour une journée",
                null,
                $exception
            );
        }
        
        $availabilityCollection = new ArrayCollection();
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return $availabilityCollection;
        }
        
        // Peuplement de la collection
        foreach ($result as $availabilityRow) {
            $availability = $this->hydrateEntity($availabilityRow);
            $availabilityCollection->add($availability);
        }

        return $availabilityCollection;
    }
    
    public function createAvailabilityException(AvailabilityException $availabilityException)
    {
        // Création des disponibilités
        $select = '
            INSERT INTO AvailabilityException (
                idAccount,
                startDatetime,
                endDatetime,
                isAvailability,
                details
            )
            VALUES (
                :idAccount,
                :startDatetime,
                :endDatetime,
                :isAvailability,
                :details
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            $statement->execute([
                ':idAccount' => $availabilityException->getIdAccount(),
                ':startDatetime' => $availabilityException->getStartDatetime(),
                ':endDatetime' => $availabilityException->getEndDatetime(),
                ':isAvailability' => $availabilityException->getIsAvailability(),
                ':details' => $availabilityException->getDetails(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'une exception de disponibilité",
                null,
                $exception
            );
        }
    }
    
    public function findDayAvailabilityExceptionByAccountId(
        Account $account,
        DateTime $expectedDate
    ) {
        // Récupère toutes les disponibilités
        $select = '
            (
                SELECT
                    idAvailabilityException,
                    isAvailability,
                    startDatetime startTime,
                    endDatetime endTime,
                    DAYOFWEEK(startDatetime) day
                FROM
                    AvailabilityException
                WHERE
                    idAccount = :idAccount
                AND
                    DATE(startDatetime) = :expectedDate
            )
            UNION (
                SELECT
                    null as idAvailabilityException,
                    0 as isAvailability,
                    start as startTime,
                    DATE_ADD(start, INTERVAL Booking.duration MINUTE) as endTime,
                    DAYOFWEEK(start) day
                FROM
                    Booking
                INNER JOIN JobService
                    ON JobService.idJobService = Booking.idJobService
                WHERE
                    JobService.idProfessional = :idAccount
                AND
                    DATE(start) = :expectedDate
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idAccount' => $account->getIdAccount(),
                ':expectedDate' => $expectedDate->format('Y-m-d'),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des exceptions de disponibilité pour une journée",
                null,
                $exception
            );
        }
        
        $availabilityCollection = new ArrayCollection();
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return $availabilityCollection;
        }
        
        // Peuplement de la collection
        foreach ($result as $availabilityRow) {
            $availability = new DayAvailability();
            $availability->startTime = $availabilityRow['startTime'];
            $availability->endTime = $availabilityRow['endTime'];
            $availability->isAvailability = $availabilityRow['isAvailability'];
            $availability->day = $availabilityRow['day'];
            $availabilityCollection->add($availability);
        }

        return $availabilityCollection;
    }
    
    public function findAvailabilityExceptionByAccountId(Account $professional)
    {
        // Récupère toutes les exceptions
        $select = '
            SELECT
                idAvailabilityException,
                idAccount,
                startDatetime,
                endDatetime,
                isAvailability,
                details
            FROM
                AvailabilityException
            WHERE
                idAccount = :professionalId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':professionalId' => $professional->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération des exceptions d'un professionnel",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $availabilityExceptionCollection = new ArrayCollection();
        foreach ($result as $availabilityExceptionRow) {
            $availabilityException = new AvailabilityException();
            $availabilityException->setIdAvailabilityException($availabilityExceptionRow['idAvailabilityException'])
                ->setIdAccount($availabilityExceptionRow['idAccount'])
                ->setStartDatetime($availabilityExceptionRow['startDatetime'])
                ->setEndDatetime($availabilityExceptionRow['endDatetime'])
                ->setIsAvailability($availabilityExceptionRow['isAvailability'])
                ->setDetails($availabilityExceptionRow['details']);
            
            $availabilityExceptionCollection->add($availabilityException);
        }

        return $availabilityExceptionCollection;
    }
    
    public function findAvailabilityExceptionByAvailabilityExceptionId(
        AvailabilityException $availabilityException
    ) {
        // Récupère toutes les exceptions
        $select = '
            SELECT
                idAvailabilityException,
                idAccount,
                startDatetime,
                endDatetime,
                isAvailability,
                details
            FROM
                AvailabilityException
            WHERE
                idAvailabilityException = :idAvailabilityException;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idAvailabilityException' => $availabilityException->getIdAvailabilityException()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une exception",
                null,
                $exception
            );
        }
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return null;
        }
        
        $availabilityExceptionRow = $result->current();
        
        $storedAvailabilityException = new AvailabilityException();
        $storedAvailabilityException
            ->setIdAvailabilityException($availabilityExceptionRow['idAvailabilityException'])
            ->setIdAccount($availabilityExceptionRow['idAccount'])
            ->setStartDatetime($availabilityExceptionRow['startDatetime'])
            ->setEndDatetime($availabilityExceptionRow['endDatetime'])
            ->setIsAvailability($availabilityExceptionRow['isAvailability'])
            ->setDetails($availabilityExceptionRow['details']);
        
        return $storedAvailabilityException;
    }
    
    public function deleteException(AvailabilityException $availabilityException)
    {
        $delete = '
            DELETE FROM
                AvailabilityException
            WHERE
                idAvailabilityException = :idAvailabilityException';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':idAvailabilityException' => $availabilityException->getIdAvailabilityException(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression d'une exception",
                null,
                $exception
            );
        }
    }
    
    private function hydrateEntity(array $row)
    {
        $availability = new Availability();
        $availability->setIdAvailability($row['idAvailability']);
        $availability->setIdWeekTemplate($row['idWeekTemplate']);
        $availability->setStartTime($row['startTime']);
        $availability->setEndTime($row['endTime']);
        $availability->setDay($row['day']);
        
        return $availability;
    }
}
