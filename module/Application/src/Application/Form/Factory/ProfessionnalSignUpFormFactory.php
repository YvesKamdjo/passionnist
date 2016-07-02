<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form\Factory;

use Application\Form\ProfessionnalSignUpForm;
use Backend\Service\AccountTypeService;
use Backend\Service\ReferralService;

class ProfessionnalSignUpFormFactory
{
    /* @var $accountTypeService AccountTypeService */
    private $accountTypeService;
    /* @var $referralService ReferralService */
    private $referralService;
    
    public function __construct(
        AccountTypeService $accountTypeService,
        ReferralService $referralService
    ) {
        $this->accountTypeService = $accountTypeService;
        $this->referralService = $referralService;
    }

    public function createProfessionalSignUpForm($getData = [])
    {
        // Récupère les types de compte
        $accountTypeCollection = $this->accountTypeService->findAll();

        // Récupère les propositions de découverte de la plateforme
        $referralCollection = $this->referralService->findAll();

        return new ProfessionnalSignUpForm($getData, $accountTypeCollection, $referralCollection);
    }
}