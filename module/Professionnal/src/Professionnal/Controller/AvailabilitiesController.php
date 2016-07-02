<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */


namespace Professionnal\Controller;

use Application\Exception\ServiceException;
use Backend\Entity\AvailabilityException;
use Backend\Service\AvailabilityService;
use Exception;
use Professionnal\Form\CreateAvailabilityExceptionForm;
use Professionnal\Form\EditAvailabilitiesForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AvailabilitiesController extends AbstractActionController
{ 
    public function editAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
            
        // Instancie le formulaire de définition des disponibilités
        /* @var $editAvailabilitiesForm EditAvailabilitiesForm */
        $editAvailabilitiesForm = $this->getServiceLocator()
            ->get('formElementManager')
            ->get('Professionnal\Form\EditAvailabilities');
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('editAvailabilitiesForm', $editAvailabilitiesForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $editAvailabilitiesForm->setData($this->params()->fromPost());
            
            // Instancie le service Account
            /* @var $availabilityService AvailabilityService */
            $availabilityService = $this->getServiceLocator()->get('Backend\Service\Availability');
            
            // Vérifie la validité du formulaire
            if ($editAvailabilitiesForm->isValid() === false) {
                return $viewModel;
            }
            
            try {
                $availabilityService->editAvailablilities(
                    $editAvailabilitiesForm->getData(),
                    (int) $sessionContainer->account->getIdAccount()
                );
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "Les disponibilités ont bien été mises à jour"
                ));
                
                return $this->redirect()->toRoute('professionnal-availabilities/edit');
            }
            catch (Exception $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue, veuillez réessayer"
                ));
            }
        }
        
        return $viewModel;
    }
    
    public function createAvailabilityAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de création d'une disponibilité
        /* @var $createAvailabilityExceptionForm CreateAvailabilityExceptionForm */
        $createAvailabilityExceptionForm = new CreateAvailabilityExceptionForm();
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('createAvailabilityExceptionForm', $createAvailabilityExceptionForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $createAvailabilityExceptionForm->setData($this->params()->fromPost());
            
            // Instancie le service Account
            /* @var $availabilityService AvailabilityService */
            $availabilityService = $this->getServiceLocator()->get('Backend\Service\Availability');
            
            // Vérifie la validité du formulaire
            if ($createAvailabilityExceptionForm->isValid() === false) {
                return $viewModel;
            }
            
            try {
                $availabilityService->createAvailabilityException(
                    [
                        'start-time' => $this->params()->fromPost('start-time'),
                        'end-time' => $this->params()->fromPost('end-time'),
                        'start-day' => $this->params()->fromPost('start-day'),
                        'end-day' => $this->params()->fromPost('end-day'),
                        'start-month' => $this->params()->fromPost('start-month'),
                        'end-month' => $this->params()->fromPost('end-month'),
                        'start-year' => $this->params()->fromPost('start-year'),
                        'end-year' => $this->params()->fromPost('end-year'),
                        'is-availability' => true,
                        'details' => $this->params()->fromPost('details'),
                    ],
                    (int) $sessionContainer->account->getIdAccount()
                );
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "La disponibilité a bien été créée"
                ));
                
                return $this->redirect()->toRoute('professionnal-availabilities/exception-list');
            }
            catch (Exception $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue, veuillez réessayer"
                ));
            }
        }
        
        return $viewModel;
    }
    
    public function createAbsenceAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de création d'une absence
        /* @var $createAvailabilityExceptionForm CreateAvailabilityExceptionForm */
        $createAvailabilityExceptionForm = new CreateAvailabilityExceptionForm();
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('createAvailabilityExceptionForm', $createAvailabilityExceptionForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $createAvailabilityExceptionForm->setData($this->params()->fromPost());
            
            // Instancie le service Account
            /* @var $availabilityService AvailabilityService */
            $availabilityService = $this->getServiceLocator()->get('Backend\Service\Availability');
            
            // Vérifie la validité du formulaire
            if ($createAvailabilityExceptionForm->isValid() === false) {
                return $viewModel;
            }
            
            try {
                $availabilityService->createAvailabilityException(
                    [
                        'start-time' => $this->params()->fromPost('start-time'),
                        'end-time' => $this->params()->fromPost('end-time'),
                        'start-day' => $this->params()->fromPost('start-day'),
                        'end-day' => $this->params()->fromPost('end-day'),
                        'start-month' => $this->params()->fromPost('start-month'),
                        'end-month' => $this->params()->fromPost('end-month'),
                        'start-year' => $this->params()->fromPost('start-year'),
                        'end-year' => $this->params()->fromPost('end-year'),
                        'is-availability' => false,
                        'details' => $this->params()->fromPost('details'),
                    ],
                    (int) $sessionContainer->account->getIdAccount()
                );
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "L'absence a bien été créée"
                ));
                
                return $this->redirect()->toRoute('professionnal-availabilities/exception-list');
            }
            catch (Exception $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue, veuillez réessayer"
                ));
            }
        }
        
        return $viewModel;
    }
    
    public function exceptionListAction()
    {        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Availability
        /* @var $availabilityService AvailabilityService */
        $availabilityService = $this->getServiceLocator()
            ->get('Backend\Service\Availability');
                
        $availabilityList = $availabilityService
            ->findAvailabilityExceptionByAccountId($sessionContainer->account->getIdAccount());
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('availabilityList', $availabilityList);
        
        return $viewModel;
    }
    
    public function deleteExceptionAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idAvailabilityException') === null) {
            $this->redirect()->toRoute('professionnal-availabilities/exception-list');
        }
        
        $idAvailabilityException = $this->params()->fromRoute('idAvailabilityException');
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Availability
        /* @var $availabilityService AvailabilityService */
        $availabilityService = $this->getServiceLocator()
            ->get('Backend\Service\Availability');
        
        // Vérifie si l'utilisateur à accès à la prestation
        /* @var $availabilityException AvailabilityException */
        $availabilityException = $availabilityService
            ->findAvailabilityExceptionByAvailabilityExceptionId($idAvailabilityException);
        
        if (is_a($availabilityException, 'Backend\Entity\AvailabilityException')
            && $availabilityException->getIdAccount() == $sessionContainer->account->getIdAccount()
        ) {
            $isAllowed = true;
        }
        else {
            $isAllowed = false;
        }

        // Si l'utilisateur n'a pas le droit de supprimer cette exception
        if ($isAllowed === false) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas supprimer cette exception"
            ));
            
            return $this->redirect()->toRoute('professionnal-availabilities/exception-list');
        }
        
        return $this->deleteConfirmForm($idAvailabilityException);
    }

    public function deleteConfirmForm($idAvailabilityException)
    {        
        // Configuration de la vue
        $viewModel = new ViewModel();
        $viewModel->setTemplate('professionnal/availabilities/delete-exception');
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            try {
                // Instancie le service Availability
                /* @var $availabilityService AvailabilityService */
                $availabilityService = $this->getServiceLocator()
                    ->get('Backend\Service\Availability');
                
                $availabilityService->deleteException($idAvailabilityException);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "L'exception a été supprimée avec succès"
                ));
            }
            catch (ServiceException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue lors de la suppression"
                ));
            }

            return $this->redirect()->toRoute('professionnal-availabilities/exception-list');
        }
        
        return $viewModel;
    }
}