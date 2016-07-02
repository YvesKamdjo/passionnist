<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Backend\Service\SalonService;
use Backend\Service\TransactionService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class TransactionController extends AbstractActionController
{
    public function listAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Récupération du salon du manager
        /* @var $salonService SalonService */
        $salonService = $this->getServiceLocator()->get('Backend\Service\Salon');
        $managerSalon = $salonService
            ->findByManagerIdAccount($sessionContainer->account->getIdAccount());

        // Récupération des transactions
        /* @var $transactionService TransactionService */
        $transactionService = $this->getServiceLocator()->get('Backend\Service\Transaction');
        
        if (is_null($managerSalon)) {
            $transactionCollection = $transactionService
                ->findByFreelanceId($sessionContainer->account->getIdAccount());
            
            $totalBalance = $transactionService
                ->calculateFreelanceTotalBalance($sessionContainer->account->getIdAccount());
            
            $currentMonthSales = $transactionService
                ->calculateFreelanceCurrentMonthSales($sessionContainer->account->getIdAccount());
            
            $globalSales = $transactionService
                ->calculateFreelanceGlobalSales($sessionContainer->account->getIdAccount());
        }
        else {
            $transactionCollection = $transactionService
                ->findBySalonId($managerSalon->getIdSalon());
            
            $totalBalance = $transactionService
                ->calculateSalonTotalBalance($managerSalon->getIdSalon());
            
            $currentMonthSales = $transactionService
                ->calculateSalonCurrentMonthSales($managerSalon->getIdSalon());
            
            $globalSales = $transactionService
                ->calculateSalonGlobalSales($managerSalon->getIdSalon());
        }

        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        $viewModel->setVariable('transactionCollection', $transactionCollection);
        $viewModel->setVariable('totalBalance', $totalBalance);
        $viewModel->setVariable('currentMonthSales', $currentMonthSales);
        $viewModel->setVariable('globalSales', $globalSales);
        
        return $viewModel;
    }
}
