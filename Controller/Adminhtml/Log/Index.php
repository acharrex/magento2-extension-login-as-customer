<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\NotFoundException;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;

class Index extends Action
{
    /**
     * Authorization resource
     */
    const ADMIN_RESOURCE = 'Shopigo_LoginAsCustomer::logs';

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @return void
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }


    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(DataHelper::MENU_LOGIN_LOGS);
        $resultPage->getConfig()->getTitle()->prepend(__('Login as Customer Logs'));

        return $resultPage;
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->dataHelper->isEnabled()) {
            throw new NotFoundException(__('Page not found.'));
        }
        return parent::dispatch($request);
    }
}
