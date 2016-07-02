<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Administration\Infrastructure\DataTransferObject;

class AccountListItem
{
    public $idAccount;
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $password;
    public $address;
    public $zipcode;
    public $city;
    public $referral;
    public $qualificationFileName;
    public $isActive;
    public $roleList;
}
