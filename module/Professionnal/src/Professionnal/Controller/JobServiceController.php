<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Application\Exception\ServiceException;
use Backend\Entity\AccountType;
use Backend\Entity\Salon;
use Backend\Infrastructure\DataTransferObject\CompleteJobService;
use Backend\Service\AccountService;
use Backend\Service\AccountTypeService;
use Backend\Service\JobServiceImageService;
use Backend\Service\JobServiceService;
use Backend\Service\SalonService;
use Professionnal\Exception\SalonJobServiceTemplateDoesntExistsException;
use Professionnal\Form\CreateJobServiceForm;
use Professionnal\Form\Factory\EmployeeEditJobServiceFormFactory;
use Professionnal\Form\Factory\FreelanceCreateJobServiceFormFactory;
use Professionnal\Form\FreelanceCreateJobServiceForm;
use Professionnal\Form\UploadJobServiceImageForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class JobServiceController extends AbstractActionController
{
    public function listAllAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $serviceManager->get('Backend\Service\JobService');
                
        $jobServiceList = $jobServiceService->listAllByIdAccount($sessionContainer->account->getIdAccount());
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        $employeeSalon = $salonService
            ->findByEmployeeIdAccount($sessionContainer->account->getIdAccount());
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $serviceManager->get('Backend\Service\Account');
        
        $account = $accountService
            ->findByIdAccount($sessionContainer->account->getIdAccount());
        
        $isSalonDeactivated = false;
        
        // Si l'utilisateur est employé d'un salon et que le salon est désactivé
        if ($employeeSalon instanceof Salon
            && $employeeSalon->isActive() === false
        ) {
            $isSalonDeactivated = true;
        }
        
        $hasJobServiceWithoutImage = false;
        
        // Si une prestation n'a pas d'image
        foreach ($jobServiceList as $jobService) {
            /* @var $jobService CompleteJobService */
            if ($jobService->jobServiceImagesCount == 0) {
                $hasJobServiceWithoutImage = true;
            }
        }
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariables([
            'jobServiceList' => $jobServiceList,
            'isSalonDeactivated' => $isSalonDeactivated,
            'isAccountDeactivated' => !$account->isActive(),
            'hasJobServiceWithoutImage' => $hasJobServiceWithoutImage,
        ]);
        
        return $viewModel;
    }
    
    public function createAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service AccountType
        /* @var $accountTypeService AccountTypeService */
        $accountTypeService = $serviceManager->get('Backend\Service\AccountType');
        $accountTypeList = $accountTypeService->findAllByIdAccount($sessionContainer->account->getIdAccount());

        $roleList = [];
        // Création de la liste des rôles
        /* @var $accountType AccountType */
        foreach ($accountTypeList as $accountType) {
            $roleList[] = $accountType->getIdAccountType();
        }
        
        // Si le compte utilisateur est un indépendant
        if (
            in_array(AccountType::ACCOUNT_TYPE_FREELANCE, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_EMPLOYEE, $roleList)
        ) {
            return $this->createAsFreelance();
        }
        elseif (in_array(AccountType::ACCOUNT_TYPE_EMPLOYEE, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_FREELANCE, $roleList)
        ) {
            return $this->createAsEmployee();
        }
    }
    
    public function editAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobService') === null) {
            $this->redirect()->toRoute('professionnal-job-service');
        }
        
        $idJobService = $this->params()->fromRoute('idJobService');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $serviceManager->get('Backend\Service\JobService');
        $jobService = $jobServiceService->findById($idJobService);
        
        // Vérifie si l'utilisateur à accès à la prestation
        $isAllowed = $jobServiceService->isAccountGrantedOnJobService(
                $sessionContainer->account->getIdAccount(), 
                $idJobService
            );

        // Si l'utilisateur n'a pas le droit, de modifier cette prestation
        if ($isAllowed === false) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas éditer cette prestation"
            ));
            
            return $this->redirect()->toRoute('professionnal-job-service');
        }
        
        // Instancie le service AccountType
        /* @var $accountTypeService AccountTypeService */
        $accountTypeService = $serviceManager->get('Backend\Service\AccountType');
        $accountTypeList = $accountTypeService->findAllByIdAccount($sessionContainer->account->getIdAccount());

        $roleList = [];
        // Création de la liste des rôles
        /* @var $accountType AccountType */
        foreach ($accountTypeList as $accountType) {
            $roleList[] = $accountType->getIdAccountType();
        }
        
        // Si le compte utilisateur est un indépendant ou que la prestation n'a
        // pas de modèle
        if (
            in_array(AccountType::ACCOUNT_TYPE_FREELANCE, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_EMPLOYEE, $roleList)
            || ($jobService->getIdJobServiceTemplate() === null)
        ) {
            return $this->editAsFreelance();
        }
        elseif (in_array(AccountType::ACCOUNT_TYPE_EMPLOYEE, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_FREELANCE, $roleList)
        ) {
            return $this->editAsEmployee();
        }
    }
    
    public function deleteAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobService') === null) {
            $this->redirect()->toRoute('professionnal-job-service');
        }
        
        $idJobService = $this->params()->fromRoute('idJobService');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $serviceManager->get('Backend\Service\JobService');
        
        // Vérifie si l'utilisateur à accès à la prestation
        $isAllowed = $jobServiceService->isAccountGrantedOnJobService(
                $sessionContainer->account->getIdAccount(), 
                $idJobService
            );

        // Si l'utilisateur n'a pas le droit, de modifier cette prestation
        if ($isAllowed === false) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas supprimer cette prestation"
            ));
            
            return $this->redirect()->toRoute('professionnal-job-service');
        }
        
        return $this->deleteConfirmForm($idJobService);
    }
    
    public function deleteImageAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('imageId') === null) {
            $this->redirect()->toRoute('professionnal-job-service');
        }
        
        $imageId = $this->params()->fromRoute('imageId');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        try {
            // Instancie le service JobServiceImage
            /* @var $jobServiceImageService JobServiceImageService */
            $jobServiceImageService = $serviceManager->get('Backend\Service\JobServiceImage');
            
            $jobServiceImageService->deleteImageByImageId($imageId);
            
            $jsonModel->setVariable('success', true);
        }
        catch (ServiceException $exception) {
            $jsonModel->setVariable('message', "Impossible de supprimer l'image.");
        }
        
        return $jsonModel;
    }

    public function deleteConfirmForm($idJobService)
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Configuration de la vue
        $viewModel = new ViewModel();
        $viewModel->setTemplate('professionnal/job-service/delete');
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            try {
                // Instancie le service JobService
                /* @var $jobServiceService JobServiceService */
                $jobServiceService = $serviceManager->get('Backend\Service\JobService');

                $jobServiceService->delete($idJobService);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "La prestation a été supprimée avec succès"
                ));
            }
            catch (ServiceException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la suppression"
                ));
            }

            return $this->redirect()->toRoute('professionnal-job-service');
        }
        
        return $viewModel;
    }
    
    public function manageImagesAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobService') === null) {
            $this->redirect()->toRoute('professionnal-job-service');
        }
        
        $idJobService = $this->params()->fromRoute('idJobService');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $serviceManager->get('Backend\Service\JobService');
        // Instancie le service JobServiceImage
        /* @var $jobServiceImageService JobServiceImageService */
        $jobServiceImageService = $serviceManager->get('Backend\Service\JobServiceImage');
        
        // Vérifie si l'utilisateur à accès à la prestation
        $isAllowed = $jobServiceService->isAccountGrantedOnJobService(
                $sessionContainer->account->getIdAccount(), 
                $idJobService
            );

        // Si l'utilisateur n'a pas le droit, de modifier cette prestation
        if ($isAllowed === false) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas modifier les images de cette prestation"
            ));
            
            return $this->redirect()->toRoute('professionnal-job-service');
        }
        
        // Instancie le formulaire d'upload d'images
        /* @var $uploadJobServiceImageForm UploadJobServiceImageForm */
        $uploadJobServiceImageForm = $serviceManager->get('ServiceManager')
            ->get('Professionnal\Form\UploadJobServiceImageFormFactory')
            ->createUploadJobServiceImageForm($idJobService);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('uploadJobServiceImageForm', $uploadJobServiceImageForm);
        $viewModel->setVariable('jobServiceImageList', $jobServiceImageService->findAllByIdJobService((int) $idJobService));
        $viewModel->setVariable('idJobService', $idJobService);
        
        return $viewModel;
    }
    
    private function createAsEmployee()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de création d'une prestation
        /* @var $createJobServiceForm CreateJobServiceForm */
        $createJobServiceForm = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Professionnal\Form\EmployeeCreateJobService');
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('createJobServiceForm', $createJobServiceForm);
        $viewModel->setTemplate('professionnal/job-service/create-as-employee');
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $createJobServiceForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($createJobServiceForm->isValid() === false) {
                return $viewModel;
            }
            
            // Instancie le service Salon
            /* @var $salonService SalonService */
            $salonService = $serviceManager->get('Backend\Service\Salon');
            $salon = $salonService->findByEmployeeIdAccount($sessionContainer->account->getIdAccount());
            
            $jobServiceData = [
                'name' => $this->params()->fromPost('name', null),
                'duration' => $this->params()->fromPost('duration', null),
                'idJobServiceTemplate' => $this->params()->fromPost('salon-job-service', null),
                'description' => $this->params()->fromPost('description', null),
                'idSalon' => $salon->getIdSalon(),
                'idProfessional' => $sessionContainer->account->getIdAccount(),
            ];
            
            // Instancie le service JobService
            /* @var $jobServiceService JobServiceService */
            $jobServiceService = $serviceManager->get('Backend\Service\JobService');

            try {
                $jobServiceService->createAsEmployee($jobServiceData);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "La prestation \"%1\$s\" a été créée avec succès",
                    $this->params()->fromPost('name', null)
                ));
                
                return $this->redirect()->toRoute('professionnal-job-service');
            }
            catch (SalonJobServiceTemplateDoesntExistsException $e) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Le modèle de prestation n'existe pas",
                    $this->params()->fromPost('name', null)
                ));
            }
            
            return $this->redirect()->toRoute('professionnal-job-service/create');
        }
        
        return $viewModel;
    }
    
    private function createAsFreelance()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de création d'une prestation
        /* @var $freelanceCreateJobServiceForm FreelanceCreateJobServiceForm */
        $freelanceCreateJobServiceForm = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Professionnal\Form\FreelanceCreateJobService');
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('createJobServiceForm', $freelanceCreateJobServiceForm);
        $viewModel->setTemplate('professionnal/job-service/create-as-freelance');
        
        $config = $serviceManager->get('config');
        $fees = $config['application']['fees'];
        $viewModel->setVariable('fees', $fees);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $freelanceCreateJobServiceForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($freelanceCreateJobServiceForm->isValid() === false) {
                return $viewModel;
            }
            
            $jobServiceData = [
                'name' => $this->params()->fromPost('name', null),
                'price' => $this->params()->fromPost('price', null),
                'duration' => $this->params()->fromPost('duration', null),
                'description' => $this->params()->fromPost('description', null),
                'idProfessional' => $sessionContainer->account->getIdAccount(),
                'jobServiceType' => $this->params()->fromPost('jobServiceType', []),
                'customerCharacteristic' => $this->params()->fromPost('customerCharacteristic', []),
            ];
            
            // Instancie le service JobService
            /* @var $jobServiceService JobServiceService */
            $jobServiceService = $serviceManager->get('Backend\Service\JobService');

            try {
                $jobServiceService->createAsFreelance($jobServiceData);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "La prestation \"%1\$s\" a été créée avec succès",
                    $this->params()->fromPost('name', null)
                ));
                
                return $this->redirect()->toRoute('professionnal-job-service');
            }
            catch (Exception $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue, veuillez réessayer"
                ));
            }
            
            return $this->redirect()->toRoute('professionnal-job-service/create');
        }
        
        return $viewModel;
    }
    
    private function editAsEmployee()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobService') === null) {
            $this->redirect()->toRoute('professionnal-job-service');
        }
        
        $idJobService = $this->params()->fromRoute('idJobService');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de modification d'une prestation
        /* @var $employeeEditJobServiceForm EmployeeEditJobServiceFormFactory */
        $employeeEditJobServiceForm = $serviceManager->get('ServiceManager')
            ->get('Professionnal\Form\EmployeeEditJobServiceFormFactory')
            ->createEmployeeEditJobServiceForm($idJobService);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('employeeEditJobServiceForm', $employeeEditJobServiceForm);
        $viewModel->setTemplate('professionnal/job-service/edit-as-employee');
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $employeeEditJobServiceForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($employeeEditJobServiceForm->isValid() === false) {
                return $viewModel;
            }
                        
            $jobServiceData = [
                'idJobService' => $idJobService,
                'name' => $this->params()->fromPost('name', null),
                'duration' => $this->params()->fromPost('duration', null),
                'idJobServiceTemplate' => $this->params()->fromPost('salon-job-service', null),
                'description' => $this->params()->fromPost('description', null),
                'idProfessional' => $sessionContainer->account->getIdAccount(),
                'idJobService' => $idJobService,
            ];
            
            // Instancie le service JobService
            /* @var $jobServiceService JobServiceService */
            $jobServiceService = $serviceManager->get('Backend\Service\JobService');

            try {
                $jobServiceService->editAsEmployee($jobServiceData);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "La prestation \"%1\$s\" a été modifiée avec succès",
                    $this->params()->fromPost('name', null)
                ));
                
                return $this->redirect()->toRoute('professionnal-job-service');
            }
            catch (SalonJobServiceTemplateDoesntExistsException $e) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Le modèle de prestation n'existe pas",
                    $this->params()->fromPost('name', null)
                ));
            }
            
            return $this->redirect()->toRoute('professionnal-job-service/edit');
        }
        
        return $viewModel;
    }
    
    private function editAsFreelance()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobService') === null) {
            $this->redirect()->toRoute('professionnal-job-service');
        }
        
        $idJobService = $this->params()->fromRoute('idJobService');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le formulaire de modification d'une prestation
        /* @var $freelanceEditJobServiceForm FreelanceCreateJobServiceFormFactory */
        $freelanceEditJobServiceForm = $serviceManager->get('ServiceManager')
            ->get('Professionnal\Form\FreelanceEditJobServiceFormFactory')
            ->createFreelanceEditJobServiceForm($idJobService);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('freelanceEditJobServiceForm', $freelanceEditJobServiceForm);
        $viewModel->setTemplate('professionnal/job-service/edit-as-freelance');
        
        $config = $serviceManager->get('config');
        $fees = $config['application']['fees'];
        $viewModel->setVariable('fees', $fees);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $freelanceEditJobServiceForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($freelanceEditJobServiceForm->isValid() === false) {
                return $viewModel;
            }
            
            $jobServiceData = [
                'name' => $this->params()->fromPost('name', null),
                'price' => $this->params()->fromPost('price', null),
                'duration' => $this->params()->fromPost('duration', null),
                'description' => $this->params()->fromPost('description', null),
                'jobServiceType' => $this->params()->fromPost('jobServiceType', []),
                'customerCharacteristic' => $this->params()->fromPost('customerCharacteristic', []),
                'idJobService' => $idJobService,
            ];
            
            // Instancie le service JobService
            /* @var $jobServiceService JobServiceService */
            $jobServiceService = $serviceManager->get('Backend\Service\JobService');

            try {
                $jobServiceService->editAsFreelance($jobServiceData);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "La prestation \"%1\$s\" a été modifiée avec succès",
                    $this->params()->fromPost('name', null)
                ));
                
                return $this->redirect()->toRoute('professionnal-job-service');
            }
            catch (Exception $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue, veuillez réessayer"
                ));
            }
            
            return $this->redirect()->toRoute('professionnal-job-service/create');
        }
        
        return $viewModel;
    }
    
    public function sendImageAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        $jsonModel->setVariable('message', "Une erreur inattendue est survenue. Si le problème persiste, veuillez contacter notre service client.");
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        $idJobService = $this->params()->fromPost('idJobService');

        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $serviceManager->get('Backend\Service\JobService');
        
        // Vérifie si l'utilisateur à accès à la prestation
        $isAllowed = $jobServiceService->isAccountGrantedOnJobService(
                $sessionContainer->account->getIdAccount(), 
                $idJobService
            );

        // Si l'utilisateur n'a pas le droit, de modifier cette prestation
        if ($isAllowed === false) {
            $jsonModel->setVariable('message', "Vous ne pouvez pas ajouter d'image sur cette prestation.");
            return $jsonModel;
        }
        
        // Instancie le formulaire d'upload d'images
        /* @var $uploadJobServiceImageForm UploadJobServiceImageForm */
        $uploadJobServiceImageForm = $serviceManager->get('ServiceManager')
            ->get('Professionnal\Form\UploadJobServiceImageFormFactory')
            ->createUploadJobServiceImageForm($idJobService);
        
        $uploadJobServiceImageForm->setData(array_merge_recursive(
            $this->params()->fromPost(),
            $this->params()->fromFiles()
        ));
        
        // Vérifie la validité du formulaire
        if ($uploadJobServiceImageForm->isValid() === false) {
            $jsonModel->setVariable('errors', $uploadJobServiceImageForm->getMessages());
            return $jsonModel;
        }
        
        /* @var $jobServiceImageService JobServiceImageService */
        $jobServiceImageService = $serviceManager->get('Backend\Service\JobServiceImage');
        
        // Lance l'envoi de l'image du salon
        try {
            $jobSeviceImage = $jobServiceImageService->saveJobServiceImage([
                'idJobService' => $idJobService,
                'image' => $this->getRequest()->getFiles('job-service-image'),
            ]);
            
            $url = $this->getServiceLocator()->get('ViewHelperManager')->get('url');
            
            $jsonModel->setVariable('action', $url('professionnal-job-service/delete-image', ['imageId' => $jobSeviceImage->getIdJobServiceImage()]));
            $jsonModel->setVariable('jobServiceImageFilename', $jobSeviceImage->getFilepath());
            $jsonModel->setVariable('success', true);
        }
        catch(Exception $exception) {
            $this->getServiceLocator()
                ->get('Logger\Error')
                ->info($exception->getMessage());
            
            $jsonModel->setVariable('message', "Votre image n'a pas pu être envoyé. Si le problème persiste, veuillez contacter notre service client.");
        }

        return $jsonModel;
    }
}
