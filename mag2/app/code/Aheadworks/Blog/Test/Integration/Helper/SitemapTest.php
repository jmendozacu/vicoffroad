<?php
namespace Aheadworks\Blog\Test\Integration\Helper;

/**
 * Class SitemapTest
 * @package Aheadworks\Blog\Test\Integration\Helper
 */
class SitemapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Helper\Sitemap
     */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->helper = $this->objectManager->create('Aheadworks\Blog\Helper\Sitemap');
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getId();
    }

    public function testGetBlogItem()
    {
        $siteMapItem = $this->helper->getBlogItem($this->getStoreId());
        $this->checkDataFormat($siteMapItem);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/categories.php
     */
    public function testGetCategoryItems()
    {
        $siteMapItem = $this->helper->getCategoryItems($this->getStoreId());
        $this->checkDataFormat($siteMapItem);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testGetPostItems()
    {
        $siteMapItem = $this->helper->getPostItems($this->getStoreId());
        $this->checkDataFormat($siteMapItem);
    }

    protected function checkDataFormat($siteMapItem)
    {
        $this->assertInstanceOf('Magento\Framework\DataObject', $siteMapItem);
        $this->assertTrue(is_array($siteMapItem->getCollection()));
        foreach ($siteMapItem->getCollection() as $item) {
            $this->assertInstanceOf('Magento\Framework\DataObject', $item);
            $this->assertNotNull($item->getUrl());
        }
    }
}
