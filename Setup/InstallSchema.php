<?php
/**
 * Copyright © Shopigo. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 */

namespace Shopigo\LoginAsCustomer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Login as customer setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'shopigo_loginascustomer_log'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shopigo_loginascustomer_log')
        )->addColumn(
            'log_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Log Id'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Admin User Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Customer Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'user',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            40,
            [],
            'User name'
        )->addColumn(
            'ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '50',
            ['nullable' => true, 'default' => null],
            'Ip address'
        )->addColumn(
            'x_forwarded_ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '50',
            ['nullable' => true, 'default' => null],
            'Real ip address if visitor used proxy'
        )->addColumn(
            'time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Log Time'
        )->addIndex(
            $installer->getIdxName('shopigo_loginascustomer_log', ['user_id']),
            ['user_id']
        )->addIndex(
            $installer->getIdxName('shopigo_loginascustomer_log', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $installer->getIdxName('shopigo_loginascustomer_log', ['store_id']),
            ['store_id']
        )
        ->addForeignKey(
            $installer->getFkName('shopigo_loginascustomer_log', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->setComment(
            'Shopigo Login as Customer Logs Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'shopigo_loginascustomer_token'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shopigo_loginascustomer_token')
        )->addColumn(
            'token_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Token Id'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Admin User Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Customer Id'
        )->addColumn(
            'token',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Token'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $installer->getIdxName('shopigo_loginascustomer_log', ['user_id']),
            ['user_id']
        )->addIndex(
            $installer->getIdxName('shopigo_loginascustomer_log', ['customer_id']),
            ['customer_id']
        )->setComment(
            'Shopigo Login as Customer Tokens Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
