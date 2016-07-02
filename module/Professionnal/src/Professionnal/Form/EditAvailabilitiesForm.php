<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Collection\ArrayCollection;
use Professionnal\Form\Fieldset\AvailabilityFieldset;
use Zend\Form\Element\Collection;
use Zend\Form\Form;

class EditAvailabilitiesForm extends Form
{

    private $availabilityList;
    
    /**
     * Instancie un formulaire d'édition
     */
    public function __construct(ArrayCollection $availabilityCollection)
    {
        parent::__construct();

        $this->availabilityList = $this->organizeData($availabilityCollection);
        
        $this->buildElements();
    }

    /**
     * Construit les éléments du formulaire
     */
    private function buildElements()
    {
        $monday = new Collection('monday');
        $monday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new AvailabilityFieldset());

        $this->populateAvailabilityFieldsets($monday, date('N', strtotime('monday')));        
        $this->add($monday);
        
        $tuesday = new Collection('tuesday');
        $tuesday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new AvailabilityFieldset());
        
        $this->populateAvailabilityFieldsets($tuesday, date('N', strtotime('tuesday')));
        $this->add($tuesday);
        
        $wednesday = new Collection('wednesday');
        $wednesday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new AvailabilityFieldset());
        
        $this->populateAvailabilityFieldsets($wednesday, date('N', strtotime('wednesday')));
        $this->add($wednesday);
        
        $thursday = new Collection('thursday');
        $thursday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new AvailabilityFieldset());
        
        $this->populateAvailabilityFieldsets($thursday, date('N', strtotime('thursday')));
        $this->add($thursday);
        
        $friday = new Collection('friday');
        $friday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new AvailabilityFieldset());
        
        $this->populateAvailabilityFieldsets($friday, date('N', strtotime('friday')));
        $this->add($friday);
        
        $saturday = new Collection('saturday');
        $saturday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new AvailabilityFieldset());
        
        $this->populateAvailabilityFieldsets($saturday, date('N', strtotime('saturday')));
        $this->add($saturday);
        
        $sunday = new Collection('sunday');
        $sunday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new AvailabilityFieldset());
        
        $this->populateAvailabilityFieldsets($sunday, date('N', strtotime('sunday')));
        $this->add($sunday);

    }
    
    /**
     * Réorganisation des disponibilités
     * 
     * @param ArrayCollection $availabilityCollection
     * @return ArrayCollection
     */
    private function organizeData(ArrayCollection $availabilityCollection)
    {
        // Définition de l'index du jour initial
        $lastDay = 0;
        $availabilityList = [];
        foreach ($availabilityCollection as $availability) {
            // Si le jour de la dispo actuelle n'est égale à celle de la dispo d'avant
            if ($availability->getDay() != $lastDay) {
                // On crée un nouveau tableau
                $availabilityList[$availability->getDay()] = [];
            }
            
            // Ajout de la dispo dans le tableau
            $availabilityList[$availability->getDay()][] = $availability;
            
            // Mise à jour de l'index du jour initial
            $lastDay = $availability->getDay();
        }
        
        return $availabilityList;
    }
    
    /**
     * Peuple les fieldsets d'une journée
     * 
     * @param Collection $collection
     * @param int $dayIndex
     */
    private function populateAvailabilityFieldsets(
        Collection $collection,
        $dayIndex
    ) {
        if (!isset($this->availabilityList[$dayIndex])) {
            return false;
        }        
        
        // Comptage des dispo de la journée
        $countMondayAvalabilities = count($this->availabilityList[$dayIndex]);

        // Création des fieldsets
        for ($i = 0; $i < $countMondayAvalabilities; $i ++) {
            $collection->add(new AvailabilityFieldset(), ['name' => $i]);
        }

        $fieldsets = $collection->getFieldsets();
        // Pour chaque fieldset, on définit les données
        foreach ($fieldsets as $fieldset) {
            $elements = $fieldset->getElements();

            $availability = array_shift($this->availabilityList[$dayIndex]);
            
            list($startHour, $startMinute) = explode(':', $availability->getStartTime());
            list($endHour, $endMinute) = explode(':', $availability->getEndTime());

            $elements['start']->setValue($startHour . ':' . $startMinute);
            $elements['end']->setValue($endHour . ':' . $endMinute);
        }
    }
}
