<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Model\Config\Backend;

use Magento\Cron\Model\Config\Source\Frequency as CronSourceFrequency;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\Config\ValueFactory as ConfigValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Cron extends ConfigValue
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/shopigo_loginascustomer_delete_old_logs/schedule/cron_expr';

    /**
     * Cron model path
     */
    const CRON_MODEL_PATH = 'crontab/default/jobs/shopigo_loginascustomer_delete_old_logs/run/model';

    /**
     * @var ConfigValueFactory
     */
    protected $configValueFactory;

    /**
     * @var string
     */
    protected $runModelPath = '';

    /**
     * Initialize dependencies
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ConfigValueFactory $configValueFactory
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param string $runModelPath
     * @param array $data
     * @return void
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        CacheTypeListInterface $cacheTypeList,
        ConfigValueFactory $configValueFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        $runModelPath = '',
        array $data = []
    ) {
        $this->runModelPath = $runModelPath;
        $this->configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Processing object after save data
     *
     * @return \Shopigo\LoginAsCustomer\Model\Config\Backend\Cron
     */
    public function afterSave()
    {
        $time = $this->getData('groups/shopigo_loginascustomer_log_cron/fields/time/value');
        $frequency = $this->getData('groups/shopigo_loginascustomer_log_cron/fields/frequency/value');

        $cronExprArray = [
            intval($time[1]),                                            // Minute
            intval($time[0]),                                            // Hour
            $frequency == CronSourceFrequency::CRON_MONTHLY ? '1' : '*', // Day of the Month
            '*',                                                         // Month of the Year
            $frequency == CronSourceFrequency::CRON_WEEKLY ? '1' : '*',  // Day of the Week
        ];

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->configValueFactory->create()->load(
                self::CRON_STRING_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH
            )->save();
            $this->configValueFactory->create()->load(
                self::CRON_MODEL_PATH,
                'path'
            )->setValue(
                $this->runModelPath
            )->setPath(
                self::CRON_MODEL_PATH
            )->save();
        } catch (\Exception $e) {
            throw new \Exception(__("We can't save the cron expression."));
        }

        return parent::afterSave();
    }
}
