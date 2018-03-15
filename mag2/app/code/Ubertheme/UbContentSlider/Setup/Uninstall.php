<?php
/**
 * Copyright © 2015 UberTheme. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ubertheme\UbContentSlider\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface {

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        //uninstall code, drop related tables
        $installer->getConnection()->dropTable($installer->getTable('ubcontentslider_slide_item'));
        $installer->getConnection()->dropTable($installer->getTable('ubcontentslider_slide_store'));
        $installer->getConnection()->dropTable($installer->getTable('ubcontentslider_slide'));

        $installer->endSetup();
    }
}
