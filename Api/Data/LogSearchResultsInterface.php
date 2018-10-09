<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface LogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get logs list
     *
     * @return \Shopigo\LoginAsCustomer\Api\Data\LogInterface[]
     */
    public function getItems();

    /**
     * Set logs list
     *
     * @param \Shopigo\LoginAsCustomer\Api\Data\LogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
