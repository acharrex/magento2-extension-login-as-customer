<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Plugin\Ui\Component\Listing\Column;

use Magento\Customer\Ui\Component\Listing\Column\Actions as CustomerActions;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;

class CustomerActionsPlugin
{
    /**
     * @var ContextInterface
     */
    protected $context;

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
     * Initialize dependencies
     *
     * @param ContextInterface $context
     * @param DataHelper $dataHelper
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @return void
     */
    public function __construct(
        ContextInterface $context,
        DataHelper $dataHelper,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder
    ) {
        $this->context = $context;
        $this->dataHelper = $dataHelper;
        $this->authorization = $authorization;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare data source
     *
     * @param CustomerActions $subject
     * @param array $dataSource
     * @return array
     */
    public function afterPrepareDataSource(
        CustomerActions $subject,
        array $dataSource
    ) {
        if (!$this->dataHelper->isLoginBtnEnabled(DataHelper::BTN_LOCATION_CUSTOMER_GRID)) {
            return $dataSource;
        }
        if (!$this->authorization->isAllowed(DataHelper::ACL_CUSTOMER_LOGIN)) {
            return $dataSource;
        }

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$subject->getData('name')]['login_as_customer'] = [
                    'href'   => $this->getLoginAsCustomerUrl($item['entity_id']),
                    'label'  => __('Login as Customer'),
                    'hidden' => false
                ];
            }
        }

        return $dataSource;
    }
}
