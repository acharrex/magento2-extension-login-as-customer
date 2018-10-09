<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Controller\Login;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\User\Model\UserFactory;
use Psr\Log\LoggerInterface;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;
use Shopigo\LoginAsCustomer\Model\LogRepository;
use Shopigo\LoginAsCustomer\Model\SessionDestroyer;
use Shopigo\LoginAsCustomer\Model\TokenRepository;

class Index extends Action
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var LogRepository
     */
    protected $logRepository;

    /**
     * @var TokenRepository
     */
    protected $tokenRepository;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SessionDestroyer
     */
    protected $sessionDestroyer;

    /**
     * Retrieve token model object
     *
     * @param string $tokenHash
     * @return \Shopigo\LoginAsCustomer\Model\Token|bool
     */
    protected function getToken($tokenHash)
    {
        $token = $this->tokenRepository->getByHash($tokenHash);
        if ($token->getId()) {
            return $token;
        }
        return false;
    }

    /**
     * Retrieve user model object
     *
     * @param int $userId
     * @return \Magento\User\Model\User|bool
     */
    protected function getUser($userId)
    {
        $user = $this->userFactory->create()
            ->load($userId);

        if ($user->getId()) {
            return $user;
        }
        return false;
    }

    /**
     * Retrieve customer model object
     *
     * @param int $customerId
     * @return \Magento\Customer\Model\Customer|bool
     */
    protected function getCustomer($customerId)
    {
        $customer = $this->customerFactory->create()
            ->load($customerId);

        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param CustomerFactory $customerFactory
     * @param CustomerSession $customerSession
     * @param LogRepository $logRepository
     * @param TokenRepository $tokenRepository
     * @param UserFactory $userFactory
     * @param LoggerInterface $logger
     * @param SessionDestroyer $sessionDestroyer
     * @return void
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        CustomerFactory $customerFactory,
        CustomerSession $customerSession,
        LogRepository $logRepository,
        TokenRepository $tokenRepository,
        UserFactory $userFactory,
        LoggerInterface $logger,
        SessionDestroyer $sessionDestroyer
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->logRepository = $logRepository;
        $this->tokenRepository = $tokenRepository;
        $this->userFactory = $userFactory;
        $this->logger = $logger;
        $this->sessionDestroyer = $sessionDestroyer;
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

        try {
            $tokenHash = $this->getRequest()->getParam('token');
            if (!$tokenHash) {
                throw new \Exception(__('Cannot login to account. No token provided.'));
            }

            $token = $this->getToken($tokenHash);
            if (!$token) {
                throw new \Exception(__('Cannot login to account. Invalid token.'));
            }

            $user = $this->getUser($token->getUserId());
            if (!$user) {
                throw new \Exception(__('Cannot login to account. This user no longer exists.'));
            }

            $customer = $this->getCustomer($token->getCustomerId());
            if (!$customer) {
                throw new \Exception(__('Cannot login to account. This customer no longer exists.'));
            }

            if ($this->dataHelper->isLogEnabled()) {
                $this->logRepository->generate(
                    $user->getId(),
                    $user->getUserName(),
                    $customer->getId(),
                    $customer->getStoreId()
                );
            }

            // Destroy/end sessions
            $this->sessionDestroyer->destroy();

            $this->_eventManager->dispatch(
                'shopigo_loginascustomer_login_destroy_sessions',
                ['customer' => $customer]
            );

            // Free all session variables
            session_unset();

            // Login as customer
            $this->customerSession->setCustomerAsLoggedIn($customer);
            $this->customerSession->setLoginAsCustomerMode(true);

            $this->tokenRepository->delete($token);

            $this->messageManager->addSuccessMessage(
                __('You are logged in as customer: %1', $customer->getName())
            );
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Cannot login to account.'));
            $this->_redirect('');
            return;
        }

        $this->_redirect($this->dataHelper->getRedirectUrl());
    }
}
