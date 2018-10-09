<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Block\Adminhtml\Customer\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;

class Login extends GenericButton implements ButtonProviderInterface
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
     * Retrieve the 'login as customer' url
     *
     * @return string
     */
    protected function getLoginAsCustomerUrl()
    {
        return $this->getUrl('shopigo_loginascustomer/login', [
            'customer_id' => $this->getCustomerId()
        ]);
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param Registry $registry
     * @param DataHelper $dataHelper
     * @return void
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $dataHelper
    ) {
        $this->authorization = $context->getAuthorization();
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $registry);
    }

    /**
     * Retrieve button data
     *
     * @return array
     */
    public function getButtonData()
    {
        if (!$this->dataHelper->isLoginBtnEnabled(DataHelper::BTN_LOCATION_CUSTOMER_EDIT)) {
            return [];
        }
        if (!$this->authorization->isAllowed(DataHelper::ACL_CUSTOMER_LOGIN)) {
            return [];
        }

        $customerId = $this->getCustomerId();
        if (!$customerId) {
            return [];
        }

        $data = [
            'label'      => __('Login as Customer'),
            'class'      => 'login-button',
            'on_click'   => "window.open('" . $this->getLoginAsCustomerUrl() . "')",
            'sort_order' => 45,
        ];
        return $data;
    }
}
