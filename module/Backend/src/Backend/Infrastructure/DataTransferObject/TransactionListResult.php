<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Infrastructure\DataTransferObject;

class TransactionListResult {
    public $idTransaction;
    public $idSalon;
    public $idFreelance;
    public $amount;
    public $description;
    public $idCreator;
    public $creationDate;
    public $firstName;
    public $lastName;
}