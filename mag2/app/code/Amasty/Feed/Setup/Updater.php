<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Feed\Setup;

use Magento\Framework\Setup;

class Updater implements Setup\SampleData\InstallerInterface
{
    protected $import;
    protected $templates = [];

    public function __construct(
        \Amasty\Feed\Model\Import $import
    ) {
        $this->import = $import;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->import->update($this->templates);
    }

    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }
}