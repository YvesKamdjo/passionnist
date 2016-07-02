<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Listener;

use Administration\Infrastructure\DataTransferObject\HeaderNavItem as AdminHeaderNavItem;
use Application\Infrastructure\DataTransferObject\HeaderNavItem as ApplicationHeaderNavItem;
use Application\Infrastructure\DataTransferObject\SubmenuNavItem;
use Application\Service\AuthorizationService;
use Backend\Service\AccountService;
use Professionnal\Infrastructure\DataTransferObject\HeaderNavItem as ProfessionalHeaderNavItem;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\HelperPluginManager;

class LayoutInitializerListener extends AbstractListenerAggregate
{
    /** @var ServiceManager */
    protected $serviceManager;
    /** @var HelperPluginManager */
    private $viewHelperManager;
    /** @var string */
    private $applicationVersion;
    
    /**
     * @param ServiceManager $serviceManager
     * @param string $applicationVersion
     */
    public function __construct(
        ServiceManager $serviceManager,
        $applicationVersion
    ) {
        $this->serviceManager = $serviceManager;
        $this->viewHelperManager = $this->serviceManager->get('viewHelperManager');
        $this->applicationVersion = $applicationVersion;
    }
    
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH,
            [$this, 'initializeLayout'],
            2
        );
    }
    
    /**
     * @param MvcEvent $event
     */
    public function initializeLayout(MvcEvent $event)
    {
        // Récupère l'utilisateur connecté
        $sessionContainer = new Container('hairlov');
        
        // La route commence par "professionnal", initialise le layout du module Professionnal
        if (strpos($event->getRouteMatch()->getMatchedRouteName(), 'professionnal') === 0) {
            $this->initializeProfessionnalLayout($event, $sessionContainer);
        }
        // La route commence par "administration", initialise le layout du module Administration
        elseif (strpos($event->getRouteMatch()->getMatchedRouteName(), 'administration') === 0) {
            $this->initializeAdministrationLayout($event, $sessionContainer);
        }
        // Par défaut, initialise le layout du module Application
        else {
            $this->initializeApplicationLayout($event, $sessionContainer);
        }
    }
    
    private function initializeApplicationLayout(
        MvcEvent $event,
        Container $sessionContainer = null
    ) {
        // Récupération des permissions de l'utilisateur
        // Récupération du service d'autorisation
        /* @var $authorizationService AuthorizationService */
        $authorizationService = $this->serviceManager->get(
            'Application\Service\Authorization'
        );

        // Récupération du service Account
        /* @var $accountService AccountService */
        $accountService = $this->serviceManager->get(
            'Backend\Service\Account'
        );
        
        // Récupération des permissions de l'utilisateur connecté
        if (isset($sessionContainer->account)) {
            $accountPermissionList = $authorizationService->getAccountPermissions($sessionContainer->account);
        }
        else {
            $accountPermissionList = [];
        }
        
        // Configure le titre par défaut des pages
        $this->viewHelperManager
            ->get('headTitle')
            ->setSeparator(' - ')
            ->append('Hairlov')
            ->setAutoEscape(false);
        
        // Configure les feuilles de styles et autres ressources
        $this->viewHelperManager
            ->get('headLink')
            ->appendStylesheet("https://fonts.googleapis.com/css?family=Open+Sans:400,300,700")
            ->appendStylesheet("https://fonts.googleapis.com/icon?family=Material+Icons")
            ->appendStylesheet("/css/vendor.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/components.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/hairlov-application.css?v=$this->applicationVersion")
        ;
        
        // Configure les scripts
        $this->viewHelperManager
            ->get('headScript')
            ->appendFile("/js/vendor.js?v=$this->applicationVersion")
            ->appendFile("/js/hairlov.js?v=$this->applicationVersion")
            ->appendFile("/js/hairlov-application.js?v=$this->applicationVersion")
        ;

        $config = $this->serviceManager->get('config');
        
        // Défini le layout par défaut à utiliser pour le module
        $layoutViewModel = $event->getViewModel();
        $layoutViewModel->setTemplate('layout/application');
        $layoutViewModel->setVariable('isLoggedIn', isset($sessionContainer->account));
        $layoutViewModel->setVariable('isTakeOver', isset($sessionContainer->savedAccount));
        $layoutViewModel->setVariable('accountPermissionList', $accountPermissionList);
        $layoutViewModel->setVariable('urlDomain', $config['mailer']['urlDomain']);
        if(isset($sessionContainer->account)){
            $layoutViewModel->setVariable('account', $accountService->findByIdAccount($sessionContainer->account->getIdAccount()));
        }
        
        // Sélectionne l'item dans le menu de navigation de la sidebar en
        // fonction de la route
        if ($event->getRouteMatch() instanceof RouteMatch) {
            $navItemMap = [
                'application-home' => ApplicationHeaderNavItem::DASHBOARD,
                'application-search-job-service' => ApplicationHeaderNavItem::SEARCH_JOB_SERVICE,
                'application-search-professional' => ApplicationHeaderNavItem::SEARCH_PROFESSIONAL,
            ];
            
            $routeName = $event->getRouteMatch()->getMatchedRouteName();
            if (strpos($routeName, '/') !== false) {
                $routeName = strstr($routeName, '/', true);
            }

            if (isset($navItemMap[$routeName]) === true) {
                $layoutViewModel->setVariable(
                    'selectedHeaderNavItem',
                    $navItemMap[$routeName]
                );
            }
        }
    }
    
    private function initializeProfessionnalLayout(
        MvcEvent $event,
        Container $sessionContainer = null
    ) {
        // Récupération des permissions de l'utilisateur
        // Récupération du service d'autorisation
        /* @var $authorizationService AuthorizationService */
        $authorizationService = $this->serviceManager->get(
            'Application\Service\Authorization'
        );
        
        // Récupération du service Account
        /* @var $accountService AccountService */
        $accountService = $this->serviceManager->get(
            'Backend\Service\Account'
        );
        
        // Récupération des permissions de l'utilisateur connecté
        if (isset($sessionContainer->account)) {
            $accountPermissionList = $authorizationService->getAccountPermissions($sessionContainer->account);
        }
        else {
            $accountPermissionList = [];
        }
        
        // Configure le titre par défaut des pages
        $this->viewHelperManager
            ->get('headTitle')
            ->prepend('Hairlov Pro')
            ->setSeparator(' - ')
            ->setAutoEscape(false);
        
        // Configure les feuilles de styles et autres ressources
        $this->viewHelperManager
            ->get('headLink')
            ->appendStylesheet("https://fonts.googleapis.com/css?family=Open+Sans:400,300,700")
            ->appendStylesheet("https://fonts.googleapis.com/icon?family=Material+Icons")
            ->appendStylesheet("/css/vendor.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/components.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/hairlov.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/hairlov-professionnal.css?v=$this->applicationVersion")
        ;
        
        // Configure les scripts
        $this->viewHelperManager
            ->get('headScript')
            ->appendFile("/js/vendor.js?v=$this->applicationVersion")
            ->appendFile("/js/hairlov.js?v=$this->applicationVersion")
            ->appendFile("/js/hairlov-professionnal.js?v=$this->applicationVersion")
        ;
        
        // Défini le layout par défaut à utiliser pour le module
        $layoutViewModel = $event->getViewModel();
        $layoutViewModel->setTemplate('layout/professionnal');
        $layoutViewModel->setVariable('isLoggedIn', isset($sessionContainer->account));
        $layoutViewModel->setVariable('isTakeOver', isset($sessionContainer->savedAccount));
        $layoutViewModel->setVariable('accountPermissionList', $accountPermissionList);
        $layoutViewModel->setVariable('account', $accountService->findByIdAccount($sessionContainer->account->getIdAccount()));
        $layoutViewModel->setVariable('actualRoute', $event->getRouteMatch()->getMatchedRouteName());
        
        if (isset($sessionContainer->newUserBanner)) {
            $layoutViewModel->setVariable('newUserBanner', $sessionContainer->newUserBanner);
        }
        else {
            $layoutViewModel->setVariable('newUserBanner', false);
        }
        
        // Sélectionne l'item dans le menu de navigation de la sidebar en
        // fonction de la route
        if ($event->getRouteMatch() instanceof RouteMatch) {
            $navItemMap = [
                'professionnal-dashboard' => ProfessionalHeaderNavItem::DASHBOARD,
                'professionnal-booking' => ProfessionalHeaderNavItem::BOOKING,
                'professionnal-discount' => ProfessionalHeaderNavItem::DISCOUNT,
                'professionnal-salon' => ProfessionalHeaderNavItem::SALON,
                'professionnal-job-service-template' => ProfessionalHeaderNavItem::SALON,
                'professionnal-availabilities' => ProfessionalHeaderNavItem::AVAILABILITIES,
                'professionnal-job-service' => ProfessionalHeaderNavItem::JOB_SERVICE,
                'professionnal-profile-edit' => ProfessionalHeaderNavItem::EDIT_PROFILE,
                'professionnal-financial' => ProfessionalHeaderNavItem::FINANCIAL,
            ];
            
            $subnavItemMap = [
                'professionnal-salon/salon-attachment-request-list' => SubmenuNavItem::ATTACHMENT_REQUEST_LIST,
                'professionnal-job-service-template' => SubmenuNavItem::JOB_SERVICE_TEMPLATE_LIST,
                'professionnal-salon/edit' => SubmenuNavItem::SALON_INFORMATIONS,
                'professionnal-salon/manage-images' => SubmenuNavItem::SALON_MANAGE_PICTURES,
                'professionnal-availabilities/edit' => SubmenuNavItem::AVAILABILITIES,
                'professionnal-availabilities/exception-list' => SubmenuNavItem::EXCEPTIONS,
                'professionnal-financial/list-transaction' => SubmenuNavItem::TRANSACTION_LIST,
                'professionnal-financial/list-transfer-request' => SubmenuNavItem::TRANSFER_REQUEST_LIST,
                'professionnal-salon/create' => SubmenuNavItem::SALON_CREATE,
            ];
            
            $routeName = $event->getRouteMatch()->getMatchedRouteName();
            if (strpos($routeName, '/') !== false) {
                $routeName = strstr($routeName, '/', true);
            }

            if (isset($navItemMap[$routeName]) === true) {
                $layoutViewModel->setVariable(
                    'selectedHeaderNavItem',
                    $navItemMap[$routeName]
                );
            }
            
            if (isset($subnavItemMap[$event->getRouteMatch()->getMatchedRouteName()]) === true) {
                $layoutViewModel->setVariable(
                    'selectedSubmenuNavItem',
                    $subnavItemMap[$event->getRouteMatch()->getMatchedRouteName()]
                );
            }
        }
    }
    
    private function initializeAdministrationLayout(
        MvcEvent $event,
        Container $sessionContainer = null
    ) {
        // Récupération des permissions de l'utilisateur
        // Récupération du service d'autorisation
        /* @var $authorizationService AuthorizationService */
        $authorizationService = $this->serviceManager->get(
            'Application\Service\Authorization'
        );
        
        // Récupération des permissions de l'utilisateur connecté
        if (isset($sessionContainer->account)) {
            $accountPermissionList = $authorizationService->getAccountPermissions($sessionContainer->account);
        }
        else {
            $accountPermissionList = [];
        }
        
        // Configure le titre par défaut des pages
        $this->viewHelperManager
            ->get('headTitle')
            ->prepend('Hairlov Admin')
            ->setSeparator(' - ')
            ->setAutoEscape(false);
        
        // Configure les feuilles de styles et autres ressources
        $this->viewHelperManager
            ->get('headLink')
            ->appendStylesheet("https://fonts.googleapis.com/css?family=Open+Sans:400,300,700")
            ->appendStylesheet("https://fonts.googleapis.com/icon?family=Material+Icons")
            ->appendStylesheet("/css/vendor.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/components.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/hairlov.css?v=$this->applicationVersion")
            ->appendStylesheet("/css/hairlov-administration.css?v=$this->applicationVersion")
        ;
        
        // Configure les scripts
        $this->viewHelperManager
            ->get('headScript')
            ->appendFile("/js/vendor.js?v=$this->applicationVersion")
            ->appendFile("/js/hairlov.js?v=$this->applicationVersion")
            ->appendFile("/js/hairlov-administration.js?v=$this->applicationVersion")
        ;
        
        // Défini le layout par défaut à utiliser pour le module
        $layoutViewModel = $event->getViewModel();
        $layoutViewModel->setTemplate('layout/administration');
        $layoutViewModel->setVariable('isLoggedIn', isset($sessionContainer->account));
        $layoutViewModel->setVariable('accountPermissionList', $accountPermissionList);
        
        // Sélectionne l'item dans le menu de navigation de la sidebar en
        // fonction de la route
        if ($event->getRouteMatch() instanceof RouteMatch) {
            $navItemMap = [
                'administration-salon' => AdminHeaderNavItem::SALON,
                'administration-account' => AdminHeaderNavItem::ACCOUNT,
                'administration-prospect' => AdminHeaderNavItem::PROSPECT,
            ];
            
            $routeName = $event->getRouteMatch()->getMatchedRouteName();
            if (strpos($routeName, '/') !== false) {
                $routeName = strstr($routeName, '/', true);
            }

            if (isset($navItemMap[$routeName]) === true) {
                $layoutViewModel->setVariable(
                    'selectedHeaderNavItem',
                    $navItemMap[$routeName]
                );
            }
        }
    }
}
