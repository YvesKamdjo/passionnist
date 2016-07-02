<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Zend\Form\Element\Hidden;
use Zend\Form\Form;

class FacebookLogInForm extends Form
{

    /**
     * Instancie un formulaire d'authentification
     */
    public function __construct()
    {
        parent::__construct();

        $this->buildElements();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Access token
        $this->add(new Hidden('access_token'));
    }
}
