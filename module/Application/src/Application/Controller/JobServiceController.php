<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Application\Exception\ServiceException;
use Application\Form\AddBookingInformationsForm;
use Application\Form\FacebookLogInForm;
use Application\Form\JobServiceSearchForm;
use Application\Form\LogInForm;
use Application\Form\ProfessionalSearchForm;
use Backend\Collection\ArrayCollection;
use Backend\Entity\Booking;
use Backend\Entity\Discount;
use Backend\Entity\JobService;
use Backend\Entity\Salon;
use Backend\Service\AccountService;
use Backend\Service\AvailabilityService;
use Backend\Service\BookingCommentService;
use Backend\Service\BookingService;
use Backend\Service\CustomerCharacteristicService;
use Backend\Service\DiscountService;
use Backend\Service\JobServiceImageService;
use Backend\Service\JobServiceService;
use Backend\Service\JobServiceTypeService;
use Backend\Service\PaymentService;
use Backend\Service\SalonImageService;
use Backend\Service\SalonService;
use DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class JobServiceController extends AbstractActionController
{   
    
    public function searchAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le formulaire de recherche
        /* @var $jobServiceSearchForm JobServiceSearchForm */
        $jobServiceSearchForm = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Application\Form\JobServiceSearch');
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        /* @var $serviceManager ServiceManager */ 
        $serviceManager = $this->getServiceLocator();
        
        // Si l'utilisateur n'est pas connecté
        if (!isset($sessionContainer->account)) {
            
            // Redirection temporaire
            // @TODO Retirer la redirection
            return $this->redirect()->toRoute('application-professional-landing-page');
            
            // Instancies le formulaire de connexion
            /* @var $logInForm LogInForm */
            $logInForm = $serviceManager->get('FormElementManager')
                ->get('Application\Form\LogInForm');
            
            $facebookLogInForm = new FacebookLogInForm();
            
            $viewModel->setVariable('logInForm', $logInForm);
            $viewModel->setVariable('facebookLogInForm', $facebookLogInForm);

            // Récupération de la configuration de l'API Facebook
            $config = $serviceManager->get('config');
            $viewModel->setVariable('facebookApi', $config['facebook']);
            
            $this->layout()->setVariable('landingPage', 'customer');
        }
        
        $viewModel->setVariable('jobServiceSearchForm', $jobServiceSearchForm);
        
        // Récupération de la configuration de l'API Google Maps
        $config = $serviceManager->get('config');
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isGet() === true) {

            $jobServiceSearchForm->setData($this->params()->fromQuery());
            
            // Vérifie la validité du formulaire
            if ($jobServiceSearchForm->isValid() === false) {
                return $viewModel;
            }
            
            $searchData = [];
            
            if (!empty($this->params()->fromQuery('date'))) {
                $date = \DateTime::createFromFormat('d/m/Y', $this->params()->fromQuery('date', null));
                $searchData['date'] = $this->params()->fromQuery('date_hidden', date('Y-m-d'));
            }
            
            if (!empty($this->params()->fromQuery('minLike'))) {
                $searchData['minLike'] = $this->params()->fromQuery('minLike', null);
            }
            
            if (!empty($this->params()->fromQuery('minRate'))) {
                $searchData['minRate'] = $this->params()->fromQuery('minRate', null);
            }
            
            if (!empty($this->params()->fromQuery('maxPrice'))) {
                $searchData['maxPrice'] = $this->params()->fromQuery('maxPrice', null);
            }
            
            if (!empty($this->params()->fromQuery('idJobServiceType'))) {
                $searchData['idJobServiceType'] = $this->params()->fromQuery('idJobServiceType', null);
            }
            
            if (!empty($this->params()->fromQuery('idCustomerCharacteristic'))) {
                $searchData['idCustomerCharacteristic'] = $this->params()->fromQuery('idCustomerCharacteristic', null);
            }
            
            if (!empty($this->params()->fromQuery('idAccountType'))) {
                $searchData['idAccountType'] = $this->params()->fromQuery('idAccountType', null);
            }
            
            if (!empty($this->params()->fromQuery('latitude'))
                && !empty($this->params()->fromQuery('longitude'))
                && !empty($this->params()->fromQuery('address'))
            ) {
                $searchData['location']['latitude'] = $this->params()->fromQuery('latitude', null);
                $searchData['location']['longitude'] = $this->params()->fromQuery('longitude', null);
                $searchData['location']['address'] = $this->params()->fromQuery('address', null);
            }

            // Instancie le service JobService
            /* @var $jobServiceService JobServiceService */
            $jobServiceService = $this->getServiceLocator()
            ->get('Backend\Service\JobService');

            try {
                // Création du conteneur de session
                $sessionContainer = new Container('hairlov');
                
                if (isset($searchData['location'])
                    && is_array($searchData['location'])
                ) {
                    $jobServiceService->saveSearchLocation($searchData, $sessionContainer->account->getIdAccount());
                }
                $jobServiceList = $jobServiceService->searchJobService($searchData);
            } catch (ServiceException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la recherche."
                ));
                $jobServiceList = new ArrayCollection();
            }
            
            $viewModel->setVariable('jobServiceList', $jobServiceList);
            
        }
        
        return $viewModel;
    }
    
    public function searchProfessionalAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le formulaire de recherche
        /* @var $professionalSearchForm ProfessionalSearchForm */
        $professionalSearchForm = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Application\Form\ProfessionalSearch');
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        /* @var $serviceManager ServiceManager */ 
        $serviceManager = $this->getServiceLocator();
        
        // Si l'utilisateur n'est pas connecté
        if (!isset($sessionContainer->account)) {
            
            // Redirection temporaire
            // @TODO Retirer la redirection
            return $this->redirect()->toRoute('application-professional-landing-page');
            
            // Instancies le formulaire de connexion
            /* @var $logInForm LogInForm */
            $logInForm = $serviceManager->get('FormElementManager')
                ->get('Application\Form\LogInForm');
            
            $facebookLogInForm = new FacebookLogInForm();
            
            $viewModel->setVariable('logInForm', $logInForm);
            $viewModel->setVariable('facebookLogInForm', $facebookLogInForm);

            // Récupération de la configuration de l'API Facebook
            $config = $serviceManager->get('config');
            $viewModel->setVariable('facebookApi', $config['facebook']);
            
            $this->layout()->setVariable('landingPage', 'customer');
        }
        
        $viewModel->setVariable('professionalSearchForm', $professionalSearchForm);
        
        // Récupération de la configuration de l'API Google Maps
        $config = $serviceManager->get('config');
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isGet() === true) {

            $professionalSearchForm->setData($this->params()->fromQuery());
            
            // Vérifie la validité du formulaire
            if ($professionalSearchForm->isValid() === false) {
                return $viewModel;
            }
            
            $searchData = [];
            
            if (!empty($this->params()->fromQuery('date'))) {
                $date = \DateTime::createFromFormat('d/m/Y', $this->params()->fromQuery('date', null));
                $searchData['date'] = $this->params()->fromQuery('date_hidden', date('Y-m-d'));
            }
            
            if (!empty($this->params()->fromQuery('minRate'))) {
                $searchData['minRate'] = $this->params()->fromQuery('minRate', null);
            }
            
            if (!empty($this->params()->fromQuery('idAccountType'))) {
                $searchData['idAccountType'] = $this->params()->fromQuery('idAccountType', null);
            }
            
            if (!empty($this->params()->fromQuery('latitude'))
                && !empty($this->params()->fromQuery('longitude'))
                && !empty($this->params()->fromQuery('address'))
            ) {
                $searchData['location']['latitude'] = $this->params()->fromQuery('latitude', null);
                $searchData['location']['longitude'] = $this->params()->fromQuery('longitude', null);
                $searchData['location']['address'] = $this->params()->fromQuery('address', null);
            }
            
            if (!empty($this->params()->fromQuery('sort'))
                && !empty($this->params()->fromQuery('order'))
            ) {
                $searchData['sort'] = $this->params()->fromQuery('sort', null);
                $searchData['order'] = $this->params()->fromQuery('order', null);
            }

            // Instancie le service JobService
            /* @var $jobServiceService JobServiceService */
            $jobServiceService = $this->getServiceLocator()
            ->get('Backend\Service\JobService');

            try {
                // Création du conteneur de session
                $sessionContainer = new Container('hairlov');
                
                if (isset($searchData['location'])
                    && is_array($searchData['location'])
                ) {
                    $jobServiceService->saveSearchLocation($searchData, $sessionContainer->account->getIdAccount());
                }
                $professionalList = $jobServiceService->searchProfessional($searchData);
            } catch (ServiceException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la recherche."
                ));
                $professionalList = new ArrayCollection();
            }
            
            $viewModel->setVariable('professionalList', $professionalList);
            
        }
        
        return $viewModel;
    }
    
    public function jobServicePageAction()
    {
        // Récupération de la date de réservation
        if (strlen($this->params()->fromPost('expectedDate'))) {
            $bookingDatetimeStart = $this->params()->fromPost('expectedDate');
        }
        else {
            $bookingDatetimeStart = date('Y-m-d');
        }
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $this->getServiceLocator()
            ->get('Backend\Service\JobService');

        // Instancie le service JobServiceType
        /* @var $jobServiceTypeService JobServiceTypeService */
        $jobServiceTypeService = $this->getServiceLocator()
            ->get('Backend\Service\JobServiceType');

        // Instancie le service AccountService
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
            ->get('Backend\Service\Account');
        
        // Instancie le service JobServiceImage
        /* @var $jobServiceImageService JobServiceImageService */
        $jobServiceImageService = $this->getServiceLocator()
            ->get('Backend\Service\JobServiceImage');
        
        // Instancie le service CustomerCharacteristic
        /* @var $customerCharacteristicService CustomerCharacteristicService */
        $customerCharacteristicService = $this->getServiceLocator()
            ->get('Backend\Service\CustomerCharacteristic');
        
        // Instancie le service BookingComment
        /* @var $bookingCommentService BookingCommentService */
        $bookingCommentService = $this->getServiceLocator()
            ->get('Backend\Service\BookingComment');
        
        // Instancie le service Availability
        /* @var $availabilityService AvailabilityService */
        $availabilityService = $this->getServiceLocator()
            ->get('Backend\Service\Availability');
        
        // Instancie le service Discount
        /* @var $discountService DiscountService */
        $discountService = $this->getServiceLocator()
            ->get('Backend\Service\Discount');
        
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()
            ->get('Backend\Service\Salon');
        
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
            ->get('Backend\Service\SalonImage');
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobService') === null) {
            $this->redirect()->toRoute('application-search-job-service');
        }
        
        $idJobService = (int) $this->params()->fromRoute('idJobService');

        // Récupération de la prestation
        /* @var $jobService JobService */
        $jobService = $jobServiceService->findById($idJobService);
        $jobServiceTypeCollection = $jobServiceTypeService->findByIdJobService($jobService);
        $jobServiceImages = $jobServiceImageService->findAllByIdJobService($idJobService);
        $customerCharacteristics = $customerCharacteristicService->findByIdJobService($jobService);
        $bookingComments = $bookingCommentService->findByJobServiceId($idJobService);
        $professional = $accountService->findProfessionalByAccountId($jobService->getIdProfessional());
        
        if ($bookingComments->count() === 0) {
            $averageRate = null;
        }
        else {
            /* Calcul de la note moyenne de la prestation */
            $averageRateSum = 0;
            foreach ($bookingComments as $comment) {
                $averageRateSum += $comment->rate;
            }
            
            $averageRate = round($averageRateSum / $bookingComments->count());
        }
        
        $weekAvailabilities = $availabilityService
            ->findDayAvailabilityByAccountId($jobService->getIdProfessional(), $bookingDatetimeStart);
        
        $dayDiscount = $discountService
            ->findDayDiscount($jobService->getIdProfessional(), $bookingDatetimeStart);
        
        // Vérification de l'activation des réservation        
        $salon = $salonService->findByEmployeeIdAccount($professional->getIdAccount());
        // Si le pro est un freelance
        if ($salon === null) {
            // On considère que son salon est activé
            $salon = new Salon();
            $salon->setIsActive(true);
        }
        
        $salonImage = $salonImageService->findAllBySalonId($salon->getIdSalon());
        $viewModel->setVariable('salonImage', $salonImage);
        
        if ($professional->isActive()
            && $salon->isActive()
            && $jobServiceImages->count()
        ) {
            $isJobServiceActive = true;
        }
        else {
            $isJobServiceActive = false;
        }
        
        // Récupération des lovs du professionnel
        $customerCollection = $accountService
            ->findCustomerWhoLikeByProfessionalId($professional->getIdAccount());
        
        // Création d'une liste des type de prestation
        $jobTypeServiceName = array();
        if($jobServiceTypeCollection->count() > 0) {
            foreach ($jobServiceTypeCollection as $jobTypeService){
                $jobTypeServiceName[] = $jobTypeService->getName();
            }
        }

        $viewModel->setVariable('jobService', $jobService);
        $viewModel->setVariable('professional', $professional);
        $viewModel->setVariable('jobServiceImages', $jobServiceImages);
        $viewModel->setVariable('jobTypeServiceName', implode(', ', $jobTypeServiceName));
        $viewModel->setVariable('customerCharacteristics', $customerCharacteristics);
        $viewModel->setVariable('bookingComments', $bookingComments);
        $viewModel->setVariable('weekAvailabilities', $weekAvailabilities);
        $viewModel->setVariable('dayDiscount', $dayDiscount);
        $viewModel->setVariable('expectedDate', $bookingDatetimeStart);
        $viewModel->setVariable('averageRate', $averageRate);
        $viewModel->setVariable('likeCounter', $customerCollection->count());
        $viewModel->setVariable('salon', $salon);
        $viewModel->setVariable('salonImage', $salonImage);
        $viewModel->setVariable('isActive', $isJobServiceActive);
        
        return $viewModel;
    }
    
    public function JobServiceDateChangedAction()
    {
        if ($this->getRequest()->isPost() !== true) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        $bookingDatetimeStart = DateTime::createFromFormat('Y-m-d', $this->params()->fromPost('expectedDate'));
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $this->getServiceLocator()
            ->get('Backend\Service\JobService');
        
        // Instancie le service JobServiceImage
        /* @var $jobServiceImageService JobServiceImageService */
        $jobServiceImageService = $this->getServiceLocator()
            ->get('Backend\Service\JobServiceImage');
        
        // Instancie le service CustomerCharacteristic
        /* @var $customerCharacteristicService CustomerCharacteristicService */
        $customerCharacteristicService = $this->getServiceLocator()
            ->get('Backend\Service\CustomerCharacteristic');
        
        // Instancie le service BookingComment
        /* @var $bookingCommentService BookingCommentService */
        $bookingCommentService = $this->getServiceLocator()
            ->get('Backend\Service\BookingComment');
        
        // Instancie le service Availability
        /* @var $availabilityService AvailabilityService */
        $availabilityService = $this->getServiceLocator()
            ->get('Backend\Service\Availability');
        
        // Instancie le service Discount
        /* @var $discountService DiscountService */
        $discountService = $this->getServiceLocator()
            ->get('Backend\Service\Discount');
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
       
        
        $idJobService = $this->params()->fromPost('idJobService');
        
        // Récupération de la prestation
        $jobService = $jobServiceService->findById($idJobService);
        $jobServiceImages = $jobServiceImageService->findAllByIdJobService($idJobService);

        
        $weekAvailabilities = $availabilityService
            ->findDayAvailabilityByAccountId($jobService->getIdProfessional(), $bookingDatetimeStart->format('Y-m-d'));
        
        $dayDiscount = $discountService
            ->findDayDiscount($jobService->getIdProfessional(), $bookingDatetimeStart->format('Y-m-d'));
        
        $viewModel->setVariable('jobService', $jobService);
        $viewModel->setVariable('jobServiceImages', $jobServiceImages);
        $viewModel->setVariable('weekAvailabilities', $weekAvailabilities);
        $viewModel->setVariable('dayDiscount', $dayDiscount);
        $viewModel->setVariable('expectedDate', $bookingDatetimeStart);
        
        return $viewModel;
    }
    
    public function bookingAction()
    {
        // Récupération de la date de réservation
        $bookingDatetimeStart = $this->params()->fromPost('expectedDate');
        $idJobService = $this->params()->fromRoute('idJobService');
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancies les formulaires d'ajout des informations de commande
        /* @var $addBookingInformationsForm AddBookingInformationsForm */
        $addBookingInformationsForm = $this->getServiceLocator()
            ->get('Application\Form\AddBookingInformationsFormFactory')
            ->AddBookingInformationsForm($bookingDatetimeStart);
        
        // Refuse les requêtes autres que POST
        if ($this->getRequest()->isPost() !== true) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "La date de réservation demandée n'est pas disponible."
            ));
            return $this->redirect()->toRoute('application-search-job-service');
        }
        
        // Si la date est absente ou si la date est passée
        if ($bookingDatetimeStart === null
            || new DateTime($bookingDatetimeStart) <= new DateTime()
        ) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "La date de réservation demandée n'est pas disponible."
            ));
            return $this->redirect()->toRoute('application-search-job-service');
        }
        
        // Instancie le service Discount
        /* @var $discountService DiscountService */
        $discountService = $this->getServiceLocator()
            ->get('Backend\Service\Discount');
        
        $storedDiscount = $discountService->findBookingDiscount($idJobService, $bookingDatetimeStart);
        if ($storedDiscount == null) {
            $storedDiscount = new Discount();
            $storedDiscount->setRate(0);
        }
            
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $this->getServiceLocator()
            ->get('Backend\Service\JobService');
            
        /* @var $jobService JobService */
        $jobService = $jobServiceService->findById($idJobService);
            
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('addBookingInformationsForm', $addBookingInformationsForm);
        $viewModel->setVariable('discount', $storedDiscount);
        $viewModel->setVariable('bookingDatetimeStart', $bookingDatetimeStart);
        $viewModel->setVariable('jobService', $jobService);
        
        // Vérifie la validité des données
        $addBookingInformationsForm->setData($this->params()->fromPost());
        if ($addBookingInformationsForm->isValid() === false) {
            return $viewModel;
        }
        
        if (is_null($this->params()->fromPost('billingInformations')) === false) {
            // Instancie le service Booking
            /* @var $bookingService BookingService */
            $bookingService = $this->getServiceLocator()
                ->get('Backend\Service\Booking');

            $customerInformations = $this->params()->fromPost('customerInformations');
            $billingInformations = $this->params()->fromPost('billingInformations');
        
            $bookingData = [
                'idJobService' => $idJobService,
                'idCustomer' => $sessionContainer->account->getIdAccount(),
                'start' => $bookingDatetimeStart,
                'duration' => $jobService->getDuration(),
                'jobServiceName' => $jobService->getName(),
                'jobServicePrice' => $jobService->getPrice(),
                'discountRate' => $storedDiscount->getRate(),
                'billingName' => $billingInformations[0]['billingName'],
                'billingAddress' => $billingInformations[0]['billingAddress'],
                'billingZipcode' => $billingInformations[0]['billingZipcode'],
                'billingCity' => $billingInformations[0]['billingCity'],
            ];
            
            if ($this->params()->fromPost('otherAddress') == false) {
                $bookingData['customerName'] = $billingInformations[0]['billingName'];
                $bookingData['customerAddress'] = $billingInformations[0]['billingAddress'];
                $bookingData['customerZipcode'] = $billingInformations[0]['billingZipcode'];
                $bookingData['customerCity'] = $billingInformations[0]['billingCity'];
            }
            else {
                $bookingData['customerName'] = $customerInformations[0]['customerName'];
                $bookingData['customerAddress'] = $customerInformations[0]['customerAddress'];
                $bookingData['customerZipcode'] = $customerInformations[0]['customerZipcode'];
                $bookingData['customerCity'] = $customerInformations[0]['customerCity'];
            }
            
            try {
                /* @var $booking Booking */
                $booking = $bookingService->add($bookingData);
                
                // Instancie le service Payment
                /* @var $paymentService PaymentService */
                $paymentService = $this->getServiceLocator()
                    ->get('Backend\Service\Payment');
                
                // Création de la page de paiement
                $paymentUrl = $paymentService->createPayment([
                    'amount' => ($jobService->getPrice() - ($jobService->getPrice() * ($storedDiscount->getRate()/100))),
                    'jobServiceId' => $jobService->getIdJobService(),
                    'bookingId' => $booking->getIdBooking(),
                    'customerId' => $sessionContainer->account->getIdAccount(),
                    'customerEmail' => $sessionContainer->account->getEmail(),
                    'customerName' => $booking->getCustomerName(),
                ]);
                
                // Redirection vers la page de paiement
                return $this->redirect()->toUrl($paymentUrl);
            }
            catch (ServiceException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la réservation, veuillez réessayer"
                ));
            }
        }
        
        return $viewModel;
    }
}