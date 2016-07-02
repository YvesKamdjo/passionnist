<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Backend\Service\AccountService;
use Backend\Service\BookingCommentService;
use Backend\Service\JobServiceService;
use Backend\Service\SalonImageService;
use Backend\Service\SalonService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SalonController extends AbstractActionController
{    
    public function indexAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('salonId') === null) {
            $this->redirect()->toRoute('application-search-job-service');
        }
        
        $salonId = (int) $this->params()->fromRoute('salonId');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()
        ->get('Backend\Service\Salon');
        
        //Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
                ->get('Backend\Service\SalonImage');
        
        $storedSalon = $salonService->findById($salonId);
        $salonImage = $salonImageService->findAllBySalonId($salonId);
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('salon', $storedSalon);
        $viewModel->setVariable('salonImage', $salonImage);
        
        return $viewModel;
    }
    
    public function professionalAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('salonId') === null) {
            $this->redirect()->toRoute('application-search-job-service');
        }
        
        $salonId = $this->params()->fromRoute('salonId');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()
        ->get('Backend\Service\Salon');
        
        //Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
                ->get('Backend\Service\SalonImage');
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
        ->get('Backend\Service\Account');
        
        $storedSalon = $salonService->findById($salonId);
        $employees = $accountService->findEmployeeBySalonId($salonId);
        $salonImage = $salonImageService->findAllBySalonId($salonId);
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('salon', $storedSalon);
        $viewModel->setVariable('employees', $employees);
        $viewModel->setVariable('salonImage', $salonImage);
        
        return $viewModel;
    }
    
    public function jobServiceAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('salonId') === null) {
            $this->redirect()->toRoute('application-search-job-service');
        }
        
        $salonId = $this->params()->fromRoute('salonId');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()
        ->get('Backend\Service\Salon');
        
        //Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
                ->get('Backend\Service\SalonImage');
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $this->getServiceLocator()
        ->get('Backend\Service\JobService');
        
        $storedSalon = $salonService->findById($salonId);
        $jobServices = $jobServiceService->findBySalonId($salonId);
        $salonImage = $salonImageService->findAllBySalonId($salonId);
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('salon', $storedSalon);
        $viewModel->setVariable('jobServices', $jobServices);
        $viewModel->setVariable('salonImage', $salonImage);
        
        return $viewModel;
    }
    
    public function informationAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('salonId') === null) {
            $this->redirect()->toRoute('application-search-job-service');
        }
        
        $salonId = $this->params()->fromRoute('salonId');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()
        ->get('Backend\Service\Salon');
        
        //Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
                ->get('Backend\Service\SalonImage');
        
        $storedSalon = $salonService->findById($salonId);
        $salonImage = $salonImageService->findAllBySalonId($salonId);
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('salon', $storedSalon);
        $viewModel->setVariable('salonImage', $salonImage);
        
        return $viewModel;
    }
    
    public function bookingCommentAction()
    {
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('salonId') === null) {
            $this->redirect()->toRoute('application-search-job-service');
        }
        
        $salonId = $this->params()->fromRoute('salonId');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()
        ->get('Backend\Service\Salon');
        
        //Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
                ->get('Backend\Service\SalonImage');
        
        // Instancie le service BookingComment
        /* @var $bookingCommentService BookingCommentService */
        $bookingCommentService = $this->getServiceLocator()
        ->get('Backend\Service\BookingComment');
        
        $storedSalon = $salonService->findById($salonId);
        $bookingComments = $bookingCommentService->findBySalonId($salonId);
        $salonImage = $salonImageService->findAllBySalonId($salonId);
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('salon', $storedSalon);
        $viewModel->setVariable('bookingComments', $bookingComments);
        $viewModel->setVariable('salonImage', $salonImage);
        
        return $viewModel;
    }
}