<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Controller;

use Backend\Entity\Payment;
use Backend\Service\PaymentService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class PaymentController extends AbstractActionController
{
    public function ipnAction()
    {        
        // Récupération du contenu de la réponse
        $bankReturn = json_decode($this->getRequest()->getContent());
        
        // Instancie le service Payment
        /* @var $paymentService PaymentService */
        $paymentService = $this->getServiceLocator()
            ->get('Backend\Service\Payment');
        
        // Définition de l'état du paiement
        $paymentStatus = ($bankReturn->failure == null)? Payment::PAYMENT_STATUS_SUCCESSED: Payment::PAYMENT_STATUS_FAILED;
        $paymentId = $bankReturn->metadata->paymentId;
        $bookingId = $bankReturn->metadata->bookingId;
        
        $paymentService->finalizePayment([
            'paymentStatus' => $paymentStatus,
            'paymentId' => $paymentId,
            'bookingId' => $bookingId,
            'bankReturn' => $bankReturn,
        ]);
        
        return true;
    }
    
    public function returnPaymentAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le service Payment
        /* @var $paymentService PaymentService */
        $paymentService = $this->getServiceLocator()
            ->get('Backend\Service\Payment');
        
        // Récupération du paiement
        $payment = $paymentService->findByPaymentId($sessionContainer->paymentId);
        

        
        if ($payment->getStatus() == Payment::PAYMENT_STATUS_SUCCESSED) {
            $this->flashMessenger()->addSuccessMessage(
                'Votre paiement a bien été pris en compte.'
            );
            unset($sessionContainer->paymentId);
            unset($sessionContainer->bookingId);
        }
        else {
            $this->flashMessenger()->addInfoMessage(
                'La transaction bancaire est en cours. Réactualisez la liste dans quelques secondes.'
            );
        }
        
        
        return $this->redirect()->toRoute('application-booking-list');
    }
    
    public function canceledPaymentAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        if (isset($sessionContainer->paymentId)
            || isset($sessionContainer->bookingId)
        ) {    
            // Instancie le service Payment
            /* @var $paymentService PaymentService */
            $paymentService = $this->getServiceLocator()
                ->get('Backend\Service\Payment');

            $paymentService->finalizePayment([
                'paymentStatus' => Payment::PAYMENT_STATUS_CANCELED,
                'paymentId' => $sessionContainer->paymentId,
                'bookingId' => $sessionContainer->bookingId,
                'bankReturn' => [],
            ]);

            unset($sessionContainer->paymentId);
            unset($sessionContainer->bookingId);
        }
        
        $this->flashMessenger()->addErrorMessage(sprintf(
            "Vous avez annuler votre paiement."
        ));
        
        return $this->redirect()->toRoute('application-home');
    }
}