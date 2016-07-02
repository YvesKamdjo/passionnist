<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Application\Exception\ServiceException;
use Backend\Service\DiscountService;
use Backend\Service\SalonService;
use Professionnal\Form\EditDiscountForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class DiscountController extends AbstractActionController
{
    public function editAction()
    {
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
            
        // Instancie le formulaire de définition des promotions
        /* @var $editDiscountForm EditDiscountForm */
        $editDiscountForm = $this->getServiceLocator()
            ->get('formElementManager')
            ->get('Professionnal\Form\EditDiscount');
        
        $viewModel = new ViewModel();
        $viewModel->setVariable('editDiscountForm', $editDiscountForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $editDiscountForm->setData($this->params()->fromPost());
            
            // Instancie le service Salon
            /* @var $salonService SalonService */
            $salonService = $this->getServiceLocator()->get('Backend\Service\Salon');
            $salon = $salonService->findByManagerIdAccount($sessionContainer->account->getIdAccount());
            
            // Instancie le service Discount
            /* @var $discountService DiscountService */
            $discountService = $this->getServiceLocator()->get('Backend\Service\Discount');
            
            // Vérifie la validité du formulaire
            if ($editDiscountForm->isValid() === false) {
                return $viewModel;
            }
            
            try {
                if (is_null($salon)) {
                    $discountService->editDiscount(
                        $editDiscountForm->getData(),
                        null,
                        $sessionContainer->account->getIdAccount()
                    );
                }
                else {
                    $discountService->editDiscount(
                        $editDiscountForm->getData(),
                        (int) $salon->getIdSalon()
                    );
                }
                
                $this->flashMessenger()->addSuccessMessage(sprintf(
                    "Les promotions ont bien été mises à jour"
                ));
                
                return $this->redirect()->toRoute('professionnal-discount/edit');
            }
            catch (ServiceException $exception) {
                $this->flashMessenger()->addErrorMessage(sprintf(
                    "Une erreur est survenue, veuillez réessayer"
                ));
            }
        }
        
        return $viewModel;
    }
}
