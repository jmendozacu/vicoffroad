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



namespace Mirasvit\SeoAutolink\Test\Unit\Model\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\SeoAutolink\Model\Config\Source\Urltarget
 */
class UrltargetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\SeoAutolink\Model\Config\Source\Urltarget|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urltargetModel;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->urltargetModel = $this->objectManager->getObject(
            '\Mirasvit\SeoAutolink\Model\Config\Source\Urltarget',
            [
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->urltargetModel, $this->urltargetModel);
    }
}
