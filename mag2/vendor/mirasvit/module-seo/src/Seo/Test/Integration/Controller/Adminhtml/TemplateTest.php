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
 * @package   mirasvit/module-seo
 * @version   1.0.38
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Admihtml;

/**
 * @magentoAppArea adminhtml
 */
class TemplateTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * @covers Mirasvit\Helpdesk\Controller\Adminhtml\Template\Index::execute
     */
    public function testIndexExecute()
    {
        $this->dispatch('backend/seo/template/index');
        $this->assertContains(
            'Template Manager',
            $this->getResponse()->getBody()
        );
    }

    /**
     * @covers Mirasvit\Helpdesk\Controller\Adminhtml\Template\Edit::execute
     */
    public function testEditExecute()
    {
        $this->dispatch('backend/seo/template/edit');
        $this->markTestIncomplete();
    }

    /**
     * @covers Mirasvit\Helpdesk\Controller\Adminhtml\Template\Save::execute
     */
    public function testSaveExecute()
    {
        $this->markTestIncomplete();
        $this->dispatch('backend/seo/template/save');
    }
}
