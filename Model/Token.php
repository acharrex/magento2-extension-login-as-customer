<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model;

use Magento\Framework\Model\AbstractModel;
use Shopigo\LoginAsCustomer\Api\Data\TokenInterface;

class Token extends AbstractModel implements TokenInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Shopigo\LoginAsCustomer\Model\ResourceModel\Token');
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::TOKEN_ID);
    }

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getData(self::TOKEN);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set id
     *
     * @param int $id
     * @return TokenInterface
     */
    public function setId($id)
    {
        return $this->setData(self::TOKEN_ID, $id);
    }

    /**
     * Set user id
     *
     * @param int $userId
     * @return TokenInterface
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return TokenInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set token
     *
     * @param string $token
     * @return TokenInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * Set creation time
     *
     * @param string $date
     * @return TokenInterface
     */
    public function setCreatedAt($date)
    {
        return $this->setData(self::CREATED_AT, $date);
    }
}
