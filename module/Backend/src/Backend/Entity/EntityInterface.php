<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

interface EntityInterface
{
    /**
     * Permet de vérifier si deux entités ont la même identité
     * 
     * @param EntityInterface $candidate
     * @return boolean
     */
    public function sameIdentityAs(EntityInterface $candidate);
}