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



namespace Mirasvit\SeoAutolink\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\SeoAutolink\Model\Link
 */
class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\SeoAutolink\Model\Link|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkModel;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->linkModel = $this->objectManager->getObject(
            '\Mirasvit\SeoAutolink\Model\Link',
            [
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->linkModel, $this->linkModel);
    }
}
