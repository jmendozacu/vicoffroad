<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */

/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Amasty\Feed\Controller\Adminhtml\Feed;

use Magento\Backend\App\Action;
use Amasty\Feed\Controller\Adminhtml\Feed;

class Duplicate extends \Amasty\Feed\Controller\Adminhtml\Feed\AbstractMassAction
{

    protected function massAction($collection)
    {
        foreach($collection as $model)
        {
            $newModel = $this->feedCopier->copy($model);
            $this->messageManager->addSuccess(__('Feed %1 was duplicated', $model->getName()));
        }
    }
}