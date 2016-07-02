<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Zend\Form\Element\Radio;
use Zend\Form\Element\Select;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\Input;

class CreateAvailabilityExceptionForm extends Form
{    
    /**
     * Instancie un formulaire de suppression d'un template de prestation
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->buildElements();
        $this->buildInputs();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        $hours = [
            '06:00' => '06h00',
            '06:15' => '06h15',
            '06:30' => '06h30',
            '06:45' => '06h45',
            '07:00' => '07h00',
            '07:15' => '07h15',
            '07:30' => '07h30',
            '07:45' => '07h45',
            '08:00' => '08h00',
            '08:15' => '08h15',
            '08:30' => '08h30',
            '08:45' => '08h45',
            '09:00' => '09h00',
            '09:15' => '09h15',
            '09:30' => '09h30',
            '09:45' => '09h45',
            '10:00' => '10h00',
            '10:15' => '10h15',
            '10:30' => '10h30',
            '10:45' => '10h45',
            '11:00' => '11h00',
            '11:15' => '11h15',
            '11:30' => '11h30',
            '11:45' => '11h45',
            '12:00' => '12h00',
            '12:15' => '12h15',
            '12:30' => '12h30',
            '12:45' => '12h45',
            '13:00' => '13h00',
            '13:15' => '13h15',
            '13:30' => '13h30',
            '13:45' => '13h45',
            '14:00' => '14h00',
            '14:15' => '14h15',
            '14:30' => '14h30',
            '14:45' => '14h45',
            '15:00' => '15h00',
            '15:15' => '15h15',
            '15:30' => '15h30',
            '15:45' => '15h45',
            '16:00' => '16h00',
            '16:15' => '16h15',
            '16:30' => '16h30',
            '16:45' => '16h45',
            '17:00' => '17h00',
            '17:15' => '17h15',
            '17:30' => '17h30',
            '17:45' => '17h45',
            '18:00' => '18h00',
            '18:15' => '18h15',
            '18:30' => '18h30',
            '18:45' => '18h45',
            '19:00' => '19h00',
            '19:15' => '19h15',
            '19:30' => '19h30',
            '19:45' => '19h45',
            '20:00' => '20h00',
            '20:15' => '20h15',
            '20:30' => '20h30',
            '20:45' => '20h45',
            '21:00' => '21h00',
            '21:15' => '21h15',
            '21:30' => '21h30',
            '21:45' => '21h45',
            '22:00' => '22h00',
            '22:15' => '22h15',
            '22:30' => '22h30',
            '22:45' => '22h45',
        ];
        
        $days = [];
        for($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }
        
        $months = [
            '1' => 'Janvier',
            '2' => 'Février',
            '3' => 'Mars',
            '4' => 'Avril',
            '5' => 'Mai',
            '6' => 'Juin',
            '7' => 'Juillet',
            '8' => 'Août',
            '9' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre',
        ];
        
        $years = [
            date('Y') => date('Y'),
            date('Y') + 1 => date('Y') + 1,
        ];
        
        $startTimeElement = new Select('start-time');
        $startTimeElement->setValueOptions($hours);
        $this->add($startTimeElement);
        
        $endTimeElement = new Select('end-time');
        $endTimeElement->setValueOptions($hours);
        $this->add($endTimeElement);
        
        $startDayElement = new Select('start-day');
        $startDayElement->setValueOptions($days);
        $this->add($startDayElement);
        
        $startMonthElement = new Select('start-month');
        $startMonthElement->setValueOptions($months);
        $this->add($startMonthElement);
        
        $startYearElement = new Select('start-year');
        $startYearElement->setValueOptions($years);
        $this->add($startYearElement);
        
        $endDayElement = new Select('end-day');
        $endDayElement->setValueOptions($days);
        $this->add($endDayElement);
        
        $endMonthElement = new Select('end-month');
        $endMonthElement->setValueOptions($months);
        $this->add($endMonthElement);
        
        $endYearElement = new Select('end-year');
        $endYearElement->setValueOptions($years);
        $this->add($endYearElement);
        
        $isAvailabilityElement = new Radio('is-availability');
        $isAvailabilityElement->setValueOptions([
            '0' => 'Absence',
            '1' => 'Disponibilité',
        ]);
        $this->add($isAvailabilityElement);
        
        $detailsElement = new Textarea('details');
        $this->add($detailsElement);
        
    }

    /**
     * Défini les validateurs et filtres pour chaque élément
     */
    private function buildInputs()
    {        
        $inputFilter = $this->getInputFilter();

        $startTimeInput = new Input('start-time');
        $endTimeInput = new Input('end-time');
        $startDayInput = new Input('start-day');
        $startMonthInput = new Input('start-month');
        $startYearInput = new Input('start-year');
        $endDayInput = new Input('end-day');
        $endMonthInput = new Input('end-month');
        $endYearInput = new Input('end-year');
        $isAvailabilityInput = new Input('is-availability');
        $isAvailabilityInput->setRequired(false);
        $detailsInput = new Input('details');
        $detailsInput->setRequired(false);
        
        $inputFilter->add($startTimeInput);
        $inputFilter->add($endTimeInput);
        $inputFilter->add($startDayInput);
        $inputFilter->add($startMonthInput);
        $inputFilter->add($startYearInput);
        $inputFilter->add($endDayInput);
        $inputFilter->add($endMonthInput);
        $inputFilter->add($endYearInput);
        $inputFilter->add($isAvailabilityInput);
        $inputFilter->add($detailsInput);
    }
}
