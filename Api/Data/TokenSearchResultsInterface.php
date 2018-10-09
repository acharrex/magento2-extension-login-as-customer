<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface TokenSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get tokens list
     *
     * @return \Shopigo\LoginAsCustomer\Api\Data\TokenInterface[]
     */
    public function getItems();

    /**
     * Set tokens list
     *
     * @param \Shopigo\LoginAsCustomer\Api\Data\TokenInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
