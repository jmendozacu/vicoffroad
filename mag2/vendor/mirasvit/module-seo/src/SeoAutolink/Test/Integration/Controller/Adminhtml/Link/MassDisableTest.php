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



namespace Mirasvit\SeoAutolink\Controller\Adminhtml\Link;

/**
 * @magentoAppArea adminhtml
 */
class MassDisableTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_SeoAutolink::seoautolink_link';
        $this->uri = 'backend/seoautolink/link/massdisable';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Mirasvit/SeoAutolink/_files/links.php
     *
     * @covers  Mirasvit\SeoAutolink\Controller\Adminhtml\Link\MassDisable::execute
     */
    public function testMassDisableAction()
    {
        $this->getRequest()->setParam('link_id', [5]);
        $this->dispatch('backend/seoautolink/link/massdisable');

        $this->assertSessionMessages(
            $this->equalTo(['Total of 1 record(s) were successfully disabled']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringContains('seoautolink/link/index/'));

        /** @var \Mirasvit\SeoAutolink\Model\Link $link */
        $link = $this->_objectManager->create('Mirasvit\SeoAutolink\Model\Link')->load(5);
        $this->assertEquals(5, $link->getId());
    }
}
