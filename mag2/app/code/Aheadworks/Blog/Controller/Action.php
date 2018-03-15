<?php
namespace Aheadworks\Blog\Controller;

use Aheadworks\Blog\Helper\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Action
 * @package Aheadworks\Blog\Controller
 */
abstract class Action extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Aheadworks\Blog\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Aheadworks\Blog\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var \Aheadworks\Blog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Aheadworks\Blog\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @var \Aheadworks\Blog\Model\TagFactory
     */
    protected $tagFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Config $configHelper
     * @param \Aheadworks\Blog\Helper\Url $urlHelper
     * @param \Aheadworks\Blog\Model\CategoryFactory $categoryFactory
     * @param \Aheadworks\Blog\Model\PostFactory $postFactory
     * @param \Aheadworks\Blog\Model\TagFactory $tagFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Aheadworks\Blog\Helper\Config $configHelper,
        \Aheadworks\Blog\Helper\Url $urlHelper,
        \Aheadworks\Blog\Model\CategoryFactory $categoryFactory,
        \Aheadworks\Blog\Model\PostFactory $postFactory,
        \Aheadworks\Blog\Model\TagFactory $tagFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->configHelper = $configHelper;
        $this->urlHelper = $urlHelper;
        $this->categoryFactory = $categoryFactory;
        $this->postFactory = $postFactory;
        $this->tagFactory = $tagFactory;
    }

    /**
     * Dispatch request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->coreRegistry->register('aw_blog_action', true, true);
        return parent::dispatch($request);
    }

    /**
     * Retrieves blog title
     *
     * @return string
     */
    protected function getBlogTitle()
    {
        return $this->configHelper->getValue(Config::XML_GENERAL_BLOG_TITLE);
    }

    /**
     * Get prepared result page
     *
     * @param array $params
     * @return \Magento\Framework\View\Result\Page
     */
    protected function getResultPage($params = [])
    {
        /** $resultPage @var \Magento\Framework\View\Result\Page */
        $resultPage = $this->resultPageFactory->create();
        if (isset($params['title'])) {
            $resultPage->getConfig()->getTitle()->set($params['title']);
        }
        if (isset($params['meta'])) {
            foreach ($params['meta'] as $name => $content) {
                $resultPage->getConfig()->setMetadata($name, $content);
            }
        }
        /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbs */
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'link' => $this->storeManager->getStore()->getBaseUrl()
                ]
            );
            if (isset($params['crumbs']) && !empty($params['crumbs'])) {
                $breadcrumbs->addCrumb(
                    'blog_home',
                    [
                        'label' => $this->getBlogTitle(),
                        'link' => $this->urlHelper->getBlogHomeUrl()
                    ]
                );
                foreach ($params['crumbs'] as $crumb) {
                    $breadcrumbs->addCrumb($crumb['name'], $crumb['info']);
                }
            } else {
                $breadcrumbs->addCrumb('blog_home', ['label' => $this->getBlogTitle()]);
            }
        }
        return $resultPage;
    }

    /**
     * Go back
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
