<?php
namespace Smv\Ebaygallery\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        /**
         * Create table 'Photogallery Table'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('photogallery'))
            ->addColumn(
                'photogallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Photogallery Id'
            )
            ->addColumn(
                'gal_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Gallery Name'
            )
            ->addColumn(
                'gorder',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Gallery Order'
            )
            ->addColumn(
                "category_ids",
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ["nullable" => true, "default" => null],
                "Categories Ids"
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Description'
            )
            ->addColumn(
                'show_in',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Show In'
            )
             ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Status'
            )
            ->addColumn(
                'created_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Created Time'
            )
            ->addColumn(
                'update_time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Updated Time'
            )
            ->addIndex(
                $installer->getIdxName('photogallery', ['photogallery_id']),
                ['photogallery_id']
            )
            ->setComment('Photogallery Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'photogallery_images'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('photogallery_images'))
            ->addColumn(
                'img_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Image Id'
            )
            ->addColumn(
                'img_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => ''],
                'Name'
            )
            ->addColumn(
                'img_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => ''],
                'Label'
            )
            ->addColumn(
                'photogallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Photogallery Id'
            )
            ->addColumn(
                'img_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => null],
                'Image Order'
            )
            ->addColumn(
                'disabled',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => 0],
                'Disabled'
            )
            ->addColumn(
                'img_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Description'
            )
            ->addForeignKey(
                $installer->getFkName('photogallery_img_ph', 'photogallery_id', 'photogallery', 'photogallery_id'),
                'photogallery_id',
                $installer->getTable('photogallery'),
                'photogallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Image Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'photogallery_products'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('photogallery_products'))
            ->addColumn(
                'photogallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Photogallery Id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'default' => 0],
                'Product Id'
            )
            ->addForeignKey(
                $installer->getFkName('photogallery_products', 'photogallery_id', 'photogallery', 'photogallery_id'),
                'photogallery_id',
                $installer->getTable('photogallery'),
                'photogallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Products Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'photogallery_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('photogallery_store'))
            ->addColumn(
                'photogallery_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Photogallery Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => null],
                'Store Id'
            )
            ->addForeignKey(
                $installer->getFkName('photogallery_store', 'photogallery_id', 'photogallery', 'photogallery_id'),
                'photogallery_id',
                $installer->getTable('photogallery'),
                'photogallery_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            
            ->setComment('Store Table');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();

    }
}
