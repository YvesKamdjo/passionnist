<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Administration\Controller;

use Administration\Form\CreateProspectForm;
use Backend\Service\ProspectService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProspectController extends AbstractActionController
{    
    public function indexAction()
    {
        // Instancie le service Prospect
        /* @var $prospectService ProspectService */
        $prospectService = $this->getServiceLocator()->get('Backend\Service\Prospect');
        
        // Récupération de la liste des prospects
        $prospectCollection = $prospectService->listAll();
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('prospectCollection', $prospectCollection);
        
        return $viewModel;
    }
    
    public function createAction()
    {
        // Instancie le formulaire de création d'un prospect
        $createProspectForm = new CreateProspectForm();
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('createProspectForm', $createProspectForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $createProspectForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($createProspectForm->isValid() === false) {
                return $viewModel;
            }
            
            $prospectData = [
                'lastName' => $this->params()->fromPost('last-name', null),
                'firstName' => $this->params()->fromPost('first-name', null),
                'email' => $this->params()->fromPost('email', null),
                'phone' => $this->params()->fromPost('phone', null),
            ];

            // Instancie le service Prospect
            /* @var $prospectService ProspectService */
            $prospectService = $this->getServiceLocator()
                ->get('Backend\Service\Prospect');

            try {
                $prospectService->createProspect($prospectData);
                                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "Le prospect a été créé avec succès"
                ));
                
                return $this->redirect()->toRoute('administration-prospect');
            }
            catch (PhoneHasWrongFormatException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Le numéro de téléphone n'est pas au bon format"
                ));
            }
        }
        
        return $viewModel;
    }
}