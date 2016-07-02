<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Mapper;

use Zend\Db\Adapter\Adapter;

class NotificationMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Insert un envoi de notification
     * 
     * @param string $idNotification
     * @param string $key
     */
    public function save($idNotification, $key)
    {
        // Ajoute un envoi de notification
        $insert = '
            INSERT INTO
                Notification (
                    idNotification,
                    `key`
                )
            VALUES (
                :idNotification,
                :key
            );';

        $statement = $this->db->createStatement($insert);
        $statement->execute([
                ':idNotification' => $idNotification,
                ':key' => $key,
            ]);
    }
}
