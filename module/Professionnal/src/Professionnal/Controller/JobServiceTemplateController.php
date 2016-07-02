<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Backend\Service\JobServiceTemplateService;
use Backend\Service\SalonService;
use Exception;
use Professionnal\Form\CreateJobServiceTemplateForm;
use Professionnal\Form\DeleteJobServiceTemplateConfirmForm;
use Professionnal\Form\EditJobServiceTemplateForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class JobServiceTemplateController extends AbstractActionController
{    
    public function listAllAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobServiceTemplate
        /* @var $jobServiceTemplateService JobServiceTemplateService */
        $jobServiceTemplateService = $serviceManager->get('Backend\Service\JobServiceTemplate');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        $salon = $salonService->findByManagerIdAccount($sessionContainer->account->getIdAccount());
                
        $jobServiceTemplateList = $jobServiceTemplateService->listAllByIdSalon($salon->getIdSalon());
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('jobServiceTemplateList', $jobServiceTemplateList);
        
        return $viewModel;
    }
    
    public function createAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de création d'une prestation
        /* @var $createJobServiceTemplateForm CreateJobServiceTemplateForm */
        $createJobServiceTemplateForm = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Professionnal\Form\CreateJobServiceTemplate');
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('createJobServiceTemplateForm', $createJobServiceTemplateForm);
        
        $config = $serviceManager->get('config');
        $fees = $config['application']['fees'];
        $viewModel->setVariable('fees', $fees);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $createJobServiceTemplateForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($createJobServiceTemplateForm->isValid() === false) {
                return $viewModel;
            }
            
            // Instancie le service JobService
            /* @var $salonService SalonService */
            $salonService = $serviceManager->get('Backend\Service\Salon');
            
            $salon = $salonService->findByManagerIdAccount($sessionContainer->account->getIdAccount());
            
            $jobServiceData = [
                'idManager' => $sessionContainer->account->getIdAccount(),
                'idSalon' => $salon->getIdSalon(),
                'name' => $this->params()->fromPost('name', null),
                'price' => $this->params()->fromPost('price', null),
                'jobServiceType' => $this->params()->fromPost('jobServiceType', []),
                'customerCharacteristic' => $this->params()->fromPost('customerCharacteristic', []),
            ];
            
            // Instancie le service JobServiceTemplate
            /* @var $jobServiceTemplateService JobServiceTemplateService */
            $jobServiceTemplateService = $serviceManager->get('Backend\Service\JobServiceTemplate');

            $jobServiceTemplateService->create($jobServiceData);
            
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "Le modèle de prestation \"%1\$s\" a été créée avec succès",
                $this->params()->fromPost('name', null)
            ));
            
            return $this->redirect()->toRoute('professionnal-job-service-template');
        }
        
        return $viewModel;
    }
    
    public function editAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobServiceTemplate') === null) {
            $this->redirect()->toRoute('professionnal-job-service-template');
        }
        
        $idJobServiceTemplate = $this->params()->fromRoute('idJobServiceTemplate');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobServiceTemplate
        /* @var $jobServiceTemplateService JobServiceTemplateService */
        $jobServiceTemplateService = $serviceManager->get('Backend\Service\JobServiceTemplate');
        
        // Vérifie si l'utilisateur à accès au modèle de prestation
        $isAllowed = $jobServiceTemplateService->isAccountGrantedOnJobServiceTemplate(
                $sessionContainer->account->getIdAccount(), 
                $idJobServiceTemplate
            );

        // Si l'utilisateur n'a pas le droit, de modifier cette prestation
        if ($isAllowed === false) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas modifier ce modèle de prestation"
            ));
            
            return $this->redirect()->toRoute('professionnal-job-service-template');
        }
        
        // Instancie le formulaire de modification d'un template de prestation
        /* @var $editJobServiceTemplateForm EditJobServiceTemplateForm */
        $editJobServiceTemplateForm = $serviceManager->get('ServiceManager')
            ->get('Professionnal\Form\EditJobServiceTemplateFormFactory')
            ->createEditJobServiceTemplateForm($idJobServiceTemplate);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('editJobServiceTemplateForm', $editJobServiceTemplateForm);
        
        $config = $serviceManager->get('config');
        $fees = $config['application']['fees'];
        $viewModel->setVariable('fees', $fees);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $editJobServiceTemplateForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($editJobServiceTemplateForm->isValid() === false) {
                return $viewModel;
            }
            
            $synchronizeJobService = (bool) $this->params()->fromPost('synchronize-job-service', null);
            
            $jobServiceData = [
                'idJobServiceTemplate' => $idJobServiceTemplate,
                'name' => $this->params()->fromPost('name', null),
                'price' => $this->params()->fromPost('price', null),
                'jobServiceType' => $this->params()->fromPost('jobServiceType', []),
                'customerCharacteristic' => $this->params()->fromPost('customerCharacteristic', []),
                'synchronizeJobService' => $synchronizeJobService,
            ];
            
            $jobServiceTemplateService->edit($jobServiceData);
            
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "Le modèle de prestation \"%1\$s\" a été modifiée avec succès",
                $this->params()->fromPost('name', null)
            ));
            
            return $this->redirect()->toRoute('professionnal-job-service-template');
        }
        
        return $viewModel;
    }
    
    public function deleteAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idJobServiceTemplate') === null) {
            $this->redirect()->toRoute('professionnal-job-service-template');
        }
        
        $idJobServiceTemplate = $this->params()->fromRoute('idJobServiceTemplate');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service JobServiceTemplate
        /* @var $jobServiceTemplateService JobServiceTemplateService */
        $jobServiceTemplateService = $serviceManager->get('Backend\Service\JobServiceTemplate');
        
        // Vérifie si l'utilisateur à accès au modèle de prestation
        $isAllowed = $jobServiceTemplateService->isAccountGrantedOnJobServiceTemplate(
                $sessionContainer->account->getIdAccount(), 
                $idJobServiceTemplate
            );

        // Si l'utilisateur n'a pas le droit, de modifier cette prestation
        if ($isAllowed === false) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas supprimer ce modèle de prestation"
            ));
            
            return $this->redirect()->toRoute('professionnal-job-service-template');
        }
        
        return $this->deleteConfirmForm($idJobServiceTemplate);
    }
    
    public function deleteConfirmForm($idJobServiceTemplate)
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le formulaire de suppression d'un template de prestation
        $deleteJobServiceTemplateConfirmForm = new DeleteJobServiceTemplateConfirmForm();
     
        // Configuration de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('deleteJobServiceTemplateConfirmForm', $deleteJobServiceTemplateConfirmForm);
        $viewModel->setTemplate('professionnal/job-service-template/delete');
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            try {
                $deleteJobServiceTemplateConfirmForm->setData($this->params()->fromPost());
            
                // Vérifie la validité du formulaire
                if ($deleteJobServiceTemplateConfirmForm->isValid() === false) {
                    return $viewModel;
                }

                $synchronizeJobService = (bool) $this->params()->fromPost('synchronize-job-service', null);

                // Instancie le service JobServiceTemplate
                /* @var $jobServiceTemplateService JobServiceTemplateService */
                $jobServiceTemplateService = $serviceManager->get('Backend\Service\JobServiceTemplate');

                $deleteData = [
                    'synchronizeJobService' => $synchronizeJobService,
                    'idJobServiceTemplate' => $idJobServiceTemplate,
                ];
                
                $jobServiceTemplateService->delete($deleteData);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "Le modèle de prestation a été supprimé avec succès"
                ));
            }
            catch (Exception $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la suppression"
                ));
            }

            return $this->redirect()->toRoute('professionnal-job-service-template');
        }
        
        return $viewModel;
    }
}
