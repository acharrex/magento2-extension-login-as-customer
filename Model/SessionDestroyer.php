<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model;

use Magento\Authorizenet\Model\Directpost\Session as AuthorizenetSession;
use Magento\Catalog\Model\Session as CatalogSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Message\Session as MessageSession;
use Magento\Newsletter\Model\Session as NewsletterSession;

class SessionDestroyer
{
    /**
     * AuthorizenetSession
     */
    protected $authorizenetSession;

    /**
     * CatalogSession
     */
    protected $catalogSession;

    /**
     * CheckoutSession
     */
    protected $checkoutSession;

    /**
     * CustomerSession
     */
    protected $customerSession;

    /**
     * MessageSession
     */
    protected $messageSession;

    /**
     * NewsletterSession
     */
    protected $newsletterSession;

    /**
     * Initialize dependencies
     *
     * @param AuthorizenetSession $authorizenetSession
     * @param CatalogSession $catalogSession
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param MessageSession $messageSession
     * @param NewsletterSession $newsletterSession
     * @return void
     */
    public function __construct(
        AuthorizenetSession $authorizenetSession,
        CatalogSession $catalogSession,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        MessageSession $messageSession,
        NewsletterSession $newsletterSession
    ) {
        $this->authorizenetSession = $authorizenetSession;
        $this->catalogSession = $catalogSession;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->messageSession = $messageSession;
        $this->newsletterSession = $newsletterSession;
    }

    /**
     * Destroy / end sessions
     *
     * @return $this
     */
    public function destroy()
    {
        $this->customerSession->destroy();
        $this->checkoutSession->clearQuote()->destroy();
        $this->authorizenetSession->destroy();
        $this->catalogSession->destroy();
        $this->messageSession->destroy();
        $this->newsletterSession->destroy();
        return $this;
    }
}
