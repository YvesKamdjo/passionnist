<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\Form;

use Backend\Entity\BookingComment;
use Zend\Form\Element\Radio;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class CreateBookingCommentForm extends Form
{    
    /**
     * @var BookingComment
     */
    private $bookingComment;
    
    /**
     * Instancie un formulaire de création de commentaire
     */
    public function __construct(BookingComment $bookingComment)
    {
        parent::__construct();
        
        $this->bookingComment = $bookingComment;
        
        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        // Rate
        $rateElement = new Radio('rate');
        $rateElement->setValueOptions([
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
        ]);
        $rateElement->setValue($this->bookingComment->getRate());
        $this->add($rateElement);
        
        // Comment
        $commentElement = new Textarea('comment');
        $commentElement->setValue($this->bookingComment->getComment());
        $this->add($commentElement);
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {        
        // Rate
        $rateInput = new Input('rate');
        $rateInput->setRequired(true);
        
        // Comment
        $commentInput = new Input('comment');
        $commentInput->setRequired(false);
        
        $inputFilter = $this->getInputFilter();
        $inputFilter->add($rateInput);
        $inputFilter->add($commentInput);
    }
}
