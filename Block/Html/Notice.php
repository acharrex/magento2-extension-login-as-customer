<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Block\Html;

use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;

class Notice extends Template
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CustomerUrl
     */
    protected $customerUrl;

    /**
     * @var BackendHelper
     */
    protected $backendHelper;

    /**
     * Retrieve customer model object
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * Check the availability to display the block
     *
     * @return bool
     */
    protected function canDisplay()
    {
        if (!$this->dataHelper->isEnabled()) {
            return false;
        }
        if (!$this->dataHelper->isLoggedInAsCustomer()) {
            return false;
        }
        return true;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->canDisplay()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param CustomerSession $customerSession
     * @param CustomerUrl $customerUrl
     * @param BackendHelper $backendHelper
     * @param array $data
     * @return void
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        CustomerSession $customerSession,
        CustomerUrl $customerUrl,
        BackendHelper $backendHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->backendHelper = $backendHelper;
        parent::__construct($context, $data);

        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->getCustomer()->getName();
    }

    /**
     * Retrieve customer email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->getCustomer()->getEmail();
    }

    /**
     * Retrieve customer logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->customerUrl->getLogoutUrl();
    }

    /**
     * Retrieve backend start page URL
     *
     * @return string
     */
    public function getBackendUrl()
    {
        return $this->backendHelper->getHomePageUrl();
    }
}
