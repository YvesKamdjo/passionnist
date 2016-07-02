<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Mapper\PermissionMapper;
use Zend\Log\Logger;

class AuthorizationService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $permissionMapper PermissionMapper */
    private $permissionMapper;
    
    private $permissions;
    
    public function __construct(PermissionMapper $permissionMapper, Logger $logger) 
    {
        $this->permissionMapper = $permissionMapper;
        $this->logger = $logger;
        
        $this->createPermissions();
    }
    
    /**
     * Vérifie si un utilisateur dispose de la permission d'accéder à la 
     * route demandée
     * 
     * @param array $accountPermissionList
     * @param string $expectedRoute
     * @return boolean
     */
    public function isAllowed(array $accountPermissionList, $expectedRoute)
    {
        foreach ($accountPermissionList as $permission) {
            if (isset($this->permissions[$permission])
                && in_array($expectedRoute, $this->permissions[$permission])
            ) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Crée les permissions disponibles avec les routes correspondantes
     */
    private function createPermissions()
    {
        $this->permissions = [
            'create-salon' => [
                'professionnal-salon/create',
            ],
            'access-professional-booking' => [
                'professionnal-booking',
            ],
            'edit-discount' => [
                'professionnal-discount/edit',
            ],
            'edit-salon' => [
                'professionnal-salon/edit',
                'professionnal-salon/send-certificate',
                'professionnal-salon/salon-attachment-request-list',
                'professionnal-salon/salon-attachment-request-list/accept',
                'professionnal-salon/salon-attachment-request-list/refuse',
                'professionnal-salon/manage-images',
                'professionnal-salon/send-image',
                'professionnal-salon/delete-image',
            ],
            'create-attachment-request' => [
                'professionnal-join-salon',
            ],
            'edit-professionnal-profile' => [
                'professionnal-profile-edit',
                'professionnal-profile-edit/send-avatar',
                'professionnal-profile-edit/send-qualification',
            ],
            'edit-freelance-move-range' => [
                'edit-freelance-move-range',
            ],
            'access-professionnal-dashboard' => [
                'professionnal-dashboard',
            ],
            'access-job-service-template-list' => [
                'professionnal-job-service-template',
            ],
            'create-job-service-template' => [
                'professionnal-job-service-template/create',
            ],
            'edit-job-service-template' => [
                'professionnal-job-service-template/edit',
                'professionnal-job-service-template/delete',
            ],
            'access-job-service-list' => [
                'professionnal-job-service',
            ],
            'create-job-service' => [
                'professionnal-job-service/create',
            ],
            'edit-job-service' => [
                'professionnal-job-service/manage-images',
                'professionnal-job-service/send-image',
                'professionnal-job-service/delete-image',
                'professionnal-job-service/edit',
                'professionnal-job-service/delete',
            ],
            'access-administration-salon' => [
                'administration-salon',
                'administration-salon/certificate',
                'administration-salon/activate',
                'administration-salon/deactivate',
            ],
            'access-administration-account' => [
                'administration-account',
                'administration-account/activate',
                'administration-account/deactivate',
                'administration-account/qualification',
                'administration-account/take-over',
                'administration-account/edit',
            ],
            'access-customer-booking' => [
                'application-booking-list',
                'application-booking/comment',
                'application-booking/generate-invoice',
            ],
            'edit-availabilities' => [
                'professionnal-availabilities',
                'professionnal-availabilities/edit',
                'professionnal-availabilities/create-availability',
                'professionnal-availabilities/create-absence',
                'professionnal-availabilities/delete-exception',
                'professionnal-availabilities/exception-list',
            ],
            'access-administration-propect' => [
                'administration-prospect',
                'administration-prospect/create',
            ],
            'create-transfer-request' => [
                'professionnal-financial/create-transfer-request',
            ],
            'list-transfer-request' => [
                'professionnal-financial/list-transfer-request',
            ],
            'list-transaction' => [
                'professionnal-financial/list-transaction',
            ],
            'job-service-booking' => [
                'application-job-service/booking',
            ],
            'access-public-pages' => [
                'application-home',
                'application-subscribe-newsletter',
                'application-contact',
                'application-legal',
                'application-login',
                'application-facebook-login',
                'application-logout',
                'application-professionnal-sign-up',
                'administration-account/take-over',
                'administration-account/end-take-over',
                'get-new-fashion-images',
                'application-load-image',
                'application-search-job-service',
                'application-search-professional',
                'application-professional',
                'application-liked-professional-list',
                'application-salon',
                'application-salon/professional',
                'application-salon/job-service',
                'application-salon/booking-comment',
                'application-job-service',
                'application-ipn',
                'application-return-payment',
                'application-canceled-payment',
                'application-professional-landing-page',
                'application-job-service/date-changed',
                'application-switch-like',
                'application-load-more-fashion-images',
                'application-password-lost',
                'application-new-password',
                'notify-new-prospect',
                'notify-professional-new-booking',
                'notify-customer-new-booking',
            ],
        ];
    }
   
    /**
     * Récupère les permissions d'un utilisateur
     * 
     * @param Account $account
     * @return array
     */
    public function getAccountPermissions(Account $account)
    {
        try {
            return $this->permissionMapper->findByIdAccount($account);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne la liste des permissions par défaut d'un gérant
     * 
     * @return array
     */
    public static function getManagerPermissions()
    {
        return [
            'access-professionnal-dashboard',
            'access-public-pages',
            'create-salon',
            'edit-professionnal-profile',
        ];
    }
    
    /**
     * Retourne la liste des permissions par défaut d'un employé
     * 
     * @return array
     */
    public static function getEmployeePermissions()
    {
        return [
            'access-customer-booking',
            'access-professional-booking',
            'access-professionnal-dashboard',
            'access-public-pages',
            'create-attachment-request',
            'edit-availabilities',
            'edit-professionnal-profile',
            'job-service-booking',
        ];
    }
    
    /**
     * Retourne la liste des permissions par défaut d'un indépendant
     * 
     * @return array
     */
    public static function getFreelancePermissions()
    {
        return [
            'access-customer-booking',
            'access-job-service-list',
            'access-professional-booking',
            'access-professionnal-dashboard',
            'access-public-pages',
            'create-job-service',
            'create-transfer-request',
            'edit-availabilities',
            'edit-discount',
            'edit-freelance-move-range',
            'edit-job-service',
            'edit-professionnal-profile',
            'job-service-booking',
            'list-transfer-request',
            'list-transaction',
        ];
    }
    
    /**
     * Retourne la liste des permissions par défaut d'un administrateur
     * 
     * @return array
     */
    public static function getAdminPermissions()
    {
        return [
            'access-administration-account',
            'access-administration-propect',
            'access-administration-salon',
            'access-public-pages',
        ];
    }
    
    /**
     * Retourne la liste des permissions par défaut d'un client
     * 
     * @return array
     */
    public static function getCustomerPermissions()
    {
        return [
            'access-customer-booking',
            'access-public-pages',
            'job-service-booking',
        ];
    }
}
