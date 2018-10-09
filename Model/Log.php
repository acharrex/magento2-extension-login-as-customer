<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model;

use Magento\Framework\Model\AbstractModel;
use Shopigo\LoginAsCustomer\Api\Data\LogInterface;

class Log extends AbstractModel implements LogInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Shopigo\LoginAsCustomer\Model\ResourceModel\Log');
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return parent::getData(self::LOG_ID);
    }

    /**
     * Get user id
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get user
     *
     * @return string|null
     */
    public function getUser()
    {
        return $this->getData(self::USER);
    }

    /**
     * Get ip address
     *
     * @return string|null
     */
    public function getIp()
    {
        return $this->getData(self::IP);
    }

    /**
     * Get real ip address if visitor used proxy
     *
     * @return string|null
     */
    public function getXForwardedIp()
    {
        return $this->getData(self::X_FORWARDED_IP);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getTime()
    {
        return $this->getData(self::TIME);
    }

    /**
     * Set id
     *
     * @param int $id
     * @return LogInterface
     */
    public function setId($id)
    {
        return $this->setData(self::LOG_ID, $id);
    }

    /**
     * Set user id
     *
     * @param int $userId
     * @return LogInterface
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return LogInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return LogInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set user
     *
     * @param string $user
     * @return LogInterface
     */
    public function setUser($user)
    {
        return $this->setData(self::USER, $user);
    }

    /**
     * Set ip address
     *
     * @param string $ip
     * @return LogInterface
     */
    public function setIp($ip)
    {
        return $this->setData(self::IP, $ip);
    }

    /**
     * Set real ip address if visitor used proxy
     *
     * @param string $ip
     * @return LogInterface
     */
    public function setXForwardedIp($ip)
    {
        return $this->setData(self::X_FORWARDED_IP, $ip);
    }

    /**
     * Set creation time
     *
     * @param string $date
     * @return LogInterface
     */
    public function setTime($date)
    {
        return $this->setData(self::TIME, $date);
    }

}
