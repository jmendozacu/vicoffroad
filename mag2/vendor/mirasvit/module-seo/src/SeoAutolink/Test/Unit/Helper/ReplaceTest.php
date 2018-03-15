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



namespace Mirasvit\SeoAutolink\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\SeoAutolink\Helper\Replace
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RepalceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\SeoAutolink\Helper\Replace|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataHelper;

    /**
     * @var \Mirasvit\SeoAutolink\Model\LinkFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkFactoryMock;

    /**
     * @var \Mirasvit\SeoAutolink\Model\Link|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkMock;

    /**
     * @var \Mirasvit\SeoAutolink\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Core\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlMock;

    /**
     * @var \Mirasvit\Core\Api\TextHelperInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreStringMock;

    /**
     * @var \Mirasvit\SeoAutolink\Helper\Pattern|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $seoAutolinkPatternMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->linkFactoryMock = $this->getMock(
            '\Mirasvit\SeoAutolink\Model\LinkFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->linkMock = $this->getMock(
            '\Mirasvit\SeoAutolink\Model\Link',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->linkFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->linkMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\SeoAutolink\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->urlMock = $this->getMock(
            '\Mirasvit\Core\Model\Url',
            [],
            [],
            '',
            false
        );
        $this->coreStringMock = $this->getMock(
            '\Mirasvit\Core\Api\TextHelperInterface',
            [],
            [],
            '',
            false
        );
        $this->seoAutolinkPatternMock = $this->getMock(
            '\Mirasvit\SeoAutolink\Helper\Pattern',
            [],
            [],
            '',
            false
        );
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->dataHelper = $this->objectManager->getObject(
            '\Mirasvit\SeoAutolink\Helper\Replace',
            [
                'linkFactory' => $this->linkFactoryMock,
                'config' => $this->configMock,
                'url' => $this->urlMock,
                'coreString' => $this->coreStringMock,
                'seoAutolinkPattern' => $this->seoAutolinkPatternMock,
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'registry' => $this->registryMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->dataHelper, $this->dataHelper);
    }
}
