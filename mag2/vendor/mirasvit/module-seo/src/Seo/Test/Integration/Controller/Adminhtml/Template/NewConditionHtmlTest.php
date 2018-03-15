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



namespace Mirasvit\Seo\Controller\Adminhtml\Template;

/**
 * @magentoAppArea adminhtml
 */
class NewConditionHtmlTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->markTestIncomplete();
    }

    /**
     * @covers  Mirasvit\Seo\Controller\Adminhtml\Template\NewConditionHtml::execute
     *
     * @return void
     */
    public function testNewConditionHtmlAction()
    {
        $this->dispatch('backend/seo/template/newconditionhtml');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
    }
}
