<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Backend\Collection\ArrayCollection;
use Backend\Entity\AccountType;
use Backend\Entity\Salon;
use Backend\Service\AccountService;
use Backend\Service\AccountTypeService;
use Backend\Service\AvailabilityService;
use Backend\Service\BookingCommentService;
use Backend\Service\DiscountService;
use Backend\Service\JobServiceImageService;
use Backend\Service\JobServiceService;
use Backend\Service\JobServiceTemplateService;
use Backend\Service\SalonImageService;
use Backend\Service\SalonService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{    
    /** @var AccountService */
    private $accountService;
    /** @var SalonService */
    private $salonService;
    /** @var SalonImageService */
    private $salonImageService;
    /** @var JobServiceTemplateService */
    private $jobServiceTemplateService;
    /** @var JobServiceService */
    private $jobServiceService;
    /** @var BookingCommentService */
    private $bookingCommentService;
    /** @var JobServiceImageService */
    private $jobServiceImageService;
    /** @var AccountTypeService */
    private $accountTypeService;
    /** @var DiscountService */
    private $discountService;
    /** @var AvailabilityService */
    private $availabilityService;

    public function dashboardAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        $accountId = $sessionContainer->account->getIdAccount();
        
        $this->accountService = $serviceManager->get('Backend\Service\Account');
        $this->salonService = $serviceManager->get('Backend\Service\Salon');
        $this->salonImageService = $serviceManager->get('Backend\Service\SalonImage');
        $this->jobServiceTemplateService = $serviceManager->get('Backend\Service\JobServiceTemplate');
        $this->jobServiceService = $serviceManager->get('Backend\Service\JobService');
        $this->bookingCommentService = $serviceManager->get('Backend\Service\BookingComment');
        $this->jobServiceImageService = $serviceManager->get('Backend\Service\JobServiceImage');
        $this->accountTypeService = $serviceManager->get('Backend\Service\AccountType');
        $this->discountService = $serviceManager->get('Backend\Service\Discount');
        $this->availabilityService = $serviceManager->get('Backend\Service\Availability');
        
        // Récupération de l'utilisateur
        $account = $this->accountService->findByIdAccount($accountId);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        // Récupération du rôle principal de l'utilisateur
        $accountTypeList = $this->accountTypeService
            ->findAllByIdAccount($account->getIdAccount());
        
        $roleList = [];
        // Création de la liste des rôles
        /* @var $accountType AccountType */
        foreach ($accountTypeList as $accountType) {
            $roleList[] = $accountType->getIdAccountType();
        }
        
        $hasManagerProfile = false;
        $hasFreelanceProfile = false;
        $hasEmployeeProfile = false;
        
        // Définition du rôle principal de l'utilisateur
        if (in_array(AccountType::ACCOUNT_TYPE_MANAGER, $roleList)) {
            $hasManagerProfile = true;
        }
        elseif (in_array(AccountType::ACCOUNT_TYPE_FREELANCE, $roleList)) {
            $hasFreelanceProfile = true;
        }
        elseif (in_array(AccountType::ACCOUNT_TYPE_EMPLOYEE, $roleList)) {
            $hasEmployeeProfile = true;
        }
        
        // Récupération du salon dont l'utilisateur est gérant
        $managerSalon = $this->salonService
            ->findByManagerIdAccount($account->getIdAccount());
        // Récupération du salon dont l'utilisateur est employé
        $employeeSalon = $this->salonService
            ->findByEmployeeIdAccount($account->getIdAccount());
        
        // Récupération des demandes de rattachement
        if ($managerSalon instanceof Salon) {
            // Récupération de la liste des invitations
            $salonInvitationList = $this->accountService->findAttachmentRequestByManagerInformations([
                'salonId' => $managerSalon->getIdSalon(),
                'managerId' => $account->getIdAccount(),
                'managerEmail' => $account->getEmail(),
            ]);
        }
        else {
            $salonInvitationList = new ArrayCollection();
        }
        
        // Si l'utilisateur est gérant
        $isManager = ($managerSalon instanceof Salon)? true: false;
        // Si l'utilisateur est employé
        $isEmployee = ($employeeSalon instanceof Salon)? true: false;
        
        // Si le salon de l'employé est activé
        if ($employeeSalon instanceof Salon) {
            $isSalonActive = $employeeSalon->isActive();
        }
        else {
            $isSalonActive = false;
        }
        
        // Si le compte est activé
        $isAccountActive = $account->isActive();
        
        // Si le salon du gérant contient des photos
        if ($managerSalon instanceof Salon) {
            $managerSalonImages = $this->salonImageService
                ->findAllBySalonId($managerSalon->getIdSalon());
            
            $hasSalonImages = (bool) $managerSalonImages->count();
        }
        else {
            $hasSalonImages = false;
        }
        
        // Si le salon du gérant dispose d'au moins un type de prestation
        if ($managerSalon instanceof Salon) {
            $managerSalonJobServiceTemplates = $this->jobServiceTemplateService
                ->findAllActiveByIdSalon($managerSalon->getIdSalon());
            
            $hasSalonJobServiceTemplate = (bool) $managerSalonJobServiceTemplates->count();
        }
        else {
            $hasSalonJobServiceTemplate = false;
        }
        
        // Si le salon du gérant dispose d'au moins une promotion
        if ($managerSalon instanceof Salon) {
            $managerSalonDiscounts = $this->discountService
                ->findDiscountBySalonId($managerSalon->getIdSalon());
            
            $hasSalonDiscount = (bool) $managerSalonDiscounts->count();
        }
        else {
            $hasSalonDiscount = false;
        }
        
        // Si le salon du gérant dispose d'un k-bis
        if ($managerSalon instanceof Salon
            && strlen($managerSalon->getCertificateFilename()) > 0
        ) {            
            $hasSalonCertificate = true;
        }
        else {
            $hasSalonCertificate = false;
        }
        
        // Si l'utilisateur dispose d'au moins une promotion
        $accountDiscounts = $this->discountService
            ->findDiscountByFreelanceId($account->getIdAccount());
        $hasDiscount = (bool) $accountDiscounts->count();
        
        // Si l'utilisateur a défini sa biographie
        if (strlen($account->getBiography()) > 0) {
            $hasBiography = true;
        }
        else {
            $hasBiography = false;
        }
        
        // Si l'utilisateur a uploadé une photo de profil
        if (strlen($account->getAccountImageFilename()) > 0) {
            $hasAccountImage = true;
        }
        else {
            $hasAccountImage = false;
        }
        
        // Si l'utilisateur a uploadé une diplôme
        if (strlen($account->getQualificationFilename()) > 0) {
            $hasQualification = true;
        }
        else {
            $hasQualification = false;
        }
        
        // Si l'utilisateur a défini son rayon d'action
        if ($account->getMoveRange() > 0) {
            $hasMoveRange = true;
        }
        else {
            $hasMoveRange = false;
        }
        
        // Si l'utilisateur a créé au moins une prestation
        $accountJobService = $this->jobServiceService
            ->listAllByIdAccount($account->getIdAccount());
        $hasJobService = (bool) $accountJobService->count();
        
        // Si l'utilisateur dispose d'au moins une plage de disponibilité
        $accountAvailabilities = $this->availabilityService
            ->findAvailabilityByAccountId($account->getIdAccount());
        $hasAvailability = (bool) $accountAvailabilities->count();
        
        // Si l'utilisateur est un gérant et respecte les contraintes
        if ((
            $hasManagerProfile === true
            && $isAccountActive === true
            && $isSalonActive === true
            && $hasSalonImages === true
            && $hasSalonJobServiceTemplate === true
            && $hasSalonCertificate === true
            && $isManager === true
        )
        // Si l'utilisateur est un employé et respecte les contraintes
        || (
            $hasEmployeeProfile === true
            && $isAccountActive === true
            && $isSalonActive === true
            && $hasAccountImage === true
            && $hasQualification === true
            && $hasAvailability === true
            && $hasJobService === true
            && $isEmployee === true
        )
        // Si l'utilisateur est un freelance et respecte les contraintes
        || (
            $hasFreelanceProfile === true
            && $isAccountActive === true
            && $hasAccountImage === true
            && $hasQualification === true
            && $hasAvailability === true
            && $hasJobService === true
            && $hasMoveRange === true
        )) {
            $sessionContainer->newUserBanner = false;
            
            // Récupère la liste des customer qui aiment le pro
            $customerWhoLikeCollection = $this->accountService
                ->findCustomerWhoLikeByProfessionalId($sessionContainer->account->getIdAccount());

            // Récupère la liste des customer qui aiment le pro
            $professionalBookingCommentCollection = $this->bookingCommentService
                ->findByProfessionalId($sessionContainer->account->getIdAccount());

            // Récupération de la prochaine réservation
            $lastJobServiceImages = $this->jobServiceImageService
                ->findProfessionalLastJobServiceImage($sessionContainer->account->getIdAccount(), 8);
            
            $viewModel->setTemplate('professionnal/index/dashboard');
            
            $viewModel->setVariables([
                'customerWhoLikeCounter' => $customerWhoLikeCollection->count(),
                'professionalBookingCommentCounter' => $professionalBookingCommentCollection->count(),
                'lastJobServiceImages' => $lastJobServiceImages,
                'salon' => $managerSalon,
            ]);
        }
        else {
            $sessionContainer->newUserBanner = true;
            
            $viewModel->setTemplate('professionnal/index/new-account-dashboard');
            
            $viewModel->setVariables([
                'hasManagerProfile' => $hasManagerProfile,
                'hasEmployeeProfile' => $hasEmployeeProfile,
                'hasFreelanceProfile' => $hasFreelanceProfile,
                'isAccountActive' => $isAccountActive,
                'isSalonActive' => $isSalonActive,
                'isEmployee' => $isEmployee,
                'isManager' => $isManager,
                'hasAccountImage' => $hasAccountImage,
                'hasAvailability' => $hasAvailability,
                'hasBiography' => $hasBiography,
                'hasDiscount' => $hasDiscount,
                'hasJobService' => $hasJobService,
                'hasMoveRange' => $hasMoveRange,
                'hasQualification' => $hasQualification,
                'hasSalonDiscount' => $hasSalonDiscount,
                'hasSalonCertificate' => $hasSalonCertificate,
                'hasSalonImages' => $hasSalonImages,
                'hasSalonJobServiceTemplate' => $hasSalonJobServiceTemplate,
                'salon' => $managerSalon,
            ]);
        }
        
        $viewModel->setVariable('hasAttachmentRequest', (bool) $salonInvitationList->count());
        
        return $viewModel;
    }
}
