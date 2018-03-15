<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.49
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Index extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_index';
        $this->_blockGroup = 'Mirasvit_Search';
        $this->_addButtonLabel = __('Add New Search Index');

        parent::_construct();
    }
}
