<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Backend\Collection\ArrayCollection;
use Backend\Entity\AccountType;
use Backend\Entity\Salon;
use Professionnal\Exception\TransferRequestAmountIsTooHigh;
use Professionnal\Form\CreateTransferRequestForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class TransferRequestController extends AbstractActionController
{
    public function listAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Récupération des rôles de l'utilisateur
        $accountTypeList = $this->getServiceLocator()->get('Backend\Service\AccountType')
            ->findAllByIdAccount($sessionContainer->account->getIdAccount());

        $roleList = [];
        // Création de la liste des rôles
        /* @var $accountType AccountType */
        foreach ($accountTypeList as $accountType) {
            $roleList[] = $accountType->getIdAccountType();
        }

        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        $transferRequestCollection = new ArrayCollection();
        $totalBalance = 0;
        // Si le compte utilisateur est un indépendant
        if (
            in_array(AccountType::ACCOUNT_TYPE_FREELANCE, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_MANAGER,$roleList)
        ) {
            $totalBalance = $this->getServiceLocator()
                ->get('Backend\Service\Transaction')
                ->calculateFreelanceTotalBalance($sessionContainer->account->getIdAccount());
            
            $transferRequestCollection = $this->getServiceLocator()->get('Backend\Service\TransferRequest')
                ->findAllFreelanceTransferRequests($sessionContainer->account->getIdAccount());
            
        } elseif (in_array(AccountType::ACCOUNT_TYPE_MANAGER, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_FREELANCE,$roleList)
        ) {
            // Récupération du salon
            /* @var $salon Salon */
            $salon = $this->getServiceLocator()->get('Backend\Service\Salon')
                ->findByManagerIdAccount($sessionContainer->account->getIdAccount());
            
            $totalBalance = $this->getServiceLocator()
                ->get('Backend\Service\Transaction')
                ->calculateSalonTotalBalance($salon->getIdSalon());
            
            $transferRequestCollection = $this->getServiceLocator()->get('Backend\Service\TransferRequest')
                ->findAllSalonTransferRequests($salon->getIdSalon());
        }
        
        $viewModel->setVariable('transferRequestCollection', $transferRequestCollection);
        $viewModel->setVariable('totalBalance', $totalBalance);
        
        return $viewModel;
    }
    
    public function createAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Récupération des rôles de l'utilisateur
        $accountTypeList = $this->getServiceLocator()->get('Backend\Service\AccountType')
            ->findAllByIdAccount($sessionContainer->account->getIdAccount());
        
        $roleList = [];
        // Création de la liste des rôles
        /* @var $accountType AccountType */
        foreach ($accountTypeList as $accountType) {
            $roleList[] = $accountType->getIdAccountType();
        }

        // Paramètrage de la vue
        $viewModel = new ViewModel();
        
        // Si le compte utilisateur est un indépendant
        if (
            in_array(AccountType::ACCOUNT_TYPE_FREELANCE, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_MANAGER,$roleList)
        ) {
            return $this->createFromFreelance();
        } elseif (in_array(AccountType::ACCOUNT_TYPE_MANAGER, $roleList) 
            && !in_array(AccountType::ACCOUNT_TYPE_FREELANCE,$roleList)
        ) {
            return $this->createFromSalon();
        }
        
        return $viewModel;
    }

    private function createFromSalon()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Instancie le formulaire de création de demande de virement
        $createTransfertRequestForm = new CreateTransferRequestForm();

        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('createTransfertRequestForm',
                                $createTransfertRequestForm);

        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {

            $createTransfertRequestForm->setData($this->params()->fromPost());

            // Vérifie la validité du formulaire
            if ($createTransfertRequestForm->isValid() === false) {
                return $viewModel;
            }

            // Récupération du salon
            /* @var $salon Salon */
            $salon = $this->getServiceLocator()->get('Backend\Service\Salon')
                ->findByManagerIdAccount($sessionContainer->account->getIdAccount());

            if ($salon === null) {
                return false;
            }

            $transferRequestData = [
                'idSalon' => $salon->getIdSalon(),
                'idApplicant' => $sessionContainer->account->getIdAccount(),
                'applicantIdentity' => $this->params()->fromPost('applicant-identity',null),
                'iban' => $this->params()->fromPost('iban', null),
                'bic' => $this->params()->fromPost('bic', null),
                'amount' => $this->params()->fromPost('amount', null),
            ];

            $transferRequestData['balance'] = $this->getServiceLocator()
                ->get('Backend\Service\Transaction')
                ->calculateSalonTotalBalance($salon->getIdSalon());

            try {
                $this->getServiceLocator()->get('Backend\Service\TransferRequest')
                    ->createTransferRequest($transferRequestData);
            } catch (TransferRequestAmountIsTooHigh $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Le montant demandé est trop élevé.
                    Le solde s'élève à %1\$.2f€",
                    $transferRequestData['balance']
                ));
            
                return $viewModel;
            }

            return $this->redirect()->toRoute('professionnal-financial/list-transfer-request');
        }

        return $viewModel;
    }

    private function createFromFreelance()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');

        // Instancie le formulaire de création de demande de virement
        $createTransfertRequestForm = new CreateTransferRequestForm();

        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('createTransfertRequestForm',
                                $createTransfertRequestForm);

        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {

            $createTransfertRequestForm->setData($this->params()->fromPost());

            // Vérifie la validité du formulaire
            if ($createTransfertRequestForm->isValid() === false) {
                return $viewModel;
            }

            $transferRequestData = [
                'idFreelance' => $sessionContainer->account->getIdAccount(),
                'idApplicant' => $sessionContainer->account->getIdAccount(),
                'applicantIdentity' => $this->params()->fromPost('applicant-identity',
                                                                 null),
                'iban' => $this->params()->fromPost('iban', null),
                'bic' => $this->params()->fromPost('bic', null),
                'amount' => $this->params()->fromPost('amount', null),
            ];

            $transferRequestData['balance'] = $this->getServiceLocator()
                ->get('Backend\Service\Transaction')
                ->calculateFreelanceTotalBalance($sessionContainer->account->getIdAccount());
            
            try {
                $this->getServiceLocator()->get('Backend\Service\TransferRequest')
                    ->createTransferRequest($transferRequestData);
            } catch (TransferRequestAmountIsTooHigh $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Le montant demandé est trop élevé.
                    Le solde s'élève à %1\$.2f€",
                    $transferRequestData['balance']
                ));
            
                return $viewModel;
            }

            return $this->redirect()->toRoute('professionnal-financial/list-transfer-request');
        }

        return $viewModel;
    }

}
