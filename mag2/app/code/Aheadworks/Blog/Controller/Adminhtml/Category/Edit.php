<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;

class Edit extends \Aheadworks\Blog\Controller\Adminhtml\Category
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Aheadworks\Blog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Aheadworks\Blog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Aheadworks\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var $category \Aheadworks\Blog\Model\Category */
        $category = $this->categoryFactory->create();
        $catId = $this->getRequest()->getParam('cat_id');
        if ($catId) {
            $category->load($catId);
            if (!$category->getId()) {
                $this->messageManager->addError(__('This category no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/*');
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $category->addData($data);
        }

        $this->coreRegistry->register('aw_blog_category', $category);

        $resultPage = $this->_getResultPage();
        $resultPage->setActiveMenu('Aheadworks_Blog::categories');
        $resultPage->getConfig()->getTitle()->prepend(
            $category->getId() ?  __('Edit Category') : __('New Category')
        );

        return $resultPage;
    }
}
