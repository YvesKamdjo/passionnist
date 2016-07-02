<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Administration\Controller;

use Administration\Form\Factory\EditAccountFormFactory;
use Application\Exception\EmailIsAlreadyUsedException;
use Application\Exception\PhoneHasWrongFormatException;
use Backend\Entity\Account;
use Backend\Entity\AccountType;
use Backend\Service\AccountService;
use Backend\Service\PermissionService;
use Exception;
use finfo;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{    
    public function indexAction()
    {
        // Récupération du filtre
        $filter = $this->params()->fromQuery('filter', null);
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $serviceManager->get('Backend\Service\Account');
        
        switch ($filter) {
            case 'employee':
                $accountCollection = $accountService->findAllByAccountTypeId(AccountType::ACCOUNT_TYPE_EMPLOYEE);
                break;
            case 'manager':
                $accountCollection = $accountService->findAllByAccountTypeId(AccountType::ACCOUNT_TYPE_MANAGER);
                break;
            case 'freelance':
                $accountCollection = $accountService->findAllByAccountTypeId(AccountType::ACCOUNT_TYPE_FREELANCE);
                break;
            case 'customer':
                $accountCollection = $accountService->findAllByAccountTypeId(AccountType::ACCOUNT_TYPE_CUSTOMER);
                break;
            case 'admin':
                $accountCollection = $accountService->findAllByAccountTypeId(AccountType::ACCOUNT_TYPE_ADMIN);
                break;
            default :
                $accountCollection = $accountService->findAll();
                break;
        }
                
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('accountCollection', $accountCollection);
        
        return $viewModel;
    }
    
    public function activateAction()
    {
        $idAccount = $this->params()->fromRoute('idAccount');
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
            ->get('Backend\Service\Account');
        
        try {
            // Active le compte
            $accountService->activate($idAccount);
            
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "Le compte a été activé avec succès"
            ));
        }
        catch (Exception $exception) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Une erreur est survenue lors de l'activation du compte"
            ));
        }
        
        return $this->redirect()->toRoute('administration-account');
    }
    
    public function deactivateAction()
    {
        $idAccount = $this->params()->fromRoute('idAccount');
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
            ->get('Backend\Service\Account');
        
        try {
            // Active le compte
            $accountService->deactivate($idAccount);
            
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "Le compte a été désactivé avec succès"
            ));
        }
        catch (Exception $exception) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Une erreur est survenue lors de la désactivation du compte"
            ));
        }
        
        return $this->redirect()->toRoute('administration-account');
    }
    
    public function qualificationAction()
    {
        $idAccount = $this->params()->fromRoute('idAccount');
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
                            ->get('Backend\Service\Account');
        /* @var $account Account */
        $account = $accountService->findByIdAccount($idAccount);

        if ($account === null) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas accéder à ce document"
            ));
            
            return $this->redirect()->toRoute('administration-account');
        }
        
        // Création du chemin du document
        $qualificationFilePath = $accountService->getQualificationStorageDir() . '/' . $account->getQualificationFilename();
        
        // Si le document est vide ou corrompu
        $qualificationFileContent = file_get_contents($qualificationFilePath);
        if ($qualificationFileContent == false) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        // Récupère le mime type du document
        $finfo = new finfo(FILEINFO_MIME);
        $contentType = $finfo->file($qualificationFilePath);
        
        // Retourne la réponse HTTP
        $response = $this->getResponse();
        $response->setContent($qualificationFileContent);
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => $contentType
        ));
        return $response;
    }
    
    public function takeOverAction()
    {
        $idAccount = $this->params()->fromRoute('idAccount');
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
            ->get('Backend\Service\Account');
        
        // Prise de contrôle
        $accountService->takeOver($idAccount);
        
        // Instancie le service Permission
        /* @var $permissionService PermissionService */
        $permissionService = $this->getServiceLocator()
            ->get('Backend\Service\Permission');
        
        $permissionList = $permissionService->findByIdAccount($idAccount);
        $redirectRoute = $this->getAccountHomepageByPermissions($permissionList);
        
        return $this->redirect()->toRoute($redirectRoute);
    }
    
    public function endTakeOverAction()
    {
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
            ->get('Backend\Service\Account');
        
        // Prise de contrôle
        $accountService->endTakeOver();
        
        return $this->redirect()->toRoute('administration-account');
    }
    
    public function editAction()
    {
        $idAccount = $this->params()->fromRoute('idAccount');
                
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de modification d'une prestation
        /* @var $editAccountForm EditAccountFormFactory */
        $editAccountForm = $this->getServiceLocator()->get('ServiceManager')
            ->get('Administration\Form\EditAccountFormFactory')
            ->createEditAccountForm($idAccount);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('editAccountForm', $editAccountForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $editAccountForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($editAccountForm->isValid() === false) {
                return $viewModel;
            }
            
            $editedProfileData = [
                'idAccount' => $idAccount,
                'lastName' => $this->params()->fromPost('last-name', null),
                'firstName' => $this->params()->fromPost('first-name', null),
                'email' => $this->params()->fromPost('email', null),
                'phone' => $this->params()->fromPost('phone', null),
                'address' => $this->params()->fromPost('address', null),
                'zipcode' => $this->params()->fromPost('zipcode', null),
                'city' => $this->params()->fromPost('city', null),
            ];

            // Instancie le service Account
            /* @var $accountService AccountService */
            $accountService = $this->getServiceLocator()
                ->get('Backend\Service\Account');

            try {
                $accountService->editProfile($editedProfileData);
                
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "Le compte a été mis à jour avec succès"
                ));
                
                return $this->redirect()->toRoute('administration-account');
            }
            catch (EmailIsAlreadyUsedException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "L'email est déjà utilisée"
                ));
            }
            catch (PhoneHasWrongFormatException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Le numéro de téléphone n'est pas au bon format"
                ));
            }
        }
        
        return $viewModel;
    }
}
