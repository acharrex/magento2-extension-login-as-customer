<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Shopigo\LoginAsCustomer\Api\Data\TokenInterface as DataTokenInterface;

interface TokenRepositoryInterface
{
    /**
     * Save token
     *
     * @param DataTokenInterface $token
     * @return DataTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(DataTokenInterface $token);

    /**
     * Generate new token
     *
     * @param int $userId
     * @param int $customerId
     * @return DataTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate($userId, $customerId);

    /**
     * Load token data by given token identity
     *
     * @param int $tokenId
     * @return DataTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($tokenId);

    /**
     * Load token data by given token hash
     *
     * @param string $hash
     * @return DataTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByHash($hash);

    /**
     * Retrieve tokens matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Shopigo\LoginAsCustomer\Api\Data\TokenSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete token
     *
     * @param DataTokenInterface $token
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(DataTokenInterface $token);

    /**
     * Delete token by id
     *
     * @param int $tokenId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tokenId);
}
