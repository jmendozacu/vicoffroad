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



namespace Mirasvit\Seo\Test\Unit\Model\System\Config\Source;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Seo\Model\Config\Source\ProductUrlFormat
 */
class ProductUrlFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Seo\Model\Config\Source\ProductUrlFormat|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $producturlformatModel;

    /**
     * @var \Mirasvit\Core\Helper\Version|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreVersionMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->mstcoreVersionMock = $this->getMock(
            '\Mirasvit\Core\Helper\Version',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->producturlformatModel = $this->objectManager->getObject(
            '\Mirasvit\Seo\Model\Config\Source\ProductUrlFormat',
            [
                'mstcoreVersion' => $this->mstcoreVersionMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->producturlformatModel, $this->producturlformatModel);
    }
}
