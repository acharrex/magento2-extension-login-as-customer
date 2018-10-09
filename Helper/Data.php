<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Config paths
     */
    const XML_PATH_ENABLED                   = 'shopigo_loginascustomer/general/enabled';
    const XML_PATH_DISABLE_OUTPUT            = 'advanced/modules_disable_output/Shopigo_LoginAsCustomer';
    const XML_PATH_REDIRECT_URL              = 'shopigo_loginascustomer/general/redirect_url';
    const XML_PATH_LOG_ENABLED               = 'shopigo_loginascustomer/general/log_enabled';
    const XML_PATH_CUSTOMER_GRID_BTN_ENABLED = 'shopigo_loginascustomer/general/cutomer_grid_btn_enabled';
    const XML_PATH_CUSTOMER_EDIT_BTN_ENABLED = 'shopigo_loginascustomer/general/cutomer_edit_btn_enabled';
    const XML_PATH_ORDER_VIEW_BTN_ENABLED    = 'shopigo_loginascustomer/general/order_view_btn_enabled';
    const XML_PATH_CHECKOUT_ENABLED          = 'shopigo_loginascustomer/general/checkout_enabled';
    const XML_PATH_CHECKOUT_MESSAGE          = 'shopigo_loginascustomer/general/checkout_message';

    /**
     * Default redirect path
     */
    const DEFAULT_REDIRECT_URL               = 'customer/account';

    /**
     * Login button locations
     */
    const BTN_LOCATION_CUSTOMER_GRID         = 'customer_grid';
    const BTN_LOCATION_CUSTOMER_EDIT         = 'customer_edit';
    const BTN_LOCATION_ORDER_VIEW            = 'order_view';

    /**
     * ACL resources
     */
    const ACL_CUSTOMER_LOGIN                 = 'Shopigo_LoginAsCustomer::customer_login';
    const ACL_LOGIN_LOGS                     = 'Shopigo_LoginAsCustomer::logs';

    /**
     * Menu
     */
    const MENU_LOGIN_LOGS                    = 'Shopigo_LoginAsCustomer::logs';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param CustomerSession $customerSession
     * @return void
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        CustomerSession $customerSession
    ) {
        $this->encryptor = $encryptor;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Check if the module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (!$this->isModuleOutputEnabled()) {
            return false;
        }
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Check if logs enabled
     *
     * @return bool
     */
    public function isLogEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_LOG_ENABLED,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Retrieve whether the login button is enabled
     *
     * @param string $location
     * @return bool
     */
    public function isLoginBtnEnabled($location = null)
    {
        if (empty($location)) {
            return false;
        }

        $configPath = null;

        switch ($location) {
            // Customers grid
            case self::BTN_LOCATION_CUSTOMER_GRID:
                $configPath = self::XML_PATH_CUSTOMER_GRID_BTN_ENABLED;
                break;

            // Customer information page
            case self::BTN_LOCATION_CUSTOMER_EDIT:
                $configPath = self::XML_PATH_CUSTOMER_EDIT_BTN_ENABLED;
                break;

            // Order detail page
            case self::BTN_LOCATION_ORDER_VIEW:
                $configPath = self::XML_PATH_ORDER_VIEW_BTN_ENABLED;
                break;

            default:
                break;
        }

        if (!empty($configPath)) {
            return $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORES);
        }
        return false;
    }

    /**
     * Retrieve whether the checkout is enabled
     *
     * @return bool
     */
    public function isCheckoutEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CHECKOUT_ENABLED,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Retrieve whether the current administrator is logged as customer
     *
     * @return bool
     */
    public function isLoggedInAsCustomer()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }
        if (!$this->customerSession->getLoginAsCustomerMode()) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve checkout message
     *
     * @return bool
     */
    public function getCheckoutMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHECKOUT_MESSAGE,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Retrieve redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        $redirectUrl = $this->scopeConfig->getValue(
            self::XML_PATH_REDIRECT_URL,
            ScopeInterface::SCOPE_STORES
        );

        if (!$redirectUrl) {
            $redirectUrl = self::DEFAULT_REDIRECT_URL;
        }
        return $redirectUrl;
    }

    /**
     * Encrypt value
     *
     * @param string $data
     * @return string
     */
    public function encryptValue($data)
    {
        return $this->encryptor->encrypt($data);
    }

    /**
     * Decrypt value
     *
     * @param string $data
     * @return string
     */
    public function decryptValue($data)
    {
        return $this->encryptor->decrypt($data);
    }
}
