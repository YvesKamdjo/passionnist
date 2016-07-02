<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Entity\Booking;
use Backend\Entity\JobService;
use Backend\Entity\Payment;
use Backend\Entity\Transaction;
use Backend\Mapper\BookingMapper;
use Backend\Mapper\JobServiceMapper;
use Backend\Mapper\PaymentMapper;
use Backend\Mapper\SalonMapper;
use Backend\Mapper\TransactionMapper;
use Payplug\Payment as PayplugPayment;
use Payplug\Payplug;
use Zend\Log\Logger;
use Zend\Session\Container;

class PaymentService
{
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    
    /**
     * @var PaymentMapper
     */
    private $paymentMapper;
    
    /**
     * @var BookingMapper
     */
    private $bookingMapper;
    
    /**
     * @var TransactionMapper
     */
    private $transactionMapper;
    
    /**
     * @var JobServiceMapper
     */
    private $jobServiceMapper;
    
    /**
     * @var SalonMapper
     */
    private $salonMapper;
    
    /**
     * @var array
     */
    private $applicationConfig;
    
    /**
     * @var array
     */
    private $paymentConfig;
    
    /* @var $logger Logger */
    private $logger;

    public function __construct(
        $invoiceService,
        $paymentMapper,
        $bookingMapper,
        $transactionMapper,
        $jobServiceMapper,
        $salonMapper,
        $paymentConfig,
        $applicationConfig,
        $logger
    ) {
        $this->invoiceService = $invoiceService;
        $this->paymentMapper = $paymentMapper;
        $this->bookingMapper = $bookingMapper;
        $this->transactionMapper = $transactionMapper;
        $this->jobServiceMapper = $jobServiceMapper;
        $this->salonMapper = $salonMapper;
        $this->paymentConfig = $paymentConfig;
        $this->applicationConfig = $applicationConfig;
        $this->logger = $logger;
    }

    /**
     * Crée un paiement
     * 
     * @param array $paymentData
     * ["amount"]
     * ["jobServiceId"]
     * ["customerId"]
     * ["bookingId"]
     * @return string Adresse de la page de paiement
     */
    public function createPayment($paymentData)
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        try {
            $jobService = new JobService();
            $jobService->setIdJobService($paymentData['jobServiceId']);
            $storedJobService = $this->jobServiceMapper->findById($jobService);
            
            $employee = new Account();
            $employee->setIdAccount($storedJobService->getIdProfessional());
            
            $storedSalon = $this->salonMapper->findByEmployeeIdAccount($employee);
            
            $payment = new Payment();
            $payment->setIdCustomer($paymentData['customerId'])
                ->setAmount($paymentData['amount']);
            
            if (is_null($storedSalon)) {
                $payment->setIdFreelance($employee->getIdAccount());
            }
            else {
                $payment->setIdSalon($storedSalon->getIdSalon());
            }
            
            // Création du paiment
            $this->paymentMapper->create($payment);
            
            // Ajout de l'id du paiement dans les metadata du transfert Payplug
            $paymentData['paymentId'] = $payment->getIdPayment();
            
            // Création du transfert Payplug
            Payplug::setSecretKey($this->paymentConfig['securityKey']);
            $payplugPayment = PayplugPayment::create(array(
                'amount' => ($paymentData['amount']) * 100,
                'currency' => 'EUR',
                'save_card' => false,
                'notification_url' => $this->paymentConfig['notificationUrl'],
                'hosted_payment'    => array(
                    'return_url' => $this->paymentConfig['returnUrl'],
                    'cancel_url' => $this->paymentConfig['cancelUrl']
                ),
                'customer' => array(
                    'email' => $paymentData['customerEmail'],
                    'first_name' => ' ',
                    'last_name' => $paymentData['customerName']
                ),
                'metadata' => $paymentData
            ));
            
            // Stockage des infos du paiement en session
            $sessionContainer->paymentId = $paymentData['paymentId'];
            $sessionContainer->bookingId = $paymentData['bookingId'];
        }
        catch (\Exception $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
        return $payplugPayment->hosted_payment->payment_url;
    }
    
    /**
     * Finalise un paiement
     * 
     * @param array $paymentData
     * ["paymentStatus"]
     * ["paymentId"]
     * ["bookingId"]
     * ["bankReturn"]
     * @throws ServiceException
     */
    public function finalizePayment(array $paymentData)
    {
        $payment = new Payment();
        $payment->setIdPayment($paymentData['paymentId']);
        
        try {
            // Récupération du paiement enregistré en base de données
            $storedPayment = $this->paymentMapper->findByPaymentId($payment);
            
            // Application des modifications
            $storedPayment->setStatus($paymentData['paymentStatus']);
            $storedPayment->setBankReturn(json_encode($paymentData['bankReturn']));
            
            // Mise à jour du paiement
            $this->paymentMapper->edit($storedPayment);
            
            if ($paymentData['paymentStatus'] == Payment::PAYMENT_STATUS_SUCCESSED) {
                // Création de la transaction
                $transaction = new Transaction();
                $feesTransaction = new Transaction();
                $booking = new Booking();
                $booking->setIdBooking($paymentData['bookingId']);
                $storedBooking = $this->bookingMapper->findByBookingId($booking);
                $jobService = new JobService();
                $jobService->setIdJobService($storedBooking->getIdJobService());
                $storedJobService = $this->jobServiceMapper->findById($jobService);
                
                $transaction->setAmount($storedPayment->getAmount())
                    ->setIdCreator($storedJobService->getIdJobService())
                    ->setIdFreelance($storedPayment->getIdFreelance())
                    ->setIdSalon($storedPayment->getIdSalon())
                    ->setDescription('Réservation de prestation');
                
                $this->transactionMapper->create($transaction);
                
                $feesTransaction->setAmount($this->applicationConfig['fees'])
                    ->setIdFreelance($storedPayment->getIdFreelance())
                    ->setIdSalon($storedPayment->getIdSalon())
                    ->setDescription('Frais de gestion');
                
                $this->transactionMapper->create($feesTransaction);
                // Création de la facture
                $this->invoiceService->create($paymentData['bookingId']);
            }
            elseif ($paymentData['paymentStatus'] == Payment::PAYMENT_STATUS_FAILED
                || $paymentData['paymentStatus'] == Payment::PAYMENT_STATUS_CANCELED
            ) {
                // Suppression de la réservation
                $booking = new Booking();
                $booking->setIdBooking($paymentData['bookingId']);
                
                $this->bookingMapper->delete($booking);
            }
        }
        catch (\Exception $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    /**
     * Retourne un paiement à partir de son id
     * 
     * @param int $paymentId
     * @return Payment
     */
    public function findByPaymentId($paymentId)
    {
        $payment = new Payment();
        $payment->setIdPayment($paymentId);
        
        try {
            return $this->paymentMapper->findByPaymentId($payment);
        } catch (\Exception $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
        
    }
}
