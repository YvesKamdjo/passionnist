<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Application\Exception\ServiceException;
use Backend\Collection\ArrayCollection;
use Backend\Service\AccountService;
use Backend\Service\BookingCommentService;
use Backend\Service\DiscountService;
use Backend\Service\JobServiceImageService;
use Backend\Service\JobServiceService;
use Backend\Service\SalonImageService;
use Backend\Service\SalonService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProfessionalController extends AbstractActionController
{
    
    public function professionalPageAction()
    {
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
        ->get('Backend\Service\Account');
        
        // Instancie le service JobService
        /* @var $jobServiceService JobServiceService */
        $jobServiceService = $this->getServiceLocator()
        ->get('Backend\Service\JobService');
        
        // Instancie le service Salon
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()
        ->get('Backend\Service\Salon');
        
        // Instancie le service BookingComment
        /* @var $bookingCommentService BookingCommentService */
        $bookingCommentService = $this->getServiceLocator()
            ->get('Backend\Service\BookingComment');
        
        // Instancie le service JobServiceImage
        /* @var $jobServiceImageService JobServiceImageService */
        $jobServiceImageService = $this->getServiceLocator()
            ->get('Backend\Service\JobServiceImage');
        
        //Instancie le service SalonImage
        /* @var $salonImageService SalonImageService */
        $salonImageService = $this->getServiceLocator()
            ->get('Backend\Service\SalonImage');
        
        // Instancie le service Discount
        /* @var $discountService DiscountService */
        $discountService = $this->getServiceLocator()
            ->get('Backend\Service\Discount');
        
        // Récupère les paramètres depuis la route
        if ($this->params()->fromRoute('idProfessional') === null) {
            $this->redirect()->toRoute('application-search-job-service');
        }
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        $professionalId = (int) $this->params()->fromRoute('idProfessional');
        
        // Récupération des informations du professionnel
        $professional = $accountService->findByIdAccount($professionalId);
        $viewModel->setVariable('professional', $professional);    
        
        // Récupération des prestations du professionnel
        $jobServiceCollection = $jobServiceService->listAllByIdAccount($professionalId);
        $viewModel->setVariable('jobServiceCollection', $jobServiceCollection);
        
        // Récupération du salon du professionnel
        $salon = $salonService->findByEmployeeIdAccount($professionalId);
        $viewModel->setVariable('salon', $salon);
        
        if ($salon != null) {
            $salonImage = $salonImageService->findAllBySalonId($salon->getIdSalon());
        }
        else {
            $salonImage = new ArrayCollection();
        }

        $viewModel->setVariable('salonImage', $salonImage);
        
        // Récupération des lovs du professionnel
        $customerCollection = $accountService->findCustomerWhoLikeByProfessionalId($professionalId);
        $viewModel->setVariable('likeCounter', $customerCollection->count());
        
        // Récupération des images du professionnel
        $jobServiceImagesCollection = $jobServiceImageService->findAllByProfessionalId($professionalId);
        $viewModel->setVariable('jobServiceImagesCollection', $jobServiceImagesCollection);
        
        // Récupération des commentaires du professionnel
        $bookingComments = $bookingCommentService->findByProfessionalId($professionalId);
        $viewModel->setVariable('bookingComments', $bookingComments);
        
        // Récupération du salon de l'utilisateur
        $salon = $salonService->findByEmployeeIdAccount($professionalId);
        
        if ($salon != null) {
            $maxDiscount = $discountService->findMaxDiscountBySalonId($salon->getIdSalon());
        }
        else {
            $maxDiscount = $discountService->findMaxDiscountByFreelanceId($professionalId);
        }
        
        $viewModel->setVariable('maxDiscount', $maxDiscount);
        
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
        $viewModel->setVariable('averageRate', $averageRate);
        
        return $viewModel;
    }
    
    /**
     * [AJAX] Ajoute ou retire un lov
     * @return JsonModel
     */
    public function switchLikeAction()
    {        
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        
        // N'accepte que les requetes POST
        if ($this->getRequest()->isPost() !== true) {
            return $this->getResponse()->setStatusCode(404);
        }
        
        try {
            /* @var $accountService AccountService */
            $accountService = $this->getServiceLocator()
                ->get('Backend\Service\Account');
        
            // Création du conteneur de session
            $sessionContainer = new Container('hairlov');
            $customerId = $sessionContainer->account->getIdAccount();
            $professionalId = $this->params()->fromPost('professionalId');
            
            $isProfessionalLikedByCustomer = $accountService
                ->isProfessionalLikedByCustomer($customerId, $professionalId);
            
            if ($isProfessionalLikedByCustomer === true) {
                $accountService->removeLikeOnProfessional($professionalId, $customerId);
                $jsonModel->setVariable('action', 'remove');
            }
            else {
                $accountService->addLikeOnProfessional($professionalId, $customerId);
                $jsonModel->setVariable('action', 'add');
            }
            
            $jsonModel->setVariable('success', true);
        }
        catch (ServiceException $exception) {}
        
        return $jsonModel;
    }
    
    public function likedProfessionalListAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $this->getServiceLocator()
            ->get('Backend\Service\Account');
        
        if (!isset($sessionContainer->account)) {
            return $this->redirect()->toRoute('application-home');
        }
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        // Récupération des professionnels lovés
        $likedProfessionalList = $accountService
            ->findAllLikedByAccountId($sessionContainer->account->getIdAccount());
        
        // Ajout de la liste des professionnels dans la vue
        $viewModel->setVariable('likedProfessionalList', $likedProfessionalList);
        
        return $viewModel;
    }
}
