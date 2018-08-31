<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Api\Data;

interface TokenInterface
{
    /**
     * Constants for keys of data array
     * Identical to the name of the getter in snake case
     */
    const TOKEN_ID    = 'token_id';
    const USER_ID     = 'user_id';
    const CUSTOMER_ID = 'customer_id';
    const TOKEN       = 'token';
    const CREATED_AT  = 'created_at';

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get user id
     *
     * @return int|null
     */
    public function getUserId();

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Get token
     *
     * @return string|null
     */
    public function getToken();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set id
     *
     * @param int $id
     * @return TokenInterface
     */
    public function setId($id);

    /**
     * Set user id
     *
     * @param int $userId
     * @return TokenInterface
     */
    public function setUserId($userId);

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return TokenInterface
     */
    public function setCustomerId($customerId);

    /**
     * Set token
     *
     * @param string $token
     * @return TokenInterface
     */
    public function setToken($token);

    /**
     * Set creation time
     *
     * @param string $date
     * @return TokenInterface
     */
    public function setCreatedAt($date);
}
