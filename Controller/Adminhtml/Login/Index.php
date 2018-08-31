<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Controller\Adminhtml\Login;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session as BackendSession;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ProductMetadata;
use Magento\Store\Model\StoreManagerInterface;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;
use Shopigo\LoginAsCustomer\Model\TokenRepository;

class Index extends Action
{
    /**
     * Authorization resource
     */
    const ADMIN_RESOURCE = 'Shopigo_LoginAsCustomer::customer_login';

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var BackendSession
     */
    protected $authSession;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var TokenRepository
     */
    protected $tokenRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @var ProductMetadata
     */
    protected $productMetadata;

    /**
     * Retrieve redirect URL
     *
     * @param \Magento\Store\Model\Store $store
     * @param string $token
     * @return string
     */
    protected function getRedirectUrl($store, $token)
    {
        $redirectUrl = $store->getUrl('shopigo_loginascustomer/login/index', [
            '_nosid' => true,
            'token'  => $token
        ]);

        /**
         * FIX for Magento >= 2.1.0
         *
         * The method 'getUrl' returns an admin URL
         * Replace the admin base URL by the store base URL
         */
        if (version_compare($this->productMetadata->getVersion(), '2.1.0', '>=')) {
            $redirectUrl = str_replace(
                $this->_url->getBaseUrl() . $this->_url->getAreaFrontName() . '/',
                $store->getBaseUrl(),
                $redirectUrl
            );
        }

        return $redirectUrl;
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param BackendSession $authSession
     * @param CustomerFactory $customerFactory
     * @param TokenRepository $tokenRepository
     * @param StoreManagerInterface $storeManagerInterface
     * @param ProductMetadata $productMetadata
     * @return void
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        ScopeConfigInterface $scopeConfig,
        BackendSession $authSession,
        CustomerFactory $customerFactory,
        TokenRepository $tokenRepository,
        StoreManagerInterface $storeManagerInterface,
        ProductMetadata $productMetadata
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $scopeConfig;
        $this->authSession = $authSession;
        $this->customerFactory = $customerFactory;
        $this->tokenRepository = $tokenRepository;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Login as customer action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->dataHelper->isEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }

        $customerId = (int) $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            $this->messageManager->addErrorMessage(__('Please specify a customer ID.'));
            $this->_redirect('customer');
            return;
        }

        try {
            $customer = $this->customerFactory->create()
                ->load($customerId);

            if (!$customer->getId()) {
                $this->messageManager->addErrorMessage(__('This customer no longer exists.'));
                $this->_redirect('customer');
                return;
            }

            $tokenModel = $this->tokenRepository->generate(
                $this->authSession->getUser()->getId(),
                $customer->getId()
            );

            if (!$tokenModel->getId() || !$tokenModel->getToken()) {
                $this->messageManager->addErrorMessage(__('Unable to generate login token.'));
                $this->_redirect('customer');
                return;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('customer');
            return;
        }

        $redirectUrl = $this->getRedirectUrl(
            $customer->getStore(),
            $tokenModel->getToken()
        );

        $this->_redirect($redirectUrl);
    }
}
