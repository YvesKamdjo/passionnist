<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Infrastructure\DataTransferObject;

class CompleteJobService {
    public $idJobService;
    public $idJobServiceTemplate;
    public $idProfessional;
    public $name;
    public $duration;
    public $description;
    public $price;
    public $accountImageFilename;
    public $accountFirstName;
    public $accountLastName;
    public $jobServiceImagesCount;
    public $jobServiceImages;
    public $customerCharacteristicList;
    public $maxDiscount;
}