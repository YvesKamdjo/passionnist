<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Backend\Entity\Account;
use Backend\Entity\CustomerCharacteristic;
use Backend\Entity\JobServiceTemplate;
use Backend\Entity\JobServiceType;
use Backend\Entity\Salon;
use Backend\Mapper\JobServiceTemplateMapper;
use Zend\Log\Logger;

class SalonConfiguratorService
{
    /* @var $jobServiceTemplateMapper JobServiceTemplateMapper */
    private $jobServiceTemplateMapper;
    
    /* @var $logger Logger */
    private $logger;
    
    public function __construct(
        $jobServiceTemplateMapper,
        $logger
    ) {
        $this->jobServiceTemplateMapper = $jobServiceTemplateMapper;
        $this->logger = $logger;
    }
    
    /**
     * Permet de configurer la création d'un salon
     * 
     * @param Salon $salon
     * @param Account $manager
     */
    public function configure(Salon $salon, Account $manager)
    {
        $shortHairCharacteristic = new CustomerCharacteristic();
        $shortHairCharacteristic->setIdCustomerCharacteristic(1);
        $midHairCharacteristic = new CustomerCharacteristic();
        $midHairCharacteristic->setIdCustomerCharacteristic(2);
        $longHairCharacteristic = new CustomerCharacteristic();
        $longHairCharacteristic->setIdCustomerCharacteristic(3);
        
        $shampooingCoiffageType = new JobServiceType();
        $shampooingCoiffageType->setIdJobServiceType(1);
        $shampooingCoiffageCoupeType = new JobServiceType();
        $shampooingCoiffageCoupeType->setIdJobServiceType(2);
        $shampooingCoiffageColorationType = new JobServiceType();
        $shampooingCoiffageColorationType->setIdJobServiceType(3);
        $coupeHommeType = new JobServiceType();
        $coupeHommeType->setIdJobServiceType(4);
        $coupeEnfantType = new JobServiceType();
        $coupeEnfantType->setIdJobServiceType(5);
        $coiffageType = new JobServiceType();
        $coiffageType->setIdJobServiceType(6);
        $coupeType = new JobServiceType();
        $coupeType->setIdJobServiceType(7);
        $shampooingType = new JobServiceType();
        $shampooingType->setIdJobServiceType(8);
        $soinType = new JobServiceType();
        $soinType->setIdJobServiceType(9);
        $colorationType = new JobServiceType();
        $colorationType->setIdJobServiceType(10);
        $balayageType = new JobServiceType();
        $balayageType->setIdJobServiceType(11);
        $brushingType = new JobServiceType();
        $brushingType->setIdJobServiceType(12);
        $lissageType = new JobServiceType();
        $lissageType->setIdJobServiceType(13);
        $soinCapillaireType = new JobServiceType();
        $soinCapillaireType->setIdJobServiceType(14);
        $lissageBresilienType = new JobServiceType();
        $lissageBresilienType->setIdJobServiceType(15);
        $defrisageType = new JobServiceType();
        $defrisageType->setIdJobServiceType(16);
        $permanenteType = new JobServiceType();
        $permanenteType->setIdJobServiceType(17);
        $chignonType = new JobServiceType();
        $chignonType->setIdJobServiceType(18);
        $extensionAfroType = new JobServiceType();
        $extensionAfroType->setIdJobServiceType(19);
        $barbierType = new JobServiceType();
        $barbierType->setIdJobServiceType(20);
        $coiffureMariageType = new JobServiceType();
        $coiffureMariageType->setIdJobServiceType(21);
        $tresseAfroType = new JobServiceType();
        $tresseAfroType->setIdJobServiceType(22);
        $mechesType = new JobServiceType();
        $mechesType->setIdJobServiceType(23);
        $tissageType = new JobServiceType();
        $tissageType->setIdJobServiceType(24);
        $moins25AnsType = new JobServiceType();
        $moins25AnsType->setIdJobServiceType(25);
        $etudiantType = new JobServiceType();
        $etudiantType->setIdJobServiceType(26);
        $forfaitMariageType = new JobServiceType();
        $forfaitMariageType->setIdJobServiceType(27);
        $mariageType = new JobServiceType();
        $mariageType->setIdJobServiceType(28);
        $lissageJaponaisType = new JobServiceType();
        $lissageJaponaisType->setIdJobServiceType(29);
        $lissageFrancaisType = new JobServiceType();
        $lissageFrancaisType->setIdJobServiceType(30);
        
        $jobServiceTemplate = new JobServiceTemplate();
        $jobServiceTemplate->setIdManager($manager->getIdAccount());
        $jobServiceTemplate->setIdSalon($salon->getIdSalon());
        $jobServiceTemplate->setName('Shampooing & Coiffage sur cheveux long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);
        
        $jobServiceTemplate->setName('Shampooing & Coiffage & coupe sur cheveux long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageCoupeType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);
        
        $jobServiceTemplate->setName('Shampooing & Coiffage & Coloration sur cheveux long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageColorationType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);
        
        $jobServiceTemplate->setName('Coiffage sur cheveux long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coiffageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);
        
        $jobServiceTemplate->setName('Coupe sur cheveux long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coupeType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);
        
        $jobServiceTemplate->setName('coloration sur cheveux long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $colorationType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);
        
        $jobServiceTemplate->setName('Coupe homme');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coupeHommeType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Coloration');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $colorationType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Permanente');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $permanenteType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('coiffage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coiffageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('mèches');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $mechesType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Coupe enfant');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coupeEnfantType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Défrisage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $defrisageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Soin');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $soinType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Shampooing');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Balayage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $balayageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Brushing');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $brushingType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Lissage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $lissageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Soin capillaire');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $soinCapillaireType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Lissage brésilien');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $lissageBresilienType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Brushing');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $brushingType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Chignon');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $chignonType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Extension afro');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $extensionAfroType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Barbier');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $barbierType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Coiffure de mariage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coiffureMariageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Tresse Afro');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $tresseAfroType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('mèches');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $mechesType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('tissage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $tissageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('moins de 25 ans');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $moins25AnsType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('étudiant');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $etudiantType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Forfait mariage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $forfaitMariageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('mariage');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $mariageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Lissage japonais');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $lissageJaponaisType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Lissage français');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $longHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $lissageFrancaisType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Shampooing & Coiffage sur cheveux Mi-long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Shampooing & Coiffage & coupe sur cheveux Mi-long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageCoupeType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Shampooing & Coiffage & Coloration sur cheveux Mi-long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageColorationType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Coiffage sur cheveux Mi-long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coiffageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Coupe sur cheveux Mi-long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coupeType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('coloration sur cheveux Mi-long');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $midHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $colorationType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Shampooing & Coiffage sur cheveux Court');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Shampooing & Coiffage & coupe sur cheveux Court');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageCoupeType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Shampooing & Coiffage & Coloration sur cheveux Court');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $shampooingCoiffageColorationType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Coiffage sur cheveux Court');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coiffageType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('Coupe sur cheveux Court');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $coupeType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);

        $jobServiceTemplate->setName('coloration sur cheveux Court');
        
        $this->jobServiceTemplateMapper->create($jobServiceTemplate);
        $this->jobServiceTemplateMapper->addCustomerCharacteristic($jobServiceTemplate, $shortHairCharacteristic);
        $this->jobServiceTemplateMapper->addJobServiceType($jobServiceTemplate, $colorationType);
        $jobServiceTemplate->setIdJobServiceTemplate(null);
    }
}