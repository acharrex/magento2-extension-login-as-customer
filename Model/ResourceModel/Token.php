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

class Token extends AbstractDb
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
        $this->_init('shopigo_loginascustomer_token', 'token_id');
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
     * Delete expired tokens (after 5 minutes)
     *
     * @return void
     */
    public function deleteExpiredTokens()
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['created_at < ?' => $this->coreDate->gmtDate(null, time() - (60 * 5))]
        );
    }
}
