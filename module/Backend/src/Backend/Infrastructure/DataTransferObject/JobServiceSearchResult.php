<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Infrastructure\DataTransferObject;

class JobServiceSearchResult {
    public $likeCount;
    public $jobServiceId;
    public $jobServiceName;
    public $jobServicePrice;
    public $jobServiceLocation;
    public $accountId;
    public $accountName;
    public $accountImageFilename;
    public $jobServiceImageFilename;
    public $customerCharacteristicList;
    public $maxDiscount;
    public $isSalon;
}