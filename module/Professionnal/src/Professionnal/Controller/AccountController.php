<?php
/**
 * @package wixiweb-zf2-skeleton
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Controller;

use Application\Exception\EmailIsAlreadyUsedException;
use Application\Exception\PhoneHasWrongFormatException;
use Backend\Service\AccountService;
use Exception;
use Professionnal\Form\EditProfessionnalProfileForm;
use Professionnal\Form\UploadProfessionnalAccountImageForm;
use Professionnal\Form\ProfessionnalJoinSalonForm;
use Professionnal\Form\UploadProfessionnalQualificationForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
{    
    public function editProfileAction()
    {        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de profil
        /* @var $editprofessionnalProfileForm EditProfessionnalProfileForm */
        $editprofessionnalProfileForm = $serviceManager->get('ServiceManager')
            ->get('formElementManager')
            ->get('Professionnal\Form\EditProfessionnalProfile');
        
        // Instancie le formulaire d'upload de l'avatar
        $uploadProfessionnalAccountImageForm = new UploadProfessionnalAccountImageForm();
        
        // Instancie le formulaire d'upload du diplôme
        $uploadProfessionnalQualificationForm = new UploadProfessionnalQualificationForm();
        
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('editProfessionnalProfileForm', $editprofessionnalProfileForm);
        $viewModel->setVariable('uploadProfessionnalAccountImageForm', $uploadProfessionnalAccountImageForm);
        $viewModel->setVariable('uploadProfessionnalQualificationForm', $uploadProfessionnalQualificationForm);
            
        // Récupération de la configuration de l'API Google Maps
        $config = $serviceManager->get('config');
        $viewModel->setVariable('mapsApiConfig', $config['maps']);
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $serviceManager->get('Backend\Service\Account');
            
        $account = $accountService->findByIdAccount($sessionContainer->account->getIdAccount());
        $viewModel->setVariable('account', $account);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $editprofessionnalProfileForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($editprofessionnalProfileForm->isValid() === false) {
                return $viewModel;
            }
            
            $editedProfileData = [
                'idAccount' => $sessionContainer->account->getIdAccount(),
                'lastName' => $this->params()->fromPost('last-name', null),
                'firstName' => $this->params()->fromPost('first-name', null),
                'email' => $this->params()->fromPost('email', null),
                'phone' => $this->params()->fromPost('phone', null),
                'address' => $this->params()->fromPost('address', null),
                'zipcode' => $this->params()->fromPost('zipcode', null),
                'city' => $this->params()->fromPost('city', null),
                'latitude' => $this->params()->fromPost('latitude', null),
                'longitude' => $this->params()->fromPost('longitude', null),
                'moveRange' => $this->params()->fromPost('move-range', null),
                'biography' => $this->params()->fromPost('biography', null),
            ];
            
            try {
                $accountService->editProfile($editedProfileData);
                
                $viewModel->setVariable('editProfileSuccess', "Votre profil a été mis à jour avec succès.");
            }
            catch (EmailIsAlreadyUsedException $exception) {
                $viewModel->setVariable('editProfileError', 'L\'email est déjà utilisée.');
            }
            catch (PhoneHasWrongFormatException $exception) {
                $viewModel->setVariable('editProfileError', 'Le numéro de téléphone n\'est pas au bon format.');
            }
        }
        
        return $viewModel;
    }
    
    public function sendAvatarAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        $jsonModel->setVariable('message', "Une erreur inattendue est survenue. Si le problème persiste, veuillez contacter notre service client.");
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire d'upload de l'avatar
        $uploadProfessionnalAccountImageForm = new UploadProfessionnalAccountImageForm();
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() !== true) {
            return $jsonModel;
        }
        
        $uploadProfessionnalAccountImageForm->setData(array_merge_recursive(
            $this->params()->fromPost(),
            $this->params()->fromFiles()
        ));
        
        // Vérifie la validité du formulaire
        if ($uploadProfessionnalAccountImageForm->isValid() === false) {
            $jsonModel->setVariable('errors', $uploadProfessionnalAccountImageForm->getMessages());
            return $jsonModel;
        }
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $serviceManager->get('Backend\Service\Account');
        $uploadProfessionnalAccountImageFormData = $uploadProfessionnalAccountImageForm->getData();
        
        // Lance l'édition de l'avatar
        try {
            $accountService->editAccountImage(
                (int) $sessionContainer->account->getIdAccount(),
                $uploadProfessionnalAccountImageFormData['account-image']
            );
            
            $account = $accountService->findByIdAccount($sessionContainer->account->getIdAccount());
            
            $jsonModel->setVariable('avatarFilename', '/image/account-image/' . $account->getAccountImageFilename());
            $jsonModel->setVariable('success', true);
        }
        catch(Exception $exception) {
            $this->getServiceLocator()
                ->get('Logger\Error')
                ->info($exception->getMessage());
            
            $jsonModel->setVariable('message', "Votre photo de profil n'a pas pu être modifiée. Si le problème persiste, veuillez contacter notre service client.");
        }
       
        return $jsonModel;
    }
    
    public function sendQualificationAction()
    {
        $jsonModel = new JsonModel();
        $jsonModel->setVariable('success', false);
        $jsonModel->setVariable('message', "Une erreur inattendue est survenue. Si le problème persiste, veuillez contacter notre service client.");
        
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire d'upload du diplôme
        $uploadProfessionnalQualificationForm = new UploadProfessionnalQualificationForm();
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() !== true) {
            return $jsonModel;
        }
        
        $uploadProfessionnalQualificationForm->setData(array_merge_recursive(
            $this->params()->fromPost(),
            $this->params()->fromFiles()
        ));
        
        // Vérifie la validité du formulaire
        if ($uploadProfessionnalQualificationForm->isValid() === false) {
            $jsonModel->setVariable('errors', $uploadProfessionnalQualificationForm->getMessages());
            return $jsonModel;
        }
        
        // Instancie le service Account
        /* @var $accountService AccountService */
        $accountService = $serviceManager->get('Backend\Service\Account');
        $uploadProfessionnalQualificationFormData = $uploadProfessionnalQualificationForm->getData();
        
        // Lance l'envoi du diplôme
        try {
            $accountService->addQualification(
                $sessionContainer->account->getIdAccount(),
                $uploadProfessionnalQualificationFormData['qualification']
            );
            
            $account = $accountService->findByIdAccount($sessionContainer->account->getIdAccount());
            
            $jsonModel->setVariable('qualificationFilename', '/image/qualification/' . $account->getQualificationFilename());
            $jsonModel->setVariable('success', true);
        }
        catch(Exception $exception) {
            $this->getServiceLocator()
                ->get('Logger\Error')
                ->info($exception->getMessage());
            
            $jsonModel->setVariable('message', "Votre diplôme n'a pas pu être modifié. Si le problème persiste, veuillez contacter notre service client.");
        }
        
        return $jsonModel;
    }

    public function joinSalonAction()
    {
        /* @var $serviceManager ServiceManager */
        $serviceManager = $this->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        // Instancie le formulaire de connexion
        /* @var $professionnalJoinSalonForm ProfessionnalJoinSalonForm */
        $professionnalJoinSalonForm = new ProfessionnalJoinSalonForm();
                
        // Paramètrage de la vue
        $viewModel = new ViewModel();
        $viewModel->setVariable('professionnalJoinSalonForm', $professionnalJoinSalonForm);
        
        // Vérifie la soumission du formulaire
        if ($this->getRequest()->isPost() === true) {
            $professionnalJoinSalonForm->setData($this->params()->fromPost());
            
            // Vérifie la validité du formulaire
            if ($professionnalJoinSalonForm->isValid() === false) {
                return $viewModel;
            }

            // Instancie le service Salon
            // Envoie la demande (ou l'invitation) au gérant
            try {
                /* @var $salonService SalonService */
                $salonService = $serviceManager->get('Backend\Service\Salon');
                $salonService->joinSalon([
                    'idAccount' => $sessionContainer->account->getIdAccount(),
                    'managerEmail' => $this->params()->fromPost('manager-email', null),
                ]);
                
                $viewModel->setVariable('joinSalonSuccess', "Votre demande a été envoyée avec succès.");
            }
            catch (Exception $e) {
                $viewModel->setVariable('joinSalonError', "Votre demande n'a pas pu être envoyée. Si le problème persiste, veuillez contacter notre service client.");
            }
        }
        
        return $viewModel;
    }
}
