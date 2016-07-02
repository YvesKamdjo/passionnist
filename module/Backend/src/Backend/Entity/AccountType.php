<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class AccountType
{
    const ACCOUNT_TYPE_EMPLOYEE     = 1;
    const ACCOUNT_TYPE_MANAGER      = 2;
    const ACCOUNT_TYPE_FREELANCE    = 3;
    const ACCOUNT_TYPE_CUSTOMER     = 4;
    const ACCOUNT_TYPE_ADMIN        = 5;
    
    private $idAccountType;
    private $key;

    public function getIdAccountType()
    {
        return $this->idAccountType;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setIdAccountType($idAccountType)
    {
        $this->idAccountType = $idAccountType;
        return $this;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
}
