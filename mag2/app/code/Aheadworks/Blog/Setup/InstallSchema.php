<?php
namespace Aheadworks\Blog\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Aheadworks\Blog\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'aw_blog_post'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_blog_post'))
            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Post Id'
            )
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Post Title'
            )
            ->addColumn(
                'url_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'URL-Key'
            )
            ->addColumn(
                'short_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Post Short Content'
            )
            ->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Post Content'
            )
            ->addColumn(
                'author_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Admin User Id'
            )
            ->addColumn(
                'author_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Author Name'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Updated At'
            )
            ->addColumn(
                'publish_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Publish Date'
            )
            ->addColumn(
                'is_allow_comments',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Is Allowed Comments'
            )
            ->addColumn(
                'meta_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Meta Title'
            )
            ->addColumn(
                'meta_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Meta Description'
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_post', 'author_id', 'admin_user', 'user_id'),
                'author_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )
            ->setComment('Blog Post');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_blog_cat'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_blog_cat'))
            ->addColumn(
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Category Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Category Name'
            )
            ->addColumn(
                'url_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'URL-Key'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Sort Order'
            )
            ->addColumn(
                'meta_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Meta Title'
            )
            ->addColumn(
                'meta_description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Meta Description'
            )
            ->setComment('Blog Category');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_blog_tag'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_blog_tag'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Tag Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'count',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Count'
            )
            ->setComment('Blog Tag');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_blog_cat_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_blog_cat_store'))
            ->addColumn(
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Category Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addIndex(
                $installer->getIdxName('aw_blog_cat_store', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_cat_store', 'cat_id', 'aw_blog_cat', 'cat_id'),
                'cat_id',
                $installer->getTable('aw_blog_cat'),
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_cat_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Blog Category To Store Linkage Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_blog_post_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_blog_post_store'))
            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Post Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addIndex(
                $installer->getIdxName('aw_blog_post_store', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_post_store', 'post_id', 'aw_blog_post', 'post_id'),
                'post_id',
                $installer->getTable('aw_blog_post'),
                'post_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_post_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Blog Post To Store Linkage Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_blog_post_cat'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_blog_post_cat'))
            ->addColumn(
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Category ID'
            )
            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Post ID'
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_post_cat', 'cat_id', 'aw_blog_cat', 'cat_id'),
                'cat_id',
                $installer->getTable('aw_blog_cat'),
                'cat_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_post_cat', 'post_id', 'aw_blog_post', 'post_id'),
                'post_id',
                $installer->getTable('aw_blog_post'),
                'post_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Blog Post To Category Linkage Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_blog_post_tag'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_blog_post_tag'))
            ->addColumn(
                'tag_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Tag ID'
            )
            ->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Post ID'
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_post_tag', 'tag_id', 'aw_blog_tag', 'id'),
                'tag_id',
                $installer->getTable('aw_blog_tag'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_blog_post_tag', 'post_id', 'aw_blog_post', 'post_id'),
                'post_id',
                $installer->getTable('aw_blog_post'),
                'post_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Blog Post To Tag Linkage Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
