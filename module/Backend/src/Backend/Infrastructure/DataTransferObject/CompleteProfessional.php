<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Infrastructure\DataTransferObject;

class CompleteProfessional {
    public $idProfessional;
    public $firstName;
    public $lastName;
    public $email;
    public $address;
    public $zipcode;
    public $city;
    public $latitude;
    public $longitude;
    public $moveRange;
    public $biography;
    public $accountImageFilename;
    public $rate;
    public $like;
    public $jobServiceImageFilenameList;
}