<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Shopigo\LoginAsCustomer\Api\Data\LogInterface as DataLogInterface;

interface LogRepositoryInterface
{
    /**
     * Save log
     *
     * @param DataLogInterface $log
     * @return DataLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(DataLogInterface $log);

    /**
     * Generate new log
     *
     * @param int $userId
     * @param string $userName
     * @param int $customerId
     * @param int $storeId
     * @return DataLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate($userId, $username, $customerId, $storeId);

    /**
     * Load log data by given log identity
     *
     * @param int $logId
     * @return DataLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($logId);

    /**
     * Retrieve logs matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Shopigo\LoginAsCustomer\Api\Data\LogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete log
     *
     * @param DataLogInterface $log
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(DataLogInterface $log);

    /**
     * Delete log by id
     *
     * @param int $logId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($logId);
}
