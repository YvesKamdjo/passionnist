<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Notification\Service;

use Application\Service\EmailService;
use Notification\DataTransferObject\NewBookingNotificationViewModel;
use Notification\DataTransferObject\NewProspectNotificationViewModel;
use Notification\Finder\NewBookingNotificationFinder;
use Notification\Finder\NewProspectsNotificationFinder;
use Notification\Mapper\NotificationMapper;
use Zend\Log\Logger;

class NotificationService
{
    const MAX_SENDING_THRESHOLD = 50;
    const NOTIFICATION_NEW_PROSPECT = 'NOTIFICATION_NEW_PROSPECT';
    const NOTIFICATION_PROFESSIONAL_NEW_BOOKING = 'NOTIFICATION_PROFESSIONAL_NEW_BOOKING';
    const NOTIFICATION_CUSTOMER_NEW_BOOKING = 'NOTIFICATION_CUSTOMER_NEW_BOOKING';
    
    /* @var $logger Logger */
    private $logger;
    
    /* @var $notificationMapper NotificationMapper */
    private $notificationMapper;
    
    /* @var $emailService EmailService */
    private $emailService;

    /* @var $newProspectsNotificationFinder NewProspectsNotificationFinder */
    private $newProspectsNotificationFinder;
    
    /* @var $newBookingNotificationFinder NewBookingNotificationFinder */
    private $newBookingNotificationFinder;

    public function __construct(
        $newProspectsNotificationFinder,
        $newBookingNotificationFinder,
        $notificationMapper, 
        $emailService,
        $logger
    ) {
        $this->newProspectsNotificationFinder = $newProspectsNotificationFinder;
        $this->newBookingNotificationFinder = $newBookingNotificationFinder;
        $this->notificationMapper = $notificationMapper;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }

    /**
     * Envoie une notification aux prospects afin de leur fournir le lien de 
     * pré-inscription
     */
    public function notifyNewProspects()
    {
        // Récupère la liste des notifications à envoyer
        $notifications = $this->newProspectsNotificationFinder
            ->findAll();
        
        // Boucle sur les notifications pour envoyer les emails et archiver les
        // notifications en persistence
        $sendingCounter = 0;
        /* @var $notification NewProspectNotificationViewModel */
        foreach ($notifications as $notification) {
            
            $this->notificationMapper->save(
                self::NOTIFICATION_NEW_PROSPECT,
                $notification->idProspect
            );
                
            // Envoi le mail de notification
            $sendingCounter++;
            
            // Envoi de l'email 
            $this->emailService->setTemplateName('new-prospect');
            $this->emailService->addTo($notification->email);
            $this->emailService->setSubject('Votre pré-inscription sur HAIRLOV.com!');
            $this->emailService->setTemplateVariables((array) $notification);

            $this->emailService->send();
            
            // Marque un temps de pause avant le prochain envoi
            if ($sendingCounter < self::MAX_SENDING_THRESHOLD) {
                usleep(100000);
            }
            else {
                sleep(5);
                $sendingCounter = 0;
            }
        }
    }
    
    /**
     * Envoie une notification aux professionnels afin de les avertir d'une
     * nouvelle réservation validée
     */
    public function notifyProfessionalNewBooking()
    {
        // Récupère la liste des notifications à envoyer
        $notifications = $this->newBookingNotificationFinder
            ->findAllForProfessional();
        
        // Boucle sur les notifications pour envoyer les emails et archiver les
        // notifications en persistence
        $sendingCounter = 0;
        
        foreach ($notifications as $notification) {
            /* @var $notification NewBookingNotificationViewModel */
            
            $this->notificationMapper->save(
                self::NOTIFICATION_PROFESSIONAL_NEW_BOOKING,
                $notification->idBooking
            );
                
            // Envoi le mail de notification
            $sendingCounter++;
            
            // Envoi de l'email 
            $this->emailService->setTemplateName('professional-new-booking');
            $this->emailService->addTo($notification->professionalEmail);
            $this->emailService->setSubject('Une nouvelle réservation vient d\'être effectuée');
            $this->emailService->setTemplateVariables((array) $notification);

            $this->emailService->send();
            
            // Marque un temps de pause avant le prochain envoi
            if ($sendingCounter < self::MAX_SENDING_THRESHOLD) {
                usleep(100000);
            }
            else {
                sleep(5);
                $sendingCounter = 0;
            }
        }
    }
    
    /**
     * Envoie une notification aux clients afin de les avertir d'une
     * nouvelle réservation validée
     */
    public function notifyCustomerNewBooking()
    {
        // Récupère la liste des notifications à envoyer
        $notifications = $this->newBookingNotificationFinder
            ->findAllForCustomer();
        
        // Boucle sur les notifications pour envoyer les emails et archiver les
        // notifications en persistence
        $sendingCounter = 0;
        
        foreach ($notifications as $notification) {
            /* @var $notification NewBookingNotificationViewModel */
            
            $this->notificationMapper->save(
                self::NOTIFICATION_CUSTOMER_NEW_BOOKING,
                $notification->idBooking
            );
                
            // Envoi le mail de notification
            $sendingCounter++;
            
            // Envoi de l'email 
            $this->emailService->setTemplateName('customer-new-booking');
            $this->emailService->addTo($notification->customerEmail);
            $this->emailService->setSubject('Vous venez d\'effectuer une nouvelle réservation');
            $this->emailService->setTemplateVariables((array) $notification);

            $this->emailService->send();
            
            // Marque un temps de pause avant le prochain envoi
            if ($sendingCounter < self::MAX_SENDING_THRESHOLD) {
                usleep(100000);
            }
            else {
                sleep(5);
                $sendingCounter = 0;
            }
        }
    }
}
