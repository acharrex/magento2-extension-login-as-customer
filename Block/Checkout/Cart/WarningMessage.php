<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Block\Checkout\Cart;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\Message\CollectionFactory as MessageCollectionFactory;
use Magento\Framework\Message\Factory as MessageFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;
use Magento\Framework\View\Element\Template\Context;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;

class WarningMessage extends Messages
{
    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

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
        if (!$this->cartHelper->getItemsCount()) {
            return false;
        }

        if ($this->dataHelper->isCheckoutEnabled()) {
            return false;
        }
        return true;
    }

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param MessageFactory $messageFactory
     * @param MessageCollectionFactory $collectionFactory
     * @param MessageManagerInterface $messageManager
     * @param InterpretationStrategyInterface $interpretationStrategy
     * @param CartHelper $cartHelper
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        MessageFactory $messageFactory,
        MessageCollectionFactory $collectionFactory,
        MessageManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        CartHelper $cartHelper,
        DataHelper $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $messageFactory, $collectionFactory, $messageManager, $interpretationStrategy, $data);
        $this->cartHelper = $cartHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($this->canDisplay()) {
            $this->addCheckoutMessage();
            $this->addQuoteMessages();
            $this->addMessages($this->messageManager->getMessages(true));
        }
        return parent::_prepareLayout();
    }

    /**
     * Display warning message
     *
     * @return void
     */
    protected function addCheckoutMessage()
    {
        $message = $this->dataHelper->getCheckoutMessage();
        if (!$message) {
            $message = __('You are not authorized to place an order for this customer.');
        }
        $this->messageManager->addNoticeMessage($message);
    }

    /**
     * Add quote messages
     *
     * @return void
     */
    protected function addQuoteMessages()
    {
        // Compose array of messages to add
        $messages = [];

        /** @var \Magento\Framework\Message\MessageInterface $message */
        foreach ($this->cartHelper->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setText($this->escapeHtml($message->getText()));
                $messages[] = $message;
            }
        }

        if ($messages) {
            $this->messageManager->addUniqueMessages($messages);
        }
    }
}
