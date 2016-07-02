<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Application\Exception\BadCredentialsException;
use Application\Exception\EmailIsAlreadyUsedException;
use Application\Exception\PhoneHasWrongFormatException;
use Application\Exception\ServiceException;
use Application\Form\FacebookLogInForm;
use Application\Form\LogInForm;
use Application\Form\NewPasswordForm;
use Application\Form\PasswordLostForm;
use Application\Form\ProfessionnalSignUpForm;
use Application\Form\SetLocationForm;
use Application\Form\SubscribeNewsletterForm;
use Backend\Entity\Account;
use Backend\Entity\AccountType;
use Backend\Service\AccountService;
use Backend\Service\BookingService;
use Backend\Service\FacebookAccountService;
use Backend\Service\FashionImageService;
use Backend\Service\NewsletterService;
use Backend\Service\PermissionService;
use stdClass;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        // Création de la vue
        $viewModel = new ViewModel();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        /* @var $serviceManager ServiceManager */ 
        $serviceManager = $this->getServiceLocator();
        $config = $serviceManager->get('config');
        
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
            $viewModel->setVariable('facebookApi', $config['facebook']);
            
            $this->layout()->setVariable('landingPage', 'customer');
            $this->layout()->setVariable('hideHeader', true);
            $this->layout()->setVariable('hideFooter', true);
            
            $viewModel->setTemplate('application/index/customer-landing-page');
            
            return $viewModel;
        }
        
        $subscribeNewsletterForm = new SubscribeNewsletterForm();
        $viewModel->setVariable('subscribeNewsletterForm', $subscribeNewsletterForm);
        $setLocationForm = new SetLocationForm();
        $viewModel->setVariable('setLocationForm', $setLocationForm);
        
        // Récupération de la configuration de l'API Google Maps
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
            ->get('Backend\Service\Account');
        
        if ($this->getRequest()->isPost() === true) {
            // Si c'est le formulaire de newsletter qui a été envoyé
            $emailField = $this->params()->fromPost('email', null);
            if ($emailField !== null) {
                // Vérifie la validité des données
                $subscribeNewsletterForm->setData($this->params()->fromPost());
                if ($subscribeNewsletterForm->isValid() === false) {
                    $this->flashMessenger()->addErrorMessage(sprintf(
                        "Une erreur est survenue lors de l'inscription à la newsletter."
                    ));
                }
                else {
                    // Tente d'inscrire une adresse email à la newsletter
                    try {

                        /* @var $newsletterService NewsletterService */
                        $newsletterService = $this->getServiceLocator()
                            ->get('Backend\Service\Newsletter');

                        $newsletterService->add($this->params()->fromPost('email'));

                        $this->flashMessenger()->addSuccessMessage(sprintf(
                            "Vous vous êtes inscrit à la newsletter avec succès."
                        ));
                    }
                    catch (ServiceException $exception) {
                        $this->flashMessenger()->addErrorMessage(sprintf(
                            "Une erreur est survenue lors de l'inscription à la newsletter."
                        ));
                    }
                }
            }
            else {
                $setLocationForm->setData($this->params()->fromPost());
                if ($setLocationForm->isValid() !== false) {
                    // Tente de mettre à jour la localisation de l'utilisateur
                    try {

                        $accountService->editLocation(
                            $this->params()->fromPost(),
                            $sessionContainer->account->getIdAccount()
                        );
                    }
                    catch (ServiceException $exception) {
                        $this->flashMessenger()->addErrorMessage(sprintf(
                            "Une erreur est survenue lors de la mise à jour de votre localisation."
                        ));
                    }
                }
            }
        }
        
        // Récupération des professionnels 
        $likedProfessionals = $accountService
            ->findProfessionalLikedByCustomerId($sessionContainer->account->getIdAccount());
        
        $viewModel->setVariable('likedProfessionals', $likedProfessionals);
        
        /* @var $bookingService BookingService */
        $bookingService = $this->getServiceLocator()
            ->get('Backend\Service\Booking');
        
        // Récupération de la prochaine réservation
        $nextBooking = $bookingService
            ->findNextBookingByCustomerId($sessionContainer->account->getIdAccount());
        
        $viewModel->setVariable('nextBooking', $nextBooking);
        
        // Récupération des meilleurs coiffeurs
        $bestProfessionalList = $accountService
            ->findBestProfessionalByCustomerId($sessionContainer->account->getIdAccount());
        
        $viewModel->setVariable('bestProfessionalList', $bestProfessionalList);
        
        /* @var $fashionImageService FashionImageService */
        $fashionImageService = $this->getServiceLocator()
            ->get('Backend\Service\FashionImage');
        
        // Récupération des images de mode
        $fashionImages = $fashionImageService->findLastWeeks(2);
        
        $viewModel->setVariable('fashionImages', $fashionImages);
        
        // Récupération des infos de l'utilisateur
        if (is_a($sessionContainer->account, 'Backend\Entity\Account')) {
            $customer = $accountService->findByIdAccount($sessionContainer->account->getIdAccount());
        }
        else {
            $customer = new Account();
        }
        
        $viewModel->setVariable('customer', $customer);
        
        // Récupération de la configuration de l'API Google Maps
        $config = $serviceManager->get('config');
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        return $viewModel;
    }
    
    public function passwordLostAction()
    {
        $viewModel = new ViewModel();
        $this->layout()->setVariable('landingPage', 'customer');
        $this->layout()->setVariable('hideHeader', true);
        $this->layout()->setVariable('hideFooter', true);
        
        $passwordLostForm = new PasswordLostForm();
        
        $viewModel->setVariable('passwordLostForm', $passwordLostForm);
        
        if ($this->getRequest()->isPost() === true) {            
            // Vérifie la validité des données
            $passwordLostForm->setData($this->params()->fromPost());
            if ($passwordLostForm->isValid() === false) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la récupération du mot de passe."
                ));
            }
            else {
                // Tente d'inscrire une adresse email à la newsletter
                try {

                    /* @var $accountService AccountService */
                    $accountService = $this->getServiceLocator()
                        ->get('Backend\Service\Account');

                    $accountService->passwordLost($this->params()->fromPost('email'));

                    $this->flashMessenger()->addSuccessMessage(sprintf(
                        "Un email de récupération vient de vous être envoyé."
                    ));
                }
                catch (ServiceException $exception) {
                    $this->flashMessenger()->addErrorMessage(sprintf(
                        "Une erreur est survenue lors de la récupération du mot de passe."
                    ));
                }
            }
        }
        
        return $viewModel;
    }
    
    public function newPasswordAction()
    {
        $viewModel = new ViewModel();
        $this->layout()->setVariable('landingPage', 'customer');
        $this->layout()->setVariable('hideHeader', true);
        $this->layout()->setVariable('hideFooter', true);
        
        $newPasswordForm = new NewPasswordForm();
        
        $viewModel->setVariable('newPasswordForm', $newPasswordForm);
        
        if ($this->getRequest()->isPost() === true) {
            $params = $this->params()->fromPost();
            $params['hash'] = $this->params($this->params()->fromQuery('h'));
            
            // Vérifie la validité des données
            $newPasswordForm->setData($this->params()->fromPost());
            if ($newPasswordForm->isValid() === false) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la création du mot de passe."
                ));
            }
            else {
                // Tente d'inscrire une adresse email à la newsletter
                try {

                    /* @var $accountService AccountService */
                    $accountService = $this->getServiceLocator()
                        ->get('Backend\Service\Account');

                    $accountService->newPassword(
                        $this->params()->fromPost('password'),
                        $this->params()->fromQuery('h')
                    );

                    $this->flashMessenger()->addSuccessMessage(sprintf(
                        "Votre mot de passe a été mis à jour avec succès."
                    ));
                    
                    return $this->redirect()->toRoute('application-home');
                }
                catch (ServiceException $exception) {
                    $this->flashMessenger()->addErrorMessage(sprintf(
                        "Une erreur est survenue lors de la création du mot de passe."
                    ));
                }
            }
        }
        
        return $viewModel;
    }
    
    public function professionalLandingPageAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        $config = $serviceManager->get('config');
        
        // Instanciation du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Si une session existe
        if (isset($sessionContainer->account)) {
            /* @var $permissionService PermissionService */
            $permissionService = $this->getServiceLocator()
                ->get('Backend\Service\Permission');
            
            // Récupération des permissions
            $permissionList = $permissionService
                ->findByIdAccount($sessionContainer->account->getIdAccount());
            
            // Récupération de la route de la homepage
            $redirectRoute = $this->getAccountHomepageByPermissions($permissionList);
            
            return $this->redirect()->toRoute($redirectRoute);
        }
        
        // Instancies les formulaires de connexion et d'inscription pro
        /* @var $logInForm LogInForm */
        $logInForm = $serviceManager->get('FormElementManager')
            ->get('Application\Form\LogInForm');
        /* @var $professionnalSignUpForm ProfessionnalSignUpForm */
        $professionnalSignUpForm = $serviceManager
            ->get('Application\Form\ProfessionnalSignUpFormFactory')
            ->createProfessionalSignUpForm($this->params()->fromQuery());
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('logInForm', $logInForm);
        $viewModel->setVariable('professionnalSignUpForm', $professionnalSignUpForm);
        
        $this->layout()->setVariable('landingPage', 'professionnal');
        $this->layout()->setVariable('hideHeader', true);
        $this->layout()->setVariable('hideFooter', false);
        
        $viewModel->setVariable('session', $sessionContainer);
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        return $viewModel;
    }
    
    /**
     * [AJAX] Connexion de l'utilisateur
     * @return JsonModel
     */
    public function logInAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        
        // Instancie le formulaire de connexion
        /* @var $logInForm LogInForm */
        $logInForm = $this->getServiceLocator()
            ->get('FormElementManager')
            ->get('Application\Form\LogInForm');
        
        // Refuse les requêtes autres que POST
        if ($this->getRequest()->isPost() !== true) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        // Vérifie la validité des données
        $logInForm->setData($this->params()->fromPost());
        if ($logInForm->isValid() === false) {
            $jsonModel->setVariable('message', "Identifiants incorrects");
            return $jsonModel;
        }

        // Tente de créer la session utilisateur
        try {
            /* @var $accountService AccountService */
            $accountService = $this->getServiceLocator()
                ->get('Backend\Service\Account');
            
            if ($this->params()->fromPost('isSignUp')) {
                $accountService->signUp([
                    'lastName' => null,
                    'firstName' => null,
                    'email' => $this->params()->fromPost('email'),
                    'phone' => null,
                    'password' => $this->params()->fromPost('password'),
                    'accountType' => AccountType::ACCOUNT_TYPE_CUSTOMER,
                    'referral' => null,
                ]);
            }
            
            $accountService->logIn([
                'email' => $this->params()->fromPost('email'),
                'password' => $this->params()->fromPost('password'),
            ]);
            
            // Création du conteneur de session
            $sessionContainer = new Container('hairlov');
            $accountId = $sessionContainer->account->getIdAccount();
            
            $jsonModel->setVariable('success', true);
            
            // Définition de la page d'accueil
            // Instancie le service Permission
            /* @var $permissionService PermissionService */
            $permissionService = $this->getServiceLocator()
                ->get('Backend\Service\Permission');

            $permissionList = $permissionService->findByIdAccount($accountId);
            $redirectRoute = $this->getAccountHomepageByPermissions($permissionList);

            $jsonModel->setVariable('route', $this->url()->fromRoute($redirectRoute));
        }
        catch (BadCredentialsException $exception) {
            $jsonModel->setVariable('message', "Identifiants incorrects");
        }
        catch (EmailIsAlreadyUsedException $exception) {
            $jsonModel->setVariable('message', "L'adresse email est déjà utilisée");
        }
        
        return $jsonModel;
    }
    
    /**
     * [AJAX] Connexion de l'utilisateur via Facebook
     * @return JsonModel
     */
    public function facebookLogInAction()
    {
        $facebookLogInForm = new FacebookLogInForm();
        
        // Refuse les requêtes autres que POST
        if ($this->getRequest()->isPost() !== true) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        // Vérifie la validité des données
        $facebookLogInForm->setData($this->params()->fromPost());
        if ($facebookLogInForm->isValid() === false) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Nous n'avons pas accès à vos informations"
            ));
            
            $this->redirect()->toRoute('application-home');
        }

        // Tente de créer la session utilisateur
        try {
            /* @var $facebookAccountService FacebookAccountService */
            $facebookAccountService = $this->getServiceLocator()
                ->get('Backend\Service\FacebookAccount');

            $emailAddress = $facebookAccountService
                ->findEmail($this->params()->fromPost('access_token'));
            $accountImage = $facebookAccountService
                ->findAccountImage($this->params()->fromPost('access_token'));
            
            /* @var $accountService AccountService */
            $accountService = $this->getServiceLocator()
                ->get('Backend\Service\Account');

            $accountService->logInFromFacebook(
                $emailAddress, 
                $this->params()->fromPost('access_token')
            );

            // Instanciation du conteneur de session
            $sessionContainer = new Container('hairlov');
            
            $accountService
                ->addFacebookAccountImage($sessionContainer->account->getIdAccount(), $accountImage);
            
            $this->redirect()->toRoute('application-home');
        }
        catch (BadCredentialsException $exception) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Nous n'avons pas accès à vos informations"
            ));
            
            $this->redirect()->toRoute('application-home');
        }
        catch (ServiceException $exception) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Nous n'avons pas accès à vos informations.<br>
                Vous devez autoriser l'application à accéder à votre adresse email. <br>
                Pour se faire, <a target=\"_blank\" href=\"https://www.facebook.com/settings?tab=applications\" class=\"alert-link\">cliquez ici</a>."
            ));
            
            $this->redirect()->toRoute('application-home');
        }
    }
    
    /**
     * Déconnexion de l'utilisateur
     * @return Response
     */
    public function logOutAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Si l'utilisateur est connecté
        if (isset($sessionContainer->account)) {
            // Instancie le service Account
            /* @var $serviceManager ServiceManager */
            $serviceManager = $this->getServiceLocator();
            /* @var $accountService AccountService */
            $accountService = $serviceManager->get('Backend\Service\Account');
            
            // Déconnecte l'utilisateur
            $accountService->logOut();
        }
        
        return $this->redirect()->toRoute('application-home');
    }
    
    /**
     * [AJAX] Page d'inscription pro
     * @return JsonModel
     */
    public function professionnalSignUpAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        
        // Refuse les requêtes autres que POST
        if ($this->getRequest()->isPost() !== true) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le formulaire de connexion
        /* @var $professionnalSignUpForm ProfessionnalSignUpForm */
        $professionnalSignUpForm = $serviceManager->get('ServiceManager')
            ->get('Application\Form\ProfessionnalSignUpFormFactory')
            ->createProfessionalSignUpForm();
        
        // Vérifie la validité des données
        $professionnalSignUpForm->setData($this->params()->fromPost());
        if ($professionnalSignUpForm->isValid() === false) {
            $jsonModel->setVariable('message', "Veuillez compléter correctement les informations suivantes.");
            $jsonModel->setVariable('errors', $professionnalSignUpForm->getMessages());
            return $jsonModel;
        }

        // Tente de créer le compte utilisateur
        try {
            /* @var $accountService AccountService */
            $accountService = $serviceManager->get('Backend\Service\Account');
            $account = $accountService->signUp([
                'lastName' => $this->params()->fromPost('last-name'),
                'firstName' => $this->params()->fromPost('first-name'),
                'email' => $this->params()->fromPost('email'),
                'phone' => $this->params()->fromPost('phone'),
                'password' => $this->params()->fromPost('password'),
                'accountType' => $this->params()->fromPost('account-type'),
                'address' => $this->params()->fromPost('address', null),
                'zipcode' => $this->params()->fromPost('zipcode', null),
                'city' => $this->params()->fromPost('city', null),
                'latitude' => $this->params()->fromPost('latitude', null),
                'longitude' => $this->params()->fromPost('longitude', null),
            ]);
            
            $jsonModel->setVariable('success', true);
            
            // Définition de la page d'accueil
            // Instancie le service Permission
            /* @var $permissionService PermissionService */
            $permissionService = $this->getServiceLocator()
                ->get('Backend\Service\Permission');

            $permissionList = $permissionService->findByIdAccount($account->getIdAccount());
            $redirectRoute = $this->getAccountHomepageByPermissions($permissionList);

            $jsonModel->setVariable('route', $this->url()->fromRoute($redirectRoute));
        }
        catch (EmailIsAlreadyUsedException $exception) {
            $error = new stdClass();
            $error->email = new stdClass();
            $error->email->isWrong = 'Cette adresse e-mail est déjà utilisée';
            
            $jsonModel->setVariable('message', "Cette adresse e-mail est déjà utilisée");
            $jsonModel->setVariable('errors', $error);
        }
        catch (PhoneHasWrongFormatException $exception) {
            $error = new stdClass();
            $error->phone = new stdClass();
            $error->phone->isWrong = "Le format du numéro de téléphone n'est pas correct";
            
            $jsonModel->setVariable('message', "Le format du numéro de téléphone n'est pas correct");
            $jsonModel->setVariable('errors', $error);
        }
        
        return $jsonModel;
    }
    
    public function loadMoreFashionImagesAction()
    {
        // Refuse les requêtes autres que POST
        if ($this->getRequest()->isPost() !== true) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        $year = $this->params()->fromPost('year');
        $week = $this->params()->fromPost('week');
        $page = $this->params()->fromPost('page');
        
        /* @var $fashionImageService FashionImageService */
        $fashionImageService = $this->getServiceLocator()
            ->get('Backend\Service\FashionImage');
        
        // Récupération des images de mode
        $fashionImages = $fashionImageService->loadMoreFashionImages(
            $week,
            $year,
            $page
        );
        
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('/application/index/partial/fashion-images-block');
        $viewModel->setVariable('images', $fashionImages);
        
        return $viewModel;
    }
}
