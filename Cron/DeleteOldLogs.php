<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Shopigo\LoginAsCustomer\Helper\Data as DataHelper;
use Shopigo\LoginAsCustomer\Model\ResourceModel\LogFactory as ResourceLogFactory;

class DeleteOldLogs
{
    /**
     * Config paths
     */
    const XML_PATH_CRON_ENABLED    = 'shopigo_loginascustomer/log_cron/enabled';
    const XML_PATH_CLEAN_AFTER_DAY = 'shopigo_loginascustomer/log_cron/clean_after_day';
    const XML_PATH_ERROR_RECIPIENT = 'shopigo_loginascustomer/log_cron/error_email';
    const XML_PATH_ERROR_TEMPLATE  = 'shopigo_loginascustomer/log_cron/error_email_template';
    const XML_PATH_ERROR_IDENTITY  = 'shopigo_loginascustomer/log_cron/error_email_identity';

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var ResourceLogFactory
     */
    protected $resourceLogFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * Check if logs cleaning enabled
     *
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CRON_ENABLED,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Retrieve the number of days after which logs should be removed
     *
     * @return int
     */
    protected function getCleanAfterDay()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_CLEAN_AFTER_DAY,
            ScopeInterface::SCOPE_STORES
        );
    }

    /**
     * Send email to administrator if error
     *
     * @param  string $error Error message
     *
     * @return void
     */
    protected function sendErrorEmail($error)
    {
        if (empty($error)) {
            return;
        }

        if ($this->scopeConfig->getValue(
            self::XML_PATH_ERROR_RECIPIENT,
            ScopeInterface::SCOPE_STORES
        )
        ) {
            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->scopeConfig->getValue(
                    self::XML_PATH_ERROR_TEMPLATE,
                    ScopeInterface::SCOPE_STORES
                )
            )->setTemplateOptions(
                [
                    'area'  => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )->setTemplateVars(
                ['error' => $error]
            )->setFrom(
                $this->scopeConfig->getValue(
                    self::XML_PATH_ERROR_IDENTITY,
                    ScopeInterface::SCOPE_STORES
                )
            )->addTo(
                $this->scopeConfig->getValue(
                    self::XML_PATH_ERROR_RECIPIENT,
                    ScopeInterface::SCOPE_STORES
                )
            )->getTransport();

            $transport->sendMessage();

            $this->inlineTranslation->resume();
        }
    }

    /**
     * Initialize dependencies
     *
     * @param DataHelper $dataHelper
     * @param ResourceLogFactory $resourceLogFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @return void
     */
    public function __construct(
        DataHelper $dataHelper,
        ResourceLogFactory $resourceLogFactory,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        $this->dataHelper = $dataHelper;
        $this->resourceLogFactory = $resourceLogFactory;
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Delete unnecessary logs
     *
     * @return DeleteOldLogs
     */
    public function execute()
    {
        // Check if logs cleaning enabled
        if (!$this->isEnabled()) {
            return;
        }

        try {
            $this->resourceLogFactory->create()
                ->deleteOldLogs($this->getCleanAfterDay());
        } catch (\Exception $e) {
            $this->sendErrorEmail($e->getMessage());
        }
        return $this;
    }
}
