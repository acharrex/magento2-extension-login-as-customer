<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Plugin\Backend\Sales\Order\Detail;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;

use Magento\Backend\Block\Widget\Context;

class AddLoginButtonPlugin
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Retrieve the 'login as customer' url
     *
     * @param int $customerId Customer id
     * @return string
     */
    protected function getLoginAsCustomerUrl($customerId)
    {
        return $this->urlBuilder->getUrl(
            'shopigo_loginascustomer/login',
            ['customer_id' => $customerId]
        );
    }

    /**
     * Retrieve order model object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }

    /**
     * Return the customer id
     *
     * @return int|null
     */
    protected function getCustomerId()
    {
        $order = $this->getOrder();

        if ($order && !$order->getCustomerIsGuest()) {
            return $order->getCustomerId();
        }
        return null;
    }

    /**
     * Initialize dependencies
     *
     * @param DataHelper $dataHelper
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @param Registry $coreRegistry
     * @return void
     */
    public function __construct(
        DataHelper $dataHelper,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder,
        Registry $coreRegistry
    ) {
        $this->dataHelper = $dataHelper;
        $this->authorization = $authorization;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Prepare data source
     *
     * @param CustomerActions $subject
     * @param array $dataSource
     * @return array
     */
    public function afterGetButtonList(
        Context $subject,
        $buttonList
    ) {
        if ($subject->getRequest()->getFullActionName() != 'sales_order_view') {
            return $buttonList;
        }

        if (!$this->dataHelper->isLoginBtnEnabled(DataHelper::BTN_LOCATION_ORDER_VIEW)) {
            return $buttonList;
        }

        if (!$this->authorization->isAllowed(DataHelper::ACL_CUSTOMER_LOGIN)) {
            return $buttonList;
        }

        $customerId = $this->getCustomerId();
        if (!$customerId) {
            return $buttonList;
        }

        $buttonList->add(
            'login_as_customer_button',
            [
                'label'      => __('Login as Customer'),
                'class'      => 'login-button',
                'onclick'    => "window.open('" . $this->getLoginAsCustomerUrl($customerId) . "')",
                'sort_order' => 0
            ]
        );

        return $buttonList;
    }
}
