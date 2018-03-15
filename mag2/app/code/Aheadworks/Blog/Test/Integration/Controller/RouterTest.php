<?php
namespace Aheadworks\Blog\Test\Integration\Controller;

/**
 * Class RouterTest
 * @package Aheadworks\Blog\Test\Integration\Controller
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\Blog\Controller\Router
     */
    protected $router;

    /**
     * @var \Magento\TestFramework\Request
     */
    protected $request;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->router = $objectManager->create('Aheadworks\Blog\Controller\Router');
        $this->request = $objectManager->create('Magento\TestFramework\Request');
    }

    /**
     * @dataProvider pathDataProvider
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/categories.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/Blog/Test/Integration/_files/posts.php
     */
    public function testMatch($pathInfo, $moduleName, $controllerName, $actionName, $params = [])
    {
        $this->request->setPathInfo($pathInfo);
        $this->router->match($this->request);
        $this->assertEquals($moduleName, $this->request->getModuleName());
        $this->assertEquals($controllerName, $this->request->getControllerName());
        $this->assertEquals($actionName, $this->request->getActionName());
        foreach ($params as $key => $value) {
            $this->assertNotNull($this->request->getParam($key));
        }
    }

    /**
     * @return array
     */
    public function pathDataProvider()
    {
        return [
            'home page' => ['blog', 'aw_blog', 'index', 'index', []],
            'category page' => ['blog/cat1', 'aw_blog', 'category', 'view', ['id' => 1]],
            'not existing category page' => ['blog/catnotexists', 'cms', 'noroute', 'index', []],
            'post page' => ['blog/post1', 'aw_blog', 'post', 'view', ['id' => 1]],
            'not existing post page' => ['blog/postnotexists', 'cms', 'noroute', 'index', []],
            'category post page' => ['blog/cat1/post1', 'aw_blog', 'post', 'view', ['id' => 1, 'category_id' => 1]],
            'tag page' => ['blog/tag/tag+1', 'aw_blog', 'index', 'index', ['tag' => 'tag 1']],
        ];
    }
}
