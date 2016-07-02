<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Mapper;

use Application\Exception\MapperException;
use Backend\Entity\Payment;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\InvalidQueryException;

class PaymentMapper
{
    private $db;

    public function __construct(Adapter $db)
    {
        $this->db = $db;
    }

    public function create(Payment $payment)
    {
        // Création d'une prestation
        $insert = '
            INSERT INTO Payment (
                idCustomer,
                idFreelance,
                idSalon,
                amount
            )
            VALUES (
                :idCustomer,
                :idFreelance,
                :idSalon,
                :amount
            )';

        $statement = $this->db->createStatement($insert);
        
        try {
            $statement->execute([
                ':idCustomer' => $payment->getIdCustomer(),
                ':idFreelance' => $payment->getIdFreelance(),
                ':idSalon' => $payment->getIdSalon(),
                ':amount' => $payment->getAmount(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la création d'un paiement",
                null,
                $exception
            );
        }
        
        $payment->setIdPayment($this->db->getDriver()->getLastGeneratedValue());
    }
    
    /**
     * Récupère un paiement à partir de son ID 
     * 
     * @param Payment $payment
     * @return Payment
     * @throws MapperException
     */
    public function findByPaymentId(Payment $payment)
    {
        // Récupère le paiement
        $select = '
            SELECT
                idPayment,
                idCustomer,
                idFreelance,
                idSalon,
                amount,
                status,
                creationDate,
                bankReturn
            FROM 
                Payment
            WHERE
                idPayment = :idPayment;';

        $statement = $this->db->createStatement($select);
        
        try {
            $result = $statement->execute([
                ':idPayment' => $payment->getIdPayment(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la récupération d'un paiement",
                null,
                $exception
            );
        }
        
        if ($result->isQueryResult() === false || $result->count() != 1) {
            return null;
        }

        return $this->hydrateEntity($result->current());
    }
    
    /**
     * Modifie un paiement
     * 
     * @param Payment $payment
     * @throws MapperException
     */
    public function edit(Payment $payment)
    {
        // Modification d'une prestation
        $update = '
            UPDATE
                Payment
            SET
                idCustomer = :idCustomer,
                idFreelance = :idFreelance,
                idSalon = :idSalon,
                amount = :amount,
                status = :status,
                bankReturn = :bankReturn,
                creationDate = :creationDate
            WHERE
                idPayment = :idPayment
        ;';

        $statement = $this->db->createStatement($update);
        
        try {
            $statement->execute([
                ':idCustomer' => $payment->getIdCustomer(),
                ':idFreelance' => $payment->getIdFreelance(),
                ':idSalon' => $payment->getIdSalon(),
                ':amount' => $payment->getAmount(),
                ':status' => $payment->getStatus(),
                ':bankReturn' => $payment->getBankReturn(),
                ':creationDate' => $payment->getCreationDate(),
                ':idPayment' => $payment->getIdPayment(),
            ]);
        }
        catch (InvalidQueryException $exception) {
            throw new MapperException(
                "Erreur lors de la modification d'un paiement",
                null,
                $exception
            );
        }
    }

    /**
     * Peuple un paiement
     * 
     * @param array $row
     * @return Payment
     */
    private function hydrateEntity(array $row)
    {
        $payment = new Payment();
        $payment->setIdPayment($row['idPayment']);
        $payment->setIdCustomer($row['idCustomer']);
        $payment->setIdFreelance($row['idFreelance']);
        $payment->setIdSalon($row['idSalon']);
        $payment->setAmount($row['amount']);
        $payment->setStatus($row['status']);
        $payment->setCreationDate($row['creationDate']);
        $payment->setBankReturn($row['bankReturn']);
        
        return $payment;
    }
}
