<?php
namespace Smile\Customer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 *
 * @package Smile\Customer\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('smile_customer_request_price')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Request id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [],
            'Customer name'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            [],
            'Customer email'
        )->addColumn(
            'comment',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Request comment'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            [
                'nullable' => false,
                'default' => '1'
            ],
            'Answer status'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => Table::TIMESTAMP_INIT
            ],
            'Request date'
        )->addColumn(
            'answer',
            Table::TYPE_TEXT,
            '2M',
            [],
            'Answer on request'
        )->addColumn(
            'product_sku',
            Table::TYPE_TEXT,
            255,
            [
                'unsigned' => true,
                'nullable' => true
            ],
            'Product sku'
        )->addForeignKey(
            $installer->getFkName(
                'smile_customer_request_price',
                'product_sku',
                'catalog_product_entity',
                'sku'
            ),
            'product_sku',
            $installer->getTable('catalog_product_entity'),
            'sku',
            Table::ACTION_CASCADE
        )->setComment(
            'Catalog products requested price'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
