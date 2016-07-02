<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\AccountType;
use Backend\Infrastructure\DataTransferObject\JobServiceSearchResult;
use Backend\Infrastructure\DataTransferObject\ProfessionalSearchResult;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;
use Zend\Db\Sql\Having;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class SearchMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }
    
    public function searchSalonJobService(array $searchConditions = [])
    {
        $sql = new Sql($this->db);
        
        // Création du corps de la requête
        $select = $sql->select()
            ->from('JobService')
            ->join(
                'Account',
                'Account.idAccount = JobService.idProfessional',
                [
                    'idAccount',
                    'firstName',
                    'lastName',
                    'accountImageFilename',
                ],
                Select::JOIN_INNER
            )
            ->join(
                'Employee',
                'Employee.idEmployee = Account.idAccount',
                [],
                Select::JOIN_LEFT
            )
            ->join(
                'Salon',
                'Salon.idSalon = Employee.idSalon',
                [
                    'city'
                ],
                Select::JOIN_LEFT
            )
            ->join(
                'Role',
                'Role.idAccount = Account.idAccount',
                [],
                Select::JOIN_INNER
            )
            ->join(
                'JobServiceCustomerCharacteristic',
                'JobServiceCustomerCharacteristic.idJobService = JobService.idJobService',
                [],
                Select::JOIN_LEFT
            )
            ->join(
                'CustomerCharacteristic',
                'CustomerCharacteristic.idCustomerCharacteristic = JobServiceCustomerCharacteristic.idCustomerCharacteristic',
                [],
                Select::JOIN_LEFT
            );
        
        // Définition des colonnes
        $columns = [
            'likeCount' => new Expression('(SELECT COUNT(`Like`.`idProfessionnal`) FROM `Like` WHERE `Like`.`idProfessionnal` = `JobService`.`idProfessional`)'),
            'rateAverage' => new Expression('ROUND((SELECT AVG(`BookingComment`.`rate`) FROM `BookingComment` INNER JOIN `Booking` ON `Booking`.`idBooking` = `BookingComment`.`idBooking` WHERE `Booking`.`idJobService` = `JobService`.`idJobService`))'),
            'jobServiceImageFilename' => new Expression('(SELECT filePath FROM `JobServiceImage` WHERE `JobServiceImage`.`idJobService` = `JobService`.`idJobService` LIMIT 0, 1)'),        
            'idJobService',
            'name',
            'price',
            'duration',
            'customerCharacteristicList' => new Expression('GROUP_CONCAT(CustomerCharacteristic.name)'),
        ];
        // Définition du WHERE principal
        $mainWhere = new Where([], PredicateSet::COMBINED_BY_AND);
        // Définition du HAVING principal
        $mainHaving = new Having([], PredicateSet::COMBINED_BY_AND);
        
        // Si au moins une caractéristique est définie
        if (isset($searchConditions['idCustomerCharacteristic'])
            && is_array($searchConditions['idCustomerCharacteristic'])
        ) {
            // Ajoute la condition sur les caractéristiques
            $predicate = [];
            foreach ($searchConditions['idCustomerCharacteristic'] as $customerCharacteristicId) {
                $predicate[] = new Expression(
                    '? IN (SELECT `JobServiceCustomerCharacteristic`.`idCustomerCharacteristic` FROM `JobServiceCustomerCharacteristic` WHERE `JobServiceCustomerCharacteristic`.`idJobService` = `JobService`.`idJobService`)',
                    [
                        $customerCharacteristicId,
                    ]
                );
            }
            
            $mainWhere->addPredicate(
                new PredicateSet($predicate, PredicateSet::OP_AND)
            );
        }
        
        // Si au moins une caractéristique est définie
        if (isset($searchConditions['date'])) {
            $columns['hasAvailability'] = new Expression('(SELECT COUNT(1) FROM `Availability` INNER JOIN `WeekTemplate` ON `WeekTemplate`.`idWeekTemplate` = `Availability`.`idWeekTemplate` WHERE `WeekTemplate`.`idAccount` = `JobService`.`idProfessional` AND `Availability`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
            $columns['hasAvailabilityException'] = new Expression('(SELECT COUNT(1) FROM `AvailabilityException` WHERE `AvailabilityException`.`idAccount` = `JobService`.`idProfessional` AND DATE(`AvailabilityException`.`startDatetime`) = ?)', [$searchConditions['date']]);
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idSalon` = `Salon`.`idSalon` AND `Discount`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
                
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '(hasAvailability > 0 OR hasAvailabilityException > 0)'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        else {
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idSalon` = `Salon`.`idSalon`)');
        }
        
        // Si un lieu est défini
        if (isset($searchConditions['location'])
            && is_array($searchConditions['location'])
        ) {
            $columns['distance'] = new Expression("
                    CONCAT(
                        ROUND(
                            ST_Distance(
                                geomfromtext(CONCAT('POINT(', Salon.latitude, ' ', Salon.longitude, ')')),
                                geomfromtext(CONCAT('POINT(', ?, ' ', ?, ')'))
                            ) * PI() / 180 * 6371 # Rayon de la Terre
                        )
                    )
                ", [
                    $searchConditions['location']['latitude'],
                    $searchConditions['location']['longitude'],
                ]);
            
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '`distance` < 40'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si au moins un type de prestation est défini
        if (isset($searchConditions['idJobServiceType'])
            && is_array($searchConditions['idJobServiceType'])
        ) {
            // Ajoute la condition sur les types de prestation
            $predicate = [];
            foreach ($searchConditions['idJobServiceType'] as $jobServiceTypeId) {
                $predicate[] = new Expression(
                    '? IN (SELECT `JobServiceJobServiceType`.`idJobServiceType` FROM `JobServiceJobServiceType` WHERE `JobServiceJobServiceType`.`idJobService` = `JobService`.`idJobService`)',
                    [
                        $jobServiceTypeId,
                    ]
                );
            }
            
            $mainWhere->addPredicate(
                new PredicateSet($predicate, PredicateSet::OP_AND)
            );
        }
        
        // Si une tranche de prix est définie
        if (isset($searchConditions['maxPrice'])) {
            // Ajout de la condition sur les prix
            $mainWhere->addPredicate(
                new PredicateSet([
                    new Expression(
                        'price <= ? ', 
                        [
                            $searchConditions['maxPrice'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si une popularité minimum est définie
        if (isset($searchConditions['minLike'])) {
            // Ajout de la condition la popularité minimum
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        'likeCount >= ?', 
                        [
                            $searchConditions['minLike'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si une note minimum est définie
        if (isset($searchConditions['minRate'])) {
            // Ajout de la condition la note minimum
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        'rateAverage >= ?', 
                        [
                            $searchConditions['minRate'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Ajoute la condition sur les images de prestation
        $mainHaving->addPredicate(
            new PredicateSet([
                new Expression(
                    'jobServiceImageFilename <> ""'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Ne récupère que les employés
        $predicate[] = new Expression(
            'idAccountType = ?',
            [
                AccountType::ACCOUNT_TYPE_EMPLOYEE,
            ]
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ne récupère que ceux qui ont une photo de profil
        $predicate[] = new Expression(
            'accountImageFilename IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ajoute la condition sur l'activation du compte
        $mainWhere->addPredicate(
            new PredicateSet([
                new Expression(
                    '`Account`.isActive = true'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Ajoute la condition sur l'activation du salon
        $mainWhere->addPredicate(
            new PredicateSet([
                new Expression(
                    '`Salon`.`isActive` = true'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Intégration des colonnes
        $select->columns($columns)
        // Intégration du WHERE principal
            ->where($mainWhere)
        // Intégration du HAVING principal
            ->having($mainHaving)
        // Regroupement par id de prestation
            ->group('JobService.idJobService')
        // Tri par popularité du professionnel
            ->order('likeCount');

        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la recherche d'une prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $searchResultCollection = new ArrayCollection();
        foreach ($result as $searchResultRow) {
            $searchResult = new JobServiceSearchResult();
            
            $searchResult->likeCount = $searchResultRow['likeCount'];
            $searchResult->accountId = $searchResultRow['idAccount'];
            $searchResult->accountName = $searchResultRow['firstName']. ' ' .$searchResultRow['lastName'];
            $searchResult->jobServiceId = $searchResultRow['idJobService'];
            $searchResult->jobServiceName = $searchResultRow['name'];
            $searchResult->jobServicePrice = $searchResultRow['price'];
            $searchResult->jobServiceLocation = $searchResultRow['city'];
            $searchResult->accountImageFilename = $searchResultRow['accountImageFilename'];
            $searchResult->jobServiceImageFilename = $searchResultRow['jobServiceImageFilename'];
            $searchResult->customerCharacteristicList = $searchResultRow['customerCharacteristicList'];
            $searchResult->maxDiscount = ($searchResultRow['maxDiscount'] != null)? $searchResultRow['maxDiscount']: 0;
            $searchResult->isSalon = true;
            
            $searchResultCollection->add($searchResult);
        }
        
        return $searchResultCollection;
    }
    
    public function searchFreelanceJobService(array $searchConditions = [])
    {
        $sql = new Sql($this->db);
        
        // Création du corps de la requête
        $select = $sql->select()
            ->from('JobService')
            ->join(
                'Account',
                'Account.idAccount = JobService.idProfessional',
                [
                    'idAccount',
                    'firstName',
                    'lastName',
                    'moveRange',
                    'accountImageFilename',
                    'city',
                ],
                Select::JOIN_INNER
            )
            ->join(
                'Employee',
                'Employee.idEmployee = Account.idAccount',
                [],
                Select::JOIN_LEFT
            )
            ->join(
                'Salon',
                'Salon.idSalon = Employee.idSalon',
                [],
                Select::JOIN_LEFT
            )
            ->join(
                'Role',
                'Role.idAccount = Account.idAccount',
                [],
                Select::JOIN_INNER
            )
            ->join(
                'JobServiceCustomerCharacteristic',
                'JobServiceCustomerCharacteristic.idJobService = JobService.idJobService',
                [],
                Select::JOIN_LEFT
            )
            ->join(
                'CustomerCharacteristic',
                'CustomerCharacteristic.idCustomerCharacteristic = JobServiceCustomerCharacteristic.idCustomerCharacteristic',
                [],
                Select::JOIN_LEFT
            );
        
        // Définition des colonnes
        $columns = [
            'likeCount' => new Expression('(SELECT COUNT(`Like`.`idProfessionnal`) FROM `Like` WHERE `Like`.`idProfessionnal` = `JobService`.`idProfessional`)'),
            'rateAverage' => new Expression('ROUND((SELECT AVG(`BookingComment`.`rate`) FROM `BookingComment` INNER JOIN `Booking` ON `Booking`.`idBooking` = `BookingComment`.`idBooking` WHERE `Booking`.`idJobService` = `JobService`.`idJobService`))'),
            'jobServiceImageFilename' => new Expression('(SELECT filePath FROM `JobServiceImage` WHERE `JobServiceImage`.`idJobService` = `JobService`.`idJobService` LIMIT 0, 1)'),
            'idJobService',
            'name',
            'price',
            'duration',
            'customerCharacteristicList' => new Expression('GROUP_CONCAT(CustomerCharacteristic.name)'),
        ];
        // Définition du WHERE principal
        $mainWhere = new Where([], PredicateSet::COMBINED_BY_AND);
        // Définition du HAVING principal
        $mainHaving = new Having([], PredicateSet::COMBINED_BY_AND);
        
        // Si au moins une caractéristique est définie
        if (isset($searchConditions['idCustomerCharacteristic'])
            && is_array($searchConditions['idCustomerCharacteristic'])
        ) {
            // Ajoute la condition sur les caractéristiques
            $predicate = [];
            foreach ($searchConditions['idCustomerCharacteristic'] as $customerCharacteristicId) {
                $predicate[] = new Expression(
                    '? IN (SELECT `JobServiceCustomerCharacteristic`.`idCustomerCharacteristic` FROM `JobServiceCustomerCharacteristic` WHERE `JobServiceCustomerCharacteristic`.`idJobService` = `JobService`.`idJobService`)',
                    [
                        $customerCharacteristicId,
                    ]
                );
            }
            
            $mainWhere->addPredicate(
                new PredicateSet($predicate, PredicateSet::OP_AND)
            );
        }
        
        // Si au moins une caractéristique est définie
        if (isset($searchConditions['date'])) {
            $columns['hasAvailability'] = new Expression('(SELECT COUNT(1) FROM `Availability` INNER JOIN `WeekTemplate` ON `WeekTemplate`.`idWeekTemplate` = `Availability`.`idWeekTemplate` WHERE `WeekTemplate`.`idAccount` = `JobService`.`idProfessional` AND `Availability`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
            $columns['hasAvailabilityException'] = new Expression('(SELECT COUNT(1) FROM `AvailabilityException` WHERE `AvailabilityException`.`idAccount` = `JobService`.`idProfessional` AND DATE(`AvailabilityException`.`startDatetime`) = ?)', [$searchConditions['date']]);
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idFreelance` = `JobService`.`idProfessional` AND `Discount`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
            
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '(hasAvailability > 0 OR hasAvailabilityException > 0)'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        else {
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idFreelance` = `JobService`.`idProfessional`)');
        }
        
        // Si un lieu est défini
        if (isset($searchConditions['location'])
            && is_array($searchConditions['location'])
        ) {
            $columns['distance'] = new Expression("
                    CONCAT(
                        ROUND(
                            ST_Distance(
                                geomfromtext(CONCAT('POINT(', Account.latitude, ' ', Account.longitude, ')')),
                                geomfromtext(CONCAT('POINT(', ?, ' ', ?, ')'))
                            ) * PI() / 180 * 6371 # Rayon de la Terre
                        )
                    )
                ", [
                    $searchConditions['location']['latitude'],
                    $searchConditions['location']['longitude'],
                ]);
            
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '`distance` < moveRange'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si au moins un type de prestation est défini
        if (isset($searchConditions['idJobServiceType'])
            && is_array($searchConditions['idJobServiceType'])
        ) {
            // Ajoute la condition sur les types de prestation
            $predicate = [];
            foreach ($searchConditions['idJobServiceType'] as $jobServiceTypeId) {
                $predicate[] = new Expression(
                    '? IN (SELECT `JobServiceJobServiceType`.`idJobServiceType` FROM `JobServiceJobServiceType` WHERE `JobServiceJobServiceType`.`idJobService` = `JobService`.`idJobService`)',
                    [
                        $jobServiceTypeId,
                    ]
                );
            }
            
            $mainWhere->addPredicate(
                new PredicateSet($predicate, PredicateSet::OP_AND)
            );
        }
        
        // Si une tranche de prix est définie
        if (isset($searchConditions['maxPrice'])) {
            // Ajout de la condition sur les prix
            $mainWhere->addPredicate(
                new PredicateSet([
                    new Expression(
                        'price <= ? ', 
                        [
                            $searchConditions['maxPrice'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si une popularité minimum est définie
        if (isset($searchConditions['minLike'])) {
            // Ajout de la condition la popularité minimum
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        'likeCount >= ?', 
                        [
                            $searchConditions['minLike'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si une note minimum est définie
        if (isset($searchConditions['minRate'])) {
            // Ajout de la condition la note minimum
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        'rateAverage >= ?', 
                        [
                            $searchConditions['minRate'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Ajoute la condition sur les images de prestation
        $mainHaving->addPredicate(
            new PredicateSet([
                new Expression(
                    'jobServiceImageFilename <> ""'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Ne récupère que ceux qui ont une adresse
        $predicate[] = new Expression(
            'Account.latitude IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        $predicate[] = new Expression(
            'Account.longitude IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ne récupère que ceux qui ont un rayon
        $predicate[] = new Expression(
            'moveRange IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ne récupère que ceux qui ont une photo de profil
        $predicate[] = new Expression(
            'accountImageFilename IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ne récupère que les employés
        $predicate[] = new Expression(
            'idAccountType = ?',
            [
                AccountType::ACCOUNT_TYPE_FREELANCE,
            ]
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ajoute la condition sur l'activation du compte
        $mainWhere->addPredicate(
            new PredicateSet([
                new Expression(
                    '`Account`.isActive = true'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Intégration des colonnes
        $select->columns($columns)
        // Intégration du WHERE principal
            ->where($mainWhere)
        // Intégration du HAVING principal
            ->having($mainHaving)
        // Regroupement par id de prestation
            ->group('JobService.idJobService')
        // Tri par popularité du professionnel
            ->order('likeCount');

        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la recherche d'une prestation",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $searchResultCollection = new ArrayCollection();
        foreach ($result as $searchResultRow) {
            $searchResult = new JobServiceSearchResult();
            
            $searchResult->likeCount = $searchResultRow['likeCount'];
            $searchResult->accountId = $searchResultRow['idAccount'];
            $searchResult->accountName = $searchResultRow['firstName']. ' ' .$searchResultRow['lastName'];
            $searchResult->jobServiceId = $searchResultRow['idJobService'];
            $searchResult->jobServiceName = $searchResultRow['name'];
            $searchResult->jobServicePrice = $searchResultRow['price'];
            $searchResult->jobServiceLocation = $searchResultRow['city'];
            $searchResult->accountImageFilename = $searchResultRow['accountImageFilename'];
            $searchResult->jobServiceImageFilename = $searchResultRow['jobServiceImageFilename'];
            $searchResult->customerCharacteristicList = $searchResultRow['customerCharacteristicList'];
            $searchResult->maxDiscount = ($searchResultRow['maxDiscount'] != null)? $searchResultRow['maxDiscount']: 0;
            $searchResult->isSalon = false;
            
            $searchResultCollection->add($searchResult);
        }
        
        return $searchResultCollection;
    }
    
    public function searchSalonProfessional(array $searchConditions = [])
    {
        $sql = new Sql($this->db);
        
        // Création du corps de la requête
        $select = $sql->select()
            ->from('Account')
            ->join(
                'Employee',
                'Employee.idEmployee = Account.idAccount',
                [],
                Select::JOIN_INNER
            )
            ->join(
                'Salon',
                'Salon.idSalon = Employee.idSalon',
                [
                    'city',
                ],
                Select::JOIN_INNER
            )
            ->join(
                'Role',
                'Role.idAccount = Account.idAccount',
                [],
                Select::JOIN_INNER
            );
        
        // Définition des colonnes
        $columns = [
            'likeCount' => new Expression('(SELECT COUNT(`Like`.`idProfessionnal`) FROM `Like` WHERE `Like`.`idProfessionnal` = `Account`.`idAccount`)'),
            'rateAverage' => new Expression('ROUND((SELECT AVG(`BookingComment`.`rate`) FROM `BookingComment` INNER JOIN `Booking` ON `Booking`.`idBooking` = `BookingComment`.`idBooking` INNER JOIN `JobService` ON `JobService`.`idJobService` = `Booking`.`idJobService` WHERE `JobService`.`idProfessional` = `Account`.`idAccount`))'),
            'jobServiceImageFilenameList' => new Expression('(SELECT GROUP_CONCAT(filePath) FROM `JobServiceImage` INNER JOIN `JobService` ON `JobService`.`idJobService` = `JobServiceImage`.`idJobService` WHERE `JobService`.`idProfessional` = `Account`.`idAccount` LIMIT 0, 4)'),
            'jobServiceCount' => new Expression('(SELECT count(1) as count FROM `JobService` INNER JOIN `JobServiceImage` ON `JobServiceImage`.`idJobService` = `JobService`.`idJobService` WHERE `JobService`.`idProfessional` = `Account`.`idAccount`)'),
            'firstName',
            'lastName',
            'accountImageFilename',
            'idAccount',
            'creationDate',
        ];
        // Définition du WHERE principal
        $mainWhere = new Where([], PredicateSet::COMBINED_BY_AND);
        // Définition du HAVING principal
        $mainHaving = new Having([], PredicateSet::COMBINED_BY_AND);
        
        // Si au moins une date est définie
        if (isset($searchConditions['date'])) {
            $columns['hasAvailability'] = new Expression('(SELECT COUNT(1) FROM `Availability` INNER JOIN `WeekTemplate` ON `WeekTemplate`.`idWeekTemplate` = `Availability`.`idWeekTemplate` WHERE `WeekTemplate`.`idAccount` = `Account`.`idAccount` AND `Availability`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
            $columns['hasAvailabilityException'] = new Expression('(SELECT COUNT(1) FROM `AvailabilityException` WHERE `AvailabilityException`.`idAccount` = `Account`.`idAccount` AND DATE(`AvailabilityException`.`startDatetime`) = ?)', [$searchConditions['date']]);
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idSalon` = `Salon`.`idSalon` AND `Discount`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
            
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '(hasAvailability > 0 OR hasAvailabilityException > 0)'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        else {
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idSalon` = `Salon`.`idSalon`)');
        }
        
        // Si un lieu est défini
        if (isset($searchConditions['location'])
            && is_array($searchConditions['location'])
        ) {
            $columns['distance'] = new Expression("
                    CONCAT(
                        ROUND(
                            ST_Distance(
                                geomfromtext(CONCAT('POINT(', Salon.latitude, ' ', Salon.longitude, ')')),
                                geomfromtext(CONCAT('POINT(', ?, ' ', ?, ')'))
                            ) * PI() / 180 * 6371 # Rayon de la Terre
                        )
                    )
                ", [
                    $searchConditions['location']['latitude'],
                    $searchConditions['location']['longitude'],
                ]);
            
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '`distance` < 40'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si une note minimum est définie
        if (isset($searchConditions['minRate'])) {
            // Ajout de la condition la note minimum
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        'rateAverage >= ?', 
                        [
                            $searchConditions['minRate'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Ne récupère que les employés
        $predicate[] = new Expression(
            'idAccountType = ?',
            [
                AccountType::ACCOUNT_TYPE_EMPLOYEE,
            ]
        );

        // Ne récupère que ceux qui ont une photo de profil
        $predicate[] = new Expression(
            'accountImageFilename IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Limite aux professionnels ayant auu moins une prestation avec une image
        $mainHaving->addPredicate(
            new PredicateSet([
                new Expression(
                    '`jobServiceCount` > 0'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Ajoute la condition sur l'activation du compte
        $mainWhere->addPredicate(
            new PredicateSet([
                new Expression(
                    '`Account`.isActive = true'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Ajoute la condition sur l'activation du salon
        $mainWhere->addPredicate(
            new PredicateSet([
                new Expression(
                    '`Salon`.`isActive` = true'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Intégration des colonnes
        $select->columns($columns)
        // Intégration du WHERE principal
            ->where($mainWhere)
        // Intégration du HAVING principal
            ->having($mainHaving)
        // Regroupement par id du pro
            ->group('Account.idAccount')
        // Tri par popularité du professionnel
            ->order('likeCount');

        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la recherche d'un professionnel employé",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $searchResultCollection = new ArrayCollection();
        foreach ($result as $searchResultRow) {    
            $searchResult = new ProfessionalSearchResult();
            
            $searchResult->accountId = $searchResultRow['idAccount'];
            $searchResult->accountLike = $searchResultRow['likeCount'];
            $searchResult->accountLocation = $searchResultRow['city'];
            $searchResult->accountRate = $searchResultRow['rateAverage'];
            $searchResult->accountMaxDiscount = $searchResultRow['maxDiscount'];
            $searchResult->accountCreationDate = $searchResultRow['creationDate'];
            $searchResult->accountName = $searchResultRow['firstName']. ' ' .$searchResultRow['lastName'];
            $searchResult->accountImageFilename = $searchResultRow['accountImageFilename'];
            $searchResult->jobServiceImageFilenameList = $searchResultRow['jobServiceImageFilenameList'];
            $searchResult->isSalon = true;
            
            $searchResultCollection->add($searchResult);
        }
        
        return $searchResultCollection;
    }
    
    public function searchFreelanceProfessional(array $searchConditions = [])
    {
        $sql = new Sql($this->db);
        
        // Création du corps de la requête
        $select = $sql->select()
            ->from('Account')
            ->join(
                'Role',
                'Role.idAccount = Account.idAccount',
                [],
                Select::JOIN_INNER
            );
        
        // Définition des colonnes
        $columns = [
            'likeCount' => new Expression('(SELECT COUNT(`Like`.`idProfessionnal`) FROM `Like` WHERE `Like`.`idProfessionnal` = `Account`.`idAccount`)'),
            'rateAverage' => new Expression('ROUND((SELECT AVG(`BookingComment`.`rate`) FROM `BookingComment` INNER JOIN `Booking` ON `Booking`.`idBooking` = `BookingComment`.`idBooking` INNER JOIN `JobService` ON `JobService`.`idJobService` = `Booking`.`idJobService` WHERE `JobService`.`idProfessional` = `Account`.`idAccount`))'),
            'jobServiceImageFilenameList' => new Expression('(SELECT GROUP_CONCAT(filePath) FROM `JobServiceImage` INNER JOIN `JobService` ON `JobService`.`idJobService` = `JobServiceImage`.`idJobService` WHERE `JobService`.`idProfessional` = `Account`.`idAccount` LIMIT 0, 4)'),
            'jobServiceCount' => new Expression('(SELECT count(1) as count FROM `JobService` INNER JOIN `JobServiceImage` ON `JobServiceImage`.`idJobService` = `JobService`.`idJobService` WHERE `JobService`.`idProfessional` = `Account`.`idAccount`)'),
            'city',
            'firstName',
            'moveRange',
            'lastName',
            'accountImageFilename',
            'idAccount',
            'creationDate',
        ];
        // Définition du WHERE principal
        $mainWhere = new Where([], PredicateSet::COMBINED_BY_AND);
        // Définition du HAVING principal
        $mainHaving = new Having([], PredicateSet::COMBINED_BY_AND);
        
        // Si au moins une date est définie
        if (isset($searchConditions['date'])) {
            $columns['hasAvailability'] = new Expression('(SELECT COUNT(1) FROM `Availability` INNER JOIN `WeekTemplate` ON `WeekTemplate`.`idWeekTemplate` = `Availability`.`idWeekTemplate` WHERE `WeekTemplate`.`idAccount` = `Account`.`idAccount` AND `Availability`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
            $columns['hasAvailabilityException'] = new Expression('(SELECT COUNT(1) FROM `AvailabilityException` WHERE `AvailabilityException`.`idAccount` = `Account`.`idAccount` AND DATE(`AvailabilityException`.`startDatetime`) = ?)', [$searchConditions['date']]);
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idFreelance` = `Account`.`idAccount` AND `Discount`.`day` = DAYOFWEEK(?))', [$searchConditions['date']]);
            
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '(hasAvailability > 0 OR hasAvailabilityException > 0)'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        else {
            $columns['maxDiscount'] = new Expression('(SELECT MAX(rate) FROM `Discount` WHERE `Discount`.`idFreelance` = `Account`.`idAccount`)');
        }
        
        // Si un lieu est défini
        if (isset($searchConditions['location'])
            && is_array($searchConditions['location'])
        ) {
            $columns['distance'] = new Expression("
                    CONCAT(
                        ROUND(
                            ST_Distance(
                                geomfromtext(CONCAT('POINT(', Account.latitude, ' ', Account.longitude, ')')),
                                geomfromtext(CONCAT('POINT(', ?, ' ', ?, ')'))
                            ) * PI() / 180 * 6371 # Rayon de la Terre
                        )
                    )
                ", [
                    $searchConditions['location']['latitude'],
                    $searchConditions['location']['longitude'],
                ]);
            
            // Limite au jour demandé
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        '`distance` < `moveRange`'
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Si une note minimum est définie
        if (isset($searchConditions['minRate'])) {
            // Ajout de la condition la note minimum
            $mainHaving->addPredicate(
                new PredicateSet([
                    new Expression(
                        'rateAverage >= ?', 
                        [
                            $searchConditions['minRate'],
                        ]
                    ),
                ], PredicateSet::OP_AND)
            );
        }
        
        // Ne récupère que les employés
        $predicate[] = new Expression(
            'idAccountType = ?',
            [
                AccountType::ACCOUNT_TYPE_FREELANCE,
            ]
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Limite aux professionnels ayant auu moins une prestation avec une image
        $mainHaving->addPredicate(
            new PredicateSet([
                new Expression(
                    '`jobServiceCount` > 0'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Ajoute la condition sur l'activation du compte
        $mainWhere->addPredicate(
            new PredicateSet([
                new Expression(
                    '`Account`.isActive = true'
                ),
            ], PredicateSet::OP_AND)
        );
        
        // Ne récupère que ceux qui ont une adresse
        $predicate[] = new Expression(
            'Account.latitude IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        $predicate[] = new Expression(
            'Account.longitude IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ne récupère que ceux qui ont un rayon
        $predicate[] = new Expression(
            'moveRange IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Ne récupère que ceux qui ont une photo de profil
        $predicate[] = new Expression(
            'accountImageFilename IS NOT NULL'
        );

        $mainWhere->addPredicate(
            new PredicateSet($predicate, PredicateSet::OP_AND)
        );
        
        // Intégration des colonnes
        $select->columns($columns)
        // Intégration du WHERE principal
            ->where($mainWhere)
        // Intégration du HAVING principal
            ->having($mainHaving)
        // Regroupement par id du pro
            ->group('Account.idAccount')
        // Tri par popularité du professionnel
            ->order('likeCount');

        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $result = $statement->execute();
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la recherche d'un professionnel employé",
                null,
                $exception
            );
        }
        
        // S'il n'y a pas de résultats
        if ($result->isQueryResult() === false || $result->count() === 0) {
            return new ArrayCollection();
        }
        
        // Peuplement de la collection
        $searchResultCollection = new ArrayCollection();
        foreach ($result as $searchResultRow) {    
            $searchResult = new ProfessionalSearchResult();
            
            $searchResult->accountId = $searchResultRow['idAccount'];
            $searchResult->accountLike = $searchResultRow['likeCount'];
            $searchResult->accountLocation = $searchResultRow['city'];
            $searchResult->accountRate = $searchResultRow['rateAverage'];
            $searchResult->accountMaxDiscount = $searchResultRow['maxDiscount'];
            $searchResult->accountCreationDate = $searchResultRow['creationDate'];
            $searchResult->accountName = $searchResultRow['firstName']. ' ' .$searchResultRow['lastName'];
            $searchResult->accountImageFilename = $searchResultRow['accountImageFilename'];
            $searchResult->jobServiceImageFilenameList = $searchResultRow['jobServiceImageFilenameList'];
            $searchResult->isSalon = false;
            
            $searchResultCollection->add($searchResult);
        }
        
        return $searchResultCollection;
    }
}