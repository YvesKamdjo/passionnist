<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Account;
use Backend\Entity\Discount;
use Backend\Entity\JobService;
use Backend\Entity\Salon;
use DateTime;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class DiscountMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Retourne toutes les promotions d'un salon
     * 
     * @param Salon $salon
     * @return ArrayCollection
     */
    public function findDiscountBySalonId(Salon $salon)
    {
        // Récupère toutes les promotions
        $select = '
            SELECT
                idDiscount,
                idSalon,
                startTime,
                endTime,
                rate,
                day
            FROM 
                Discount
            WHERE
                idSalon = :salonId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':salonId' => $salon->getIdSalon()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de toutes les promotions d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $discountCollection = new ArrayCollection();
        foreach ($result as $discountRow) {
            $entity = $this->hydrateEntity($discountRow);
            $discountCollection->add($entity);
        }

        return $discountCollection;
    }

    /**
     * Retourne toutes les promotions d'un freelance
     * 
     * @param Account $freelance
     * @return ArrayCollection
     */
    public function findDiscountByFreelanceId(Account $freelance)
    {
        // Récupère toutes les promotions
        $select = '
            SELECT
                idDiscount,
                idSalon,
                startTime,
                endTime,
                rate,
                day
            FROM 
                Discount
            WHERE
                idFreelance = :freelanceId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':freelanceId' => $freelance->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de toutes les promotions d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $discountCollection = new ArrayCollection();
        foreach ($result as $discountRow) {
            $entity = $this->hydrateEntity($discountRow);
            $discountCollection->add($entity);
        }

        return $discountCollection;
    }
    
    /**
     * Retourne la promotion maximum d'un salon
     * 
     * @param Salon $salon
     * @return ArrayCollection
     */
    public function findMaxDiscountBySalonId(Salon $salon)
    {
        // Récupère toutes les promotions
        $select = '
            SELECT
                idDiscount,
                idSalon,
                startTime,
                endTime,
                MAX(rate) rate,
                day
            FROM 
                Discount
            WHERE
                idSalon = :salonId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':salonId' => $salon->getIdSalon()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de la promotion maximum d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }

    /**
     * Retourne la promotion maximum d'un freelance
     * 
     * @param Account $freelance
     * @return ArrayCollection
     */
    public function findMaxDiscountByFreelanceId(Account $freelance)
    {
        // Récupère toutes les promotions
        $select = '
            SELECT
                idDiscount,
                idSalon,
                startTime,
                endTime,
                MAX(rate) rate,
                day
            FROM 
                Discount
            WHERE
                idFreelance = :freelanceId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':freelanceId' => $freelance->getIdAccount()
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de la promotion maximum d'un freelance",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }

    /**
     * Retourne toutes les promotions d'un salon
     * 
     * @param Salon $salon
     * @return ArrayCollection
     */
    public function findDayDiscountBySalonId(
        Salon $salon,
        DateTime $expectedDay
    ) {
        // Récupère toutes les promotions
        $select = '
            SELECT
                idDiscount,
                idSalon,
                startTime,
                endTime,
                rate,
                day
            FROM 
                Discount
            WHERE
                idSalon = :salonId
            AND
                day = :expectedDayIndex;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':salonId' => $salon->getIdSalon(),
                ':expectedDayIndex' => $expectedDay->format('w') + 1,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de toutes les promotions d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $discountCollection = new ArrayCollection();
        foreach ($result as $discountRow) {
            $entity = $this->hydrateEntity($discountRow);
            $discountCollection->add($entity);
        }

        return $discountCollection;
    }

    /**
     * Retourne toutes les promotions d'un freelance
     * 
     * @param Account $freelance
     * @return ArrayCollection
     */
    public function findDayDiscountByFreelanceId(
        Account $freelance,
        DateTime $expectedDay
    ) {
        // Récupère toutes les promotions
        $select = '
            SELECT
                idDiscount,
                idSalon,
                startTime,
                endTime,
                rate,
                day
            FROM 
                Discount
            WHERE
                idFreelance = :freelanceId
            AND
                day = :expectedDayIndex;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':freelanceId' => $freelance->getIdAccount(),
                ':expectedDayIndex' => $expectedDay->format('w') + 1,
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération de toutes les promotions d'un salon",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $discountCollection = new ArrayCollection();
        foreach ($result as $discountRow) {
            $entity = $this->hydrateEntity($discountRow);
            $discountCollection->add($entity);
        }

        return $discountCollection;
    }
    
    /**
     * Suppression de toutes les promotions d'un salon
     * 
     * @param Salon $salon
     */
    public function flushDiscountBySalonId(Salon $salon)
    {
        // Création du compte
        $delete = '
            DELETE FROM
                Discount
            WHERE
                idSalon = :salonId';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':salonId' => $salon->getIdSalon(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression de toutes promotions d'un salon",
                null,
                $exception
            );
        }
    }
    
    /**
     * Suppression de toutes les promotions d'un freelance
     * 
     * @param Account $freelance
     */
    public function flushDiscountByFreelanceId(Account $freelance)
    {
        // Création du compte
        $delete = '
            DELETE FROM
                Discount
            WHERE
                idFreelance = :freelanceId';

        $statement = $this->db->createStatement($delete);
        
        try {
            $statement->execute([
                ':freelanceId' => $freelance->getIdAccount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la suppression de toutes promotions d'un freelance",
                null,
                $exception
            );
        }
    }
    
    /**
     * Création des promotions depuis une collection
     * 
     * @param ArrayCollection $discountCollection
     */
    public function createDiscount(ArrayCollection $discountCollection)
    {
        // Création des promotions
        $select = '
            INSERT INTO Discount (
                idSalon,
                idFreelance,
                startTime,
                endTime,
                day,
                rate
            )
            VALUES (
                :idSalon,
                :idFreelance,
                :startTime,
                :endTime,
                :day,
                :rate
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            /* @var $discount Discount */
            foreach($discountCollection as $discount) {
                $statement->execute([
                    ':idSalon' => $discount->getIdSalon(),
                    ':idFreelance' => $discount->getIdFreelance(),
                    ':startTime' => $discount->getStartTime(),
                    ':endTime' => $discount->getEndTime(),
                    ':day' => $discount->getDay(),
                    ':rate' => $discount->getRate(),
                ]);
            }
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création de promotions",
                null,
                $exception
            );
        }
    }
    
    public function findByDiscountId(Discount $discount)
    {
        // Récupère la promotion
        $select = '
            SELECT
                idDiscount,
                idSalon,
                startTime,
                endTime,
                rate,
                day
            FROM 
                Discount
            WHERE
                idDiscount = :discountId;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':discountId' => $discount->getIdDiscount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une promotion",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    public function findBookingDiscount(JobService $jobService, DateTime $expectedDate)
    {
        // Récupère la promotion
        $select = '
            SELECT 
                Discount.idDiscount,
                Discount.idSalon,
                Discount.startTime,
                Discount.endTime,
                Discount.rate,
                Discount.day
            FROM
                Discount
            INNER JOIN JobService
                ON JobService.idJobService = :jobServiceId
            INNER JOIN Account
                ON Account.idAccount = JobService.idProfessional
            LEFT JOIN Employee
                ON Employee.idEmployee = Account.idAccount
            WHERE
                DAYOFWEEK(:expectedDate) = Discount.day
            AND
                TIME(:expectedDate) >= Discount.startTime
            AND
                Discount.endTime >= TIME(:expectedDate)
            AND (
                    Discount.idFreelance = Account.idAccount
                OR
                    Discount.idSalon = Employee.idSalon
            );';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':expectedDate' => $expectedDate->format('Y-m-d H:i:s'),
                ':jobServiceId' => $jobService->getIdJobService(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'une promotion",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    private function hydrateEntity(array $row)
    {
        $discount = new Discount();
        $discount->setIdDiscount($row['idDiscount'])
            ->setIdSalon($row['idSalon'])
            ->setStartTime($row['startTime'])
            ->setEndTime($row['endTime'])
            ->setRate($row['rate'])
            ->setDay($row['day']);
        
        return $discount;
    }
}
