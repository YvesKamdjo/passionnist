<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Entity\Newsletter;
use Backend\Mapper\NewsletterMapper;
use Zend\Log\Logger;

class NewsletterService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $newsletterMapper NewsletterMapper */
    private $newsletterMapper;

    public function __construct($newsletterMapper, $logger)
    {
        $this->newsletterMapper = $newsletterMapper;
        $this->logger = $logger;
    }

    /**
     * Ajoute une adresse email à la newsletter
     */
    public function add($email)
    {
        $newsletter = new Newsletter();
        $newsletter->setEmail($email);
        
        try {
            $this->newsletterMapper->create($newsletter);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
}
