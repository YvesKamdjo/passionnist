<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Finder;

use Backend\Collection\ArrayCollection;
use Backend\Collection\CollectionInterface;
use Notification\DataTransferObject\NewProspectNotificationViewModel;
use Notification\Service\NotificationService;
use Zend\Db\Adapter\Adapter;

class NewProspectsNotificationFinder
{
    /** @var Adapter */
    private $db;
    
    /**
     * @param Adapter $db
     */
    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }
    
    /**
     * Retourne la liste des prospects n'ayant pas encore été notifiés de leur
     * pré-inscription.
     * 
     * @return CollectionInterface
     */
    public function findAll()
    {
        $select = '
            SELECT
                idProspect,
                email,
                firstName,
                lastName,
                phone
            FROM
                Prospect
            WHERE
                idProspect NOT IN (
                    SELECT
                        `key`
                    FROM
                        Notification
                    WHERE
                        idNotification = :idNotification
                )
            ;';
            
        $statement = $this->db->createStatement($select);
        $result = $statement->execute([
            ':idNotification' => NotificationService::NOTIFICATION_NEW_PROSPECT
        ]);
        
        $notifications = new ArrayCollection();
        if ($result->isQueryResult() === false || $result->count() < 1) {
            return $notifications;
        }
        
        foreach ($result as $row) {
            $notification = new NewProspectNotificationViewModel();
            
            $notification->idProspect = (int) $row['idProspect'];
            $notification->email = $row['email'];
            $notification->firstName = $row['firstName'];
            $notification->lastName = $row['lastName'];
            $notification->phone = $row['phone'];
            
            $notifications->add($notification);
        }
        
        return $notifications;
    }
}
