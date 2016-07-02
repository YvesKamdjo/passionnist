<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Service;

use Application\Exception\MapperException;
use Application\Exception\ServiceException;
use Backend\Entity\Account;
use Backend\Entity\Salon;
use Backend\Mapper\AccountMapper;
use Backend\Mapper\AttachmentRequestMapper;
use Backend\Mapper\PermissionMapper;
use Backend\Mapper\SalonMapper;
use Zend\Log\Logger;

class AttachmentRequestService
{
    /* @var $logger Logger */
    private $logger;

    /* @var $attachmentRequestMapper AttachmentRequestMapper */
    private $attachmentRequestMapper;

    /* @var $salonMapper SalonMapper */
    private $salonMapper;

    /* @var $accountMapper AccountMapper */
    private $accountMapper;

    /* @var $permissionMapper PermissionMapper */
    private $permissionMapper;

    public function __construct(
        $attachmentRequestMapper,
        $salonMapper,
        $accountMapper,
        $permissionMapper,
        $logger
    ) {
        $this->attachmentRequestMapper = $attachmentRequestMapper;
        $this->salonMapper = $salonMapper;
        $this->accountMapper = $accountMapper;
        $this->permissionMapper = $permissionMapper;
        $this->logger = $logger;
    }
    
    public function acceptAttachmentRequest(array $attachmentRequestData)
    {
        $employee = new Account();
        $employee->setIdAccount($attachmentRequestData['employeeId']);
        
        $salon = new Salon();
        $salon->setIdSalon($attachmentRequestData['salonId']);
        
        try {
            // Création du lien entre l'employé et le salon
            $this->salonMapper->addEmployee($salon, $employee);
            
            // Supprime la permission de demander un rattachement à l'employé
            $this->accountMapper->removePermission($employee, 'create-attachment-request');
            // Ajoute les permissions sur les prestations
            $this->permissionMapper->addAccountPermission($employee, 'access-job-service-list');
            $this->permissionMapper->addAccountPermission($employee, 'create-job-service');
            $this->permissionMapper->addAccountPermission($employee, 'edit-job-service');

            $this->attachmentRequestMapper->flushEmployeeRequest($employee);
        } catch (MapperException $exception) {
            $this->logger->alert($exception);
            throw new ServiceException();
        }
    }
    
    public function refuseAttachmentRequest(array $attachmentRequestData)
    {
        $employee = new Account();
        $employee->setIdAccount($attachmentRequestData['employeeId']);
        
        $this->attachmentRequestMapper->flushEmployeeRequest($employee);
    }
}
