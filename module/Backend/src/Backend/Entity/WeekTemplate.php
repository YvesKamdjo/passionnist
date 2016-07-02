<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class WeekTemplate
{
    
    private $idWeekTemplate;
    private $idAccount;

    public function getIdWeekTemplate()
    {
        return $this->idWeekTemplate;
    }

    public function getIdAccount()
    {
        return $this->idAccount;
    }

    public function setIdWeekTemplate($idWeekTemplate)
    {
        $this->idWeekTemplate = $idWeekTemplate;
        return $this;
    }

    public function setIdAccount($idAccount)
    {
        $this->idAccount = $idAccount;
        return $this;
    }

}
