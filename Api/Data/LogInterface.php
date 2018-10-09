<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Api\Data;

interface LogInterface
{
    /**
     * Constants for keys of data array
     * Identical to the name of the getter in snake case
     */
    const LOG_ID         = 'log_id';
    const USER_ID        = 'user_id';
    const CUSTOMER_ID    = 'customer_id';
    const STORE_ID       = 'store_id';
    const USER           = 'user';
    const IP             = 'ip';
    const X_FORWARDED_IP = 'x_forwarded_ip';
    const TIME           = 'time';

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
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Get user
     *
     * @return string|null
     */
    public function getUser();

    /**
     * Get ip address
     *
     * @return string|null
     */
    public function getIp();

    /**
     * Get real ip address if visitor used proxy
     *
     * @return string|null
     */
    public function getXForwardedIp();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getTime();

    /**
     * Set id
     *
     * @param int $id
     * @return LogInterface
     */
    public function setId($id);

    /**
     * Set user id
     *
     * @param int $userId
     * @return LogInterface
     */
    public function setUserId($userId);

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return LogInterface
     */
    public function setCustomerId($customerId);

    /**
     * Set store id
     *
     * @param int $storeId
     * @return LogInterface
     */
    public function setStoreId($storeId);

    /**
     * Set user
     *
     * @param string $user
     * @return LogInterface
     */
    public function setUser($user);

    /**
     * Set ip address
     *
     * @param int $ip
     * @return LogInterface
     */
    public function setIp($ip);

    /**
     * Set real ip address if visitor used proxy
     *
     * @param int $ip
     * @return LogInterface
     */
    public function setXForwardedIp($ip);

    /**
     * Set creation time
     *
     * @param string $date
     * @return LogInterface
     */
    public function setTime($date);
}
