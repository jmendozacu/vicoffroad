<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Aheadworks\Blog\Controller\Adminhtml\Category
 */
class Delete extends \Aheadworks\Blog\Controller\Adminhtml\Category
{
    /**
     * @var \Aheadworks\Blog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Aheadworks\Blog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Aheadworks\Blog\Model\CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $catId = $this->getRequest()->getParam('cat_id');
        if ($catId) {
            /** @var $category \Aheadworks\Blog\Model\Category */
            $category = $this->categoryFactory->create()->load($catId);
            try {
                $category->delete();
                $this->messageManager->addSuccess(__('Category was successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while deleting the category.'));
            }
            return $resultRedirect->setPath('*/*/edit', ['cat_id' => $category->getCatId()]);
        }
        $this->messageManager->addError(__('Cannot delete: wrong category ID.'));
        return $resultRedirect->setPath('*/*/');
    }
}