<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Service\SalonImageService;
use Backend\Service\SalonService;
use Exception;
use Professionnal\Form\ProfessionnalCreateSalonForm;
use Professionnal\Form\ProfessionnalEditSalonForm;
use Professionnal\Form\UploadSalonCertificateForm;
use Professionnal\Form\UploadSalonImageForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class SalonController extends AbstractActionController
{    
    public function createSalonAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        // Récupération du salon du manager
        $salon = $salonService->findByManagerIdAccount($sessionContainer->account->getIdAccount());
        
        // Si le manager a déjà un salon, redirection
        if ($salon !== null) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas créer de nouveau salon"
            ));
        
            return $this->redirect()->toRoute('professionnal-dashboard');
        }
        
        // Instancie le formulaire de création de salon
        $professionnalCreateSalonForm = new ProfessionnalCreateSalonForm();
                
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('professionnalCreateSalonForm', $professionnalCreateSalonForm);
        
        // Récupération de la configuration de l'API Google Maps
        $config = $serviceManager->get('config');
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            
            
            $professionnalCreateSalonForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($professionnalCreateSalonForm->isValid() === false) {
                return $viewModel;
            }
            
            $salonData = [
                'idAccount' => $sessionContainer->account->getIdAccount(),
                'name' => $this->params()->fromPost('name', null),
                'address' => $this->params()->fromPost('address', null),
                'zipcode' => $this->params()->fromPost('zipcode', null),
                'city' => $this->params()->fromPost('city', null),
                'latitude' => $this->params()->fromPost('latitude', null),
                'longitude' => $this->params()->fromPost('longitude', null),
            ];

            $salonService->createSalon($salonData);
            
            return $this->redirect()->toRoute('professionnal-salon/edit');
        }
        
        return $viewModel;
    }
    
    public function editSalonAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();

        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        $sessionContainer = new Container('hairlov');
        /* @var $salon \Backend\Entity\Salon */
        $salon = $salonService->findByManagerIdAccount($sessionContainer->account->getIdAccount());

        // Instancie le formulaire d'édition du salon
        /* @var $professionnalEditSalonForm ProfessionnalEditSalonForm */
        $professionnalEditSalonForm = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Professionnal\Form\ProfessionnalEditSalon');
        
        // Instancie le formulaire d'upload du k-bis
        $uploadSalonCertificate = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Professionnal\Form\UploadSalonCertificate');

        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('professionnalEditSalonForm', $professionnalEditSalonForm);
        $viewModel->setVariable('uploadSalonCertificateForm', $uploadSalonCertificate);
        $viewModel->setVariable('salon', $salon);
        
        // Récupération de la configuration de l'API Google Maps
        $config = $serviceManager->get('config');
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {

            $professionnalEditSalonForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($professionnalEditSalonForm->isValid() === false) {
                return $viewModel;
            }
            
            $editedSalonData = [
                'idSalon' => $this->params()->fromPost('idSalon', null),
                'name' => $this->params()->fromPost('name', null),
                'address' => $this->params()->fromPost('address', null),
                'zipcode' => $this->params()->fromPost('zipcode', null),
                'city' => $this->params()->fromPost('city', null),
                'latitude' => $this->params()->fromPost('latitude', null),
                'longitude' => $this->params()->fromPost('longitude', null),
            ];

            // Instancie le service Salon
            /* @var $accountService SalonService */
            $salonService = $serviceManager->get('Backend\Service\Salon');

            $salonService->editSalon($editedSalonData);
            
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "Le salon a été modifié avec succés"
            ));
        }
        
        return $viewModel;
    }
    
    public function sendCertificateAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        $jsonModel->setVariable('message', "Une erreur inattendue est survenue. Si le problème persiste, veuillez contacter notre service client.");
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le formulaire d'upload du k-bis
        /* @var $uploadSalonCertificate UploadSalonCertificateForm */
        $uploadSalonCertificate = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Professionnal\Form\UploadSalonCertificate');
        
        $uploadSalonCertificate->setData(array_merge_recursive(
            $this->params()->fromPost(),
            $this->params()->fromFiles()
        ));
        
        // Vérifie la validité du formulaire
        if ($uploadSalonCertificate->isValid() === false) {
            $jsonModel->setVariable('errors', $uploadSalonCertificate->getMessages());
            return $jsonModel;
        }
        
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        // Lance l'envoi du kbis
        try {
            $salonService->saveCertificate([
                'idSalon' => $this->params()->fromPost('idSalon', null),
                'certificate' => $this->getRequest()->getFiles('certificate'),
            ]);
            
            $jsonModel->setVariable('success', true);
        }
        catch(Exception $exception) {
            $this->getServiceLocator()
                ->get('Logger\Error')
                ->info($exception->getMessage());
            
            $jsonModel->setVariable('message', "Votre K-bis n'a pas pu être envoyé. Si le problème persiste, veuillez contacter notre service client.");
        }

        return $jsonModel;
    }
    
    public function deleteImageAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('imageId') === null) {
            $this->redirect()->toRoute('professionnal-salon/manage-image');
        }
        
        $imageId = $this->params()->fromRoute('imageId');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        try {
            // Instancie le service SalonImage
            /* @var $salonImageService SalonImageService */
            $salonImageService = $serviceManager->get('Backend\Service\SalonImage');
            
            $salonImageService->deleteImageByImageId($imageId);
            
            $jsonModel->setVariable('success', true);
        }
        catch (ServiceException $exception) {
            $jsonModel->setVariable('message', "Impossible de supprimer l'image.");
        }
        
        return $jsonModel;
    }
    
    public function salonAttachmentRequestListAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        // Récupération de l'id du compte courant
        $managerId = $sessionContainer->account->getIdAccount();
        // Récupération de l'email du compte courant
        $managerEmail = $sessionContainer->account->getEmail();
        // Récupération du salon du compte courant
        $salon = $salonService->findByManagerIdAccount($managerId);
        
        /* @var $accountService Account */
        $accountService = $serviceManager->get('Backend\Service\Account');
        
        // Récupération de la liste des invitations
        $salonInvitationList = $accountService->findAttachmentRequestByManagerInformations([
            'salonId' => $salon->getIdSalon(),
            'managerId' => $managerId,
            'managerEmail' => $managerEmail,
        ]);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('salonInvitationList', $salonInvitationList);
        
        return $viewModel;
    }
    
    public function acceptAttachmentRequestAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        $salon = $serviceManager->get('Backend\Service\Salon')
            ->findByManagerIdAccount($sessionContainer->account->getIdAccount());
        
        $idEmployee = $this->params()->fromRoute('idEmployee');
        
        try {
            // Validation de la demande de rattachement
            $serviceManager->get('Backend\Service\AttachmentRequest')
                ->acceptAttachmentRequest([
                    'employeeId' => $idEmployee,
                    'salonId' => $salon->getIdSalon(),
                ]);
            
            // Si le manager a déjà un salon, redirection
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "La demande de rattachement été acceptée avec succés"
            ));
        } catch (Exception $ex) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Une erreur est survenue, merci de réessayer"
            ));
        }
        
        return $this->redirect()->toRoute('professionnal-salon/salon-attachment-request-list');        
    }
    
    public function refuseAttachmentRequestAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        $idEmployee = $this->params()->fromRoute('idEmployee');
        
        try {
            // Validation de la demande de rattachement
            $serviceManager->get('Backend\Service\AttachmentRequest')
                ->refuseAttachmentRequest([
                    'employeeId' => $idEmployee,
                ]);
            
            // Si le manager a déjà un salon, redirection
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "La demande de rattachement été refusée avec succés"
            ));
        } catch (Exception $ex) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Une erreur est survenue, merci de réessayer"
            ));
        }
        
        return $this->redirect()->toRoute('professionnal-salon/salon-attachment-request-list');        
    }
    
    public function manageImagesAction()
    {        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Récupération du salon du manager
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()->get('Backend\Service\Salon');
        $managerSalon = $salonService
            ->findByManagerIdAccount($sessionContainer->account->getIdAccount());
        
        // Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
            ->get('Backend\Service\SalonImage');
        
        // Instancie le formulaire d'upload d'images
        $uploadSalonImageForm = new UploadSalonImageForm();
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('uploadSalonImageForm', $uploadSalonImageForm);
        $viewModel->setVariable('salonImageList', $salonImageService->findAllBySalonId((int) $managerSalon->getIdSalon()));
        
        return $viewModel;
    }
    
    public function sendImageAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        $jsonModel->setVariable('message', "Une erreur inattendue est survenue. Si le problème persiste, veuillez contacter notre service client.");
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        $salon = $serviceManager->get('Backend\Service\Salon')
            ->findByManagerIdAccount($sessionContainer->account->getIdAccount());
        
        // Instancie le formulaire d'upload d'images
        $uploadSalonImageForm = new UploadSalonImageForm();
        
        $uploadSalonImageForm->setData(array_merge_recursive(
            $this->params()->fromPost(),
            $this->params()->fromFiles()
        ));
        
        // Vérifie la validité du formulaire
        if ($uploadSalonImageForm->isValid() === false) {
            $jsonModel->setVariable('errors', $uploadSalonImageForm->getMessages());
            return $jsonModel;
        }
        
        // Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
            ->get('Backend\Service\SalonImage');
        
        // Lance l'envoi de l'image du salon
        try {
            $salonImage = $salonImageService->saveSalonImage([
                'idSalon' => $salon->getIdSalon(),
                'image' => $this->getRequest()->getFiles('salon-image'),
            ]);
            
            $url = $this->getServiceLocator()->get('ViewHelperManager')->get('url');
            
            $jsonModel->setVariable('salonImageFilename', $salonImage->getFilepath());
            $jsonModel->setVariable('action', $url('professionnal-salon/delete-image', ['imageId' => $salonImage->getIdSalonImage()]));
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
