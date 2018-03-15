<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ubertheme\UbContentSlider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        //upgrade to 1.0.7
        if (version_compare($context->getVersion(), '1.0.7') < 0) {
            //update ubcontentslider_slide_item table
            $tableName = $setup->getTable('ubcontentslider_slide_item');
            //check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                //declare some new columns
                $columns = [
                    'start_time' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        'nullable' => true,
                        'comment' => 'Slide starting time',
                    ],
                    'end_time' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        'nullable' => true,
                        'comment' => 'Slide ending time',
                    ]
                ];
                //add columns
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
                //add new index
                $connection->addIndex(
                    $tableName,
                    $setup->getIdxName(
                        $tableName,
                        ['start_time', 'end_time']
                    ),
                    ['start_time', 'end_time']
                );
            }
        }

        $setup->endSetup();
    }
}
