<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Entity\Salon;
use Zend\Form\Element\File;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class UploadSalonCertificateForm extends Form
{
    /* @var $salon Salon */
    private $salon;
    
    /**
     * Instancie un formulaire d'upload du k-bis
     */
    public function __construct(Salon $salon)
    {
        parent::__construct();

        $this->salon = $salon;
        
        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // K-bis
        $certificateElement = new File('certificate');
        $this->add($certificateElement);
        
        // idSalon
        $idSalonElement = new Text('idSalon');
        $idSalonElement->setValue($this->salon->getIdSalon());
        $this->add($idSalonElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {
        // K-bis
        $certificateInput = new Input('certificate');

        $inputFilter = $this->getInputFilter();
        $inputFilter->add($certificateInput);
    }
}
