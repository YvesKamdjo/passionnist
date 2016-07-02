<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Administration\Form\Factory;

use Administration\Form\EditAccountForm;
use Backend\Service\AccountService;

class EditAccountFormFactory
{
    /* @var $accountService AccountService */
    private $accountService;
    
    public function __construct(
        AccountService $accountService
    ) {
        $this->accountService = $accountService;
    }

    public function createEditAccountForm($idAccount)
    {
        // Récupère les données du compte
        $account = $this->accountService->findByIdAccount($idAccount);
        
        return new EditAccountForm(
            $account
        );
    }
}
