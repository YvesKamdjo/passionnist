<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Administration\Controller;

use Backend\Entity\Salon;
use Backend\Service\SalonService;
use finfo;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;

class SalonController extends AbstractActionController
{    
    public function indexAction()
    {
        // Récupération du filtre
        $filter = $this->params()->fromQuery('filter', null);
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
                
        // Récupération de tous les salons
        $salonCollection = $salonService->findAll($filter);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('salonCollection', $salonCollection);
        
        return $viewModel;
    }
    
    public function certificateAction()
    {
        $idSalon = $this->params()->fromRoute('idSalon');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        /* @var $salon Salon */
        $salon = $salonService->findById($idSalon);

        if ($salon === null) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Vous ne pouvez pas accéder à ce document"
            ));
            
            return $this->redirect()->toRoute('administration-salon');
        }
        
        // Création du chemin du document
        $certificateFilePath = $salonService->getCertificateStorageDirectory() . '/' . $salon->getCertificateFilename();
        
        // Si le document est vide ou corrompu
        $certificateFileContent = file_get_contents($certificateFilePath);
        if ($certificateFileContent == false) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        // Récupère le mime type du document
        $finfo = new finfo(FILEINFO_MIME);
        $contentType = $finfo->file($certificateFilePath);
        
        // Retourne la réponse HTTP
        $response = $this->getResponse();
        $response->setContent($certificateFileContent);
        $response->getHeaders()->addHeaders(array(
            'Content-Type' => $contentType
        ));
        return $response;
    }
    
    public function activateAction()
    {
        $idSalon = $this->params()->fromRoute('idSalon');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        try {
            // Active le salon
            $salonService->activate($idSalon);
            
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "Le salon a été activé avec succès"
            ));
        }
        catch (Exception $exception) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Une erreur est survenue lors de l'activation du salon"
            ));
        }
        
        return $this->redirect()->toRoute('administration-salon');
    }
    
    public function deactivateAction()
    {
        $idSalon = $this->params()->fromRoute('idSalon');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        
        try {
            // Désactive le salon
            $salonService->deactivate($idSalon);
            
            $this->flashMessenger()->addSuccessMessage(sprintf(
                "Le salon a été désactivé avec succès"
            ));
        }
        catch (Exception $exception) {
            $this->flashMessenger()->addErrorMessage(sprintf(
                "Une erreur est survenue lors de la désactivation du salon"
            ));
        }
        
        return $this->redirect()->toRoute('administration-salon');
    }
}
