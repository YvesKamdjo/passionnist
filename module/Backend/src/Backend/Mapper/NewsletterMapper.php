<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Newsletter;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class NewsletterMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    /**
     * Inscrit une adresse email à la newsletter
     * 
     * @return ArrayCollection
     */
    public function create(Newsletter $newsletter)
    {
        // Ajoute l'adresse email
        $insert = '
            INSERT IGNORE INTO Newsletter (
                email
            )
            VALUES (
                :email
            );';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':email' => $newsletter->getEmail(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de l'inscription d'une adresse à la newsletter",
                null,
                $exception
            );
        }
    }
}
