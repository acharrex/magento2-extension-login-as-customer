<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;

class DisableCheckoutObserver implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Initialize dependencies
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Disable checkout
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->dataHelper->isEnabled()) {
            return $this;
        }
        if (!$this->dataHelper->isLoggedInAsCustomer()) {
            return $this;
        }

        if ($this->dataHelper->isCheckoutEnabled()) {
            return $this;
        }

        $event = $observer->getEvent();
        $quote = $event->getQuote();

        if (!$quote->getHasError()) {
            $quote->setHasError(true);
        }

        return $this;
    }
}
