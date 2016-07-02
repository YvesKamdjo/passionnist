<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class Referral
{
    const REFERRAL_PHONE = 1;
    const REFERRAL_EMAIL = 2;
    const REFERRAL_ADS = 3;
    
    private $idReferral;
    private $label;

    public function getIdReferral()
    {
        return $this->idReferral;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setIdReferral($idReferral)
    {
        $this->idReferral = $idReferral;
        return $this;
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
}
