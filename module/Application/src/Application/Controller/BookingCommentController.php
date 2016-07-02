<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Application\Exception\ServiceException;
use Application\Form\CreateBookingCommentForm;
use Backend\Service\BookingCommentService;
use Backend\Service\BookingService;
use Backend\Service\InvoiceService;
use Dompdf\Dompdf;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class BookingCommentController extends AbstractActionController
{
    public function createAction()
    {        
        // Récupération l'id de la réservation
        $bookingId = $this->params()->fromRoute('bookingId');
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Booking
        /* @var $bookingService BookingService */
        $bookingService = $this->getServiceLocator()
            ->get('Backend\Service\Booking');
        
        $checkingData = [
            'bookingId' => $bookingId,
            'customerId' => $sessionContainer->account->getIdAccount(),
        ];

        // Vérifie si l'utilisateur peut commenter la prestation
        if ($bookingService->checkIfAllowedToInteract($checkingData) == true) {
            return $this->createComment();
        }
        else {
            $this->flashMessenger()->addInfoMessage(sprintf(
                "Vous devez attendre que la prestation ai eu lieu pour laisser votre avis."
            ));
            
            $this->redirect()->toRoute('application-booking-list');
        }
        
        return false;
    }
    
    public function generateInvoiceAction()
    {        
        // Récupération l'id de la réservation
        $bookingId = $this->params()->fromRoute('bookingId');
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Booking
        /* @var $bookingService BookingService */
        $bookingService = $this->getServiceLocator()
            ->get('Backend\Service\Booking');
        
        $checkingData = [
            'bookingId' => $bookingId,
            'customerId' => $sessionContainer->account->getIdAccount(),
        ];

        // Vérifie si l'utilisateur peut générer une facture sur la prestation
        if ($bookingService->checkIfAllowedToInteract($checkingData) == true) {
            return $this->generateInvoice();
        }
        else {
            $this->flashMessenger()->addInfoMessage(sprintf(
                "Votre facture sera disponible à la fin de la prestation"
            ));
            
            $this->redirect()->toRoute('application-booking-list');
        }
        
        return false;
    }
    
    private function createComment()
    {
        // Récupération l'id de la réservation
        $bookingId = $this->params()->fromRoute('bookingId');
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Instancie le formulaire de création d'un commentaire
        /* @var $createBookingCommentForm CreateBookingCommentForm */
        $createBookingCommentForm = $this->getServiceLocator()
            ->get('Application\Form\CreateBookingCommentFormFactory')
            ->CreateBookingCommentForm($bookingId);
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('createBookingCommentForm', $createBookingCommentForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $createBookingCommentForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($createBookingCommentForm->isValid() === false) {
                return $viewModel;
            }
                        
            $bookingCommentData = [
                'bookingId' => $bookingId,
                'rate' => $this->params()->fromPost('rate'),
                'comment' => $this->params()->fromPost('comment', null),
            ];
            
            // Instancie le service JobService
            /* @var $bookingCommentService BookingCommentService */
            $bookingCommentService = $serviceManager->get('Backend\Service\BookingComment');

            try {
                $bookingCommentService->add($bookingCommentData);
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "Le commentaire a été créé avec succès"
                ));
                
                return $this->redirect()->toRoute('application-booking-list');
            }
            catch (ServiceException $e) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue"
                ));
            }
        }
        
        return $viewModel;
    }
    
    private function generateInvoice()
    {
        // Récupération l'id de la réservation
        $bookingId = $this->params()->fromRoute('bookingId');
        
        // Instancie le service Invoice
        /* @var $invoiceService InvoiceService */
        $invoiceService = $this->getServiceLocator()
            ->get('Backend\Service\Invoice');
        
        $invoice = $invoiceService->findByBookingId($bookingId);
        
        // Configuration de la vue
        $viewModel = new ViewModel();
        $viewModel->setTemplate('pdf/invoice');
        $viewModel->setVariables([
            'invoice' => $invoice,
        ]);
        
        // Création de l'objet PDF
        $dompdf = new Dompdf();
        
        // Génération du rendu
        $dompdf->loadHtml(
            $this->getServiceLocator()->get('viewrenderer')->render($viewModel)
        );
        $dompdf->render();
        
        // Retour du pdf
        $dompdf->stream('facture', array(
            'Attachment' => true
        ));
    }
}