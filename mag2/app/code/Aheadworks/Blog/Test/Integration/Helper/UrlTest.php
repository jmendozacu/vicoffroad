<?php
namespace Aheadworks\Blog\Test\Integration\Helper;

/**
 * Class UrlTest
 * @package Aheadworks\Blog\Test\Integration\Helper
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Helper\Url
     */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->helper = $this->objectManager->create('Aheadworks\Blog\Helper\Url');
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/post.php
     */
    public function testGetPostRoute()
    {
        $postModel = $this->objectManager->create('Aheadworks\Blog\Model\Post')
            ->load('post', 'url_key');
        $this->assertEquals('blog/post', $this->helper->getPostRoute($postModel));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/category.php
     */
    public function testGetCategoryRoute()
    {
        $categoryModel = $this->objectManager->create('Aheadworks\Blog\Model\Category')
            ->load('cat', 'url_key');
        $this->assertEquals('blog/cat', $this->helper->getCategoryRoute($categoryModel));
    }
}
