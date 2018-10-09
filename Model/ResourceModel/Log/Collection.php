<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Shopigo\LoginAsCustomer\Model\Log', 'Shopigo\LoginAsCustomer\Model\ResourceModel\Log');
    }
}
