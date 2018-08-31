<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Log extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $coreDate;

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('shopigo_loginascustomer_log', 'log_id');
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param DateTime $coreDate
     * @param string $connectionName
     * @return void
     */
    public function __construct(
        Context $context,
        DateTime $coreDate,
        $connectionName = null
    ) {
        $this->coreDate = $coreDate;
        parent::__construct($context, $connectionName);
    }

    /**
     * Get all admin user names that are currently in login log table
     * Possible SQL-performance issue
     *
     * @return array
     */
    public function getUserNames()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->distinct()->from(
            ['admins' => $this->getTable('admin_user')],
            'username'
        )->joinInner(
            ['log' => $this->getMainTable()],
            'admins.username = log.' . $connection->quoteIdentifier('user'),
            []
        );
        return $connection->fetchCol($select);
    }

    /**
     * Delete unnecessary logs
     *
     * @param int $cleanAfterDay
     * @return void
     */
    public function deleteOldLogs($cleanAfterDay)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['time < ?' => $this->coreDate->gmtDate(null, time() - (abs($cleanAfterDay) * 86400))]
        );
    }
}
