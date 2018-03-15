<?php
namespace Aheadworks\Blog\Controller;

use Aheadworks\Blog\Helper\Config;

/**
 * Blog Router
 * @package Aheadworks\Blog\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    const SEARCH_KEY = 'search';

    const TAG_KEY = 'tag';

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Aheadworks\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @var \Aheadworks\Blog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Aheadworks\Blog\Helper\Config $configHelper
     * @param \Aheadworks\Blog\Model\PostFactory $postFactory
     * @param \Aheadworks\Blog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Aheadworks\Blog\Model\PostFactory $postFactory,
        \Aheadworks\Blog\Model\CategoryFactory $categoryFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->configHelper = $configHelper;
        $this->postFactory = $postFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Match blog page
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $moduleName = 'aw_blog';
        $controllerName = 'index';
        $actionName = 'index';
        $params = null;

        $parts = explode('/', trim($request->getPathInfo(), '/'));
        if (array_shift($parts) != $this->configHelper->getValue(Config::XML_GENERAL_ROUTE_TO_BLOG)) {
            return false;
        }
        if (count($parts)) {
            $urlKey = array_shift($parts);
            if ($urlKey == self::TAG_KEY) {
                $tagName = array_shift($parts);
                if ($tagName) {
                    $params['tag'] = urldecode($tagName);
                }
            } elseif ($urlKey == self::SEARCH_KEY) {
                // todo
            } else {
                if ($postId = $this->getPostIdByUrlKey($urlKey)) {
                    $controllerName = 'post';
                    $actionName = 'view';
                    $params['id'] = $postId;
                } else {
                    if ($categoryId = $this->getCategoryIdByUrlKey($urlKey)) {
                        $controllerName = 'category';
                        $actionName = 'view';
                        $params['id'] = $categoryId;

                        $postUrlKey = array_shift($parts);
                        if ($postUrlKey) {
                            if ($postId = $this->getPostIdByUrlKey($postUrlKey)) {
                                $controllerName = 'post';
                                $actionName = 'view';
                                $params['id'] = $postId;
                                $params['category_id'] = $categoryId;
                            }
                        }
                    } else {
                        $moduleName = 'cms';
                        $controllerName = 'noroute';
                        $actionName = 'index';
                    }
                }
            }
        }

        $request
            ->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);
        if ($params) {
            $request->setParams($params);
        }
        return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
    }

    /**
     * Retrieves post ID by URL-Key
     *
     * @param string $urlKey
     * @return int|null
     */
    protected function getPostIdByUrlKey($urlKey)
    {
        /** @var \Aheadworks\Blog\Model\Post $postModel */
        $postModel = $this->postFactory->create();
        return $postModel->getIdByUrlKey($urlKey);
    }

    /**
     * Retrieves category ID by URL-Key
     *
     * @param string $urlKey
     * @return int|null
     */
    protected function getCategoryIdByUrlKey($urlKey)
    {
        /** @var \Aheadworks\Blog\Model\Category $categoryModel */
        $categoryModel = $this->categoryFactory->create();
        return $categoryModel->getIdByUrlKey($urlKey);
    }
}
