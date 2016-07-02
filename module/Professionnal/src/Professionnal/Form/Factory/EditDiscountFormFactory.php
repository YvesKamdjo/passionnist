<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form\Factory;

use Backend\Service\DiscountService;
use Backend\Service\SalonService;
use Professionnal\Form\EditDiscountForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;

class EditDiscountFormFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $formElementManager)
    {
        $serviceManager = $formElementManager->getServiceLocator();
        
        // Création du conteneur de session
        $sessionContainer = new Container('hairlov');
        
        /* @var $salonService SalonService */
        $salonService = $serviceManager->get('Backend\Service\Salon');
        $salon = $salonService
            ->findByManagerIdAccount($sessionContainer->account->getIdAccount());
        
        /* @var $discountService DiscountService */
        $discountService = $serviceManager->get('Backend\Service\Discount');
        
        if (is_null($salon)) {
            $discountCollection = $discountService->findDiscountByFreelanceId($sessionContainer->account->getIdAccount());
        }
        else {
            $discountCollection = $discountService->findDiscountBySalonId($salon->getIdSalon());
        }
        
        return new EditDiscountForm($discountCollection);
    }
}
