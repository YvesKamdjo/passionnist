<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form\Factory;

use Application\Form\AddBookingInformationsForm;
use Backend\Entity\Invoice;
use Backend\Service\InvoiceService;
use Zend\Session\Container;

class AddBookingInformationsFormFactory
{    
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    
    public function __construct(InvoiceService $invoiceService) {
        $this->invoiceService = $invoiceService;
    }

    public function AddBookingInformationsForm($expectedDate)
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        $lastInvoice = $this->invoiceService->findLastByAccountId(
            $sessionContainer->account->getIdAccount()
        );
        
        if ($lastInvoice === null) {
            $lastInvoice = new Invoice();
        }
        
        return new AddBookingInformationsForm($lastInvoice, $expectedDate);
    }
}