<?php
/**
 * @package omictools
 * @author contact@wixiweb.fr
 */

namespace Backend\Infrastructure;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ConnectionInterface;

class TransactionManager
{
    /** @var ConnectionInterface */
    private $dbConnection;
    
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->dbConnection = $dbAdapter->getDriver()
            ->getConnection();
    }

    /**
     * Démarre une transaction
     */
    public function beginTransaction()
    {
        $this->dbConnection->beginTransaction();
    }

    /**
     * Commit une transaction
     */
    public function commit()
    {
        $this->dbConnection->commit();
    }
    
    /**
     * Rollback une transaction
     */
    public function rollBack()
    {
        $this->dbConnection->rollBack();
    }
}
