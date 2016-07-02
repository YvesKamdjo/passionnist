<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Professionnal\Form;

use Backend\Collection\ArrayCollection;
use Professionnal\Form\Fieldset\DiscountFieldset;
use Zend\Form\Element\Collection;
use Zend\Form\Form;

class EditDiscountForm extends Form
{

    private $discountList;
    
    /**
     * Instancie un formulaire d'édition
     */
    public function __construct(ArrayCollection $discountCollection)
    {
        parent::__construct();

        $this->discountList = $this->organizeData($discountCollection);
        
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
            ->setTargetElement(new DiscountFieldset());

        $this->populateDiscountFieldsets($monday, date('N', strtotime('monday')));        
        $this->add($monday);
        
        $tuesday = new Collection('tuesday');
        $tuesday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new DiscountFieldset());
        
        $this->populateDiscountFieldsets($tuesday, date('N', strtotime('tuesday')));
        $this->add($tuesday);
        
        $wednesday = new Collection('wednesday');
        $wednesday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new DiscountFieldset());
        
        $this->populateDiscountFieldsets($wednesday, date('N', strtotime('wednesday')));
        $this->add($wednesday);
        
        $thursday = new Collection('thursday');
        $thursday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new DiscountFieldset());
        
        $this->populateDiscountFieldsets($thursday, date('N', strtotime('thursday')));
        $this->add($thursday);
        
        $friday = new Collection('friday');
        $friday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new DiscountFieldset());
        
        $this->populateDiscountFieldsets($friday, date('N', strtotime('friday')));
        $this->add($friday);
        
        $saturday = new Collection('saturday');
        $saturday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new DiscountFieldset());
        
        $this->populateDiscountFieldsets($saturday, date('N', strtotime('saturday')));
        $this->add($saturday);
        
        $sunday = new Collection('sunday');
        $sunday->setCount(0)
            ->setAllowAdd(true)
            ->setShouldCreateTemplate(true)
            ->setTargetElement(new DiscountFieldset());
        
        $this->populateDiscountFieldsets($sunday, date('N', strtotime('sunday')));
        $this->add($sunday);

    }
    
    /**
     * Réorganisation des promotions
     * 
     * @param ArrayCollection $discountCollection
     * @return ArrayCollection
     */
    private function organizeData(ArrayCollection $discountCollection)
    {
        // Définition de l'index du jour initial
        $lastDay = 0;
        $discountList = [];
        foreach ($discountCollection as $discount) {
            // Si le jour de la promotion actuelle n'est égale à celle de la dispo d'avant
            if ($discount->getDay() != $lastDay) {
                // On crée un nouveau tableau
                $discountList[$discount->getDay()] = [];
            }
            
            // Ajout de la promotion dans le tableau
            $discountList[$discount->getDay()][] = $discount;
            
            // Mise à jour de l'index du jour initial
            $lastDay = $discount->getDay();
        }
        
        return $discountList;
    }
    
    /**
     * Peuple les fieldsets d'une journée
     * 
     * @param Collection $collection
     * @param int $dayIndex
     */
    private function populateDiscountFieldsets(
        Collection $collection,
        $dayIndex
    ) {
        if (!isset($this->discountList[$dayIndex])) {
            return false;
        }        
        
        // Comptage des promotions de la journée
        $countDiscount = count($this->discountList[$dayIndex]);

        // Création des fieldsets
        for ($i = 0; $i < $countDiscount; $i ++) {
            $collection->add(new DiscountFieldset(), ['name' => $i]);
        }

        $fieldsets = $collection->getFieldsets();
        // Pour chaque fieldset, on définit les données
        foreach ($fieldsets as $fieldset) {
            $elements = $fieldset->getElements();

            $discount = array_shift($this->discountList[$dayIndex]);
            
            list($startHour, $startMinute) = explode(':', $discount->getStartTime());
            list($endHour, $endMinute) = explode(':', $discount->getEndTime());
            
            $elements['start']->setValue($startHour . ':' . $startMinute);
            $elements['end']->setValue($endHour . ':' . $endMinute);
            $elements['rate']->setValue($discount->getRate());
        }
    }
}
