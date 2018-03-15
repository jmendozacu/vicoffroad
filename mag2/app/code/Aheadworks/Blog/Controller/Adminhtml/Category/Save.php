<?php
namespace Aheadworks\Blog\Controller\Adminhtml\Category;

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Aheadworks\Blog\Controller\Adminhtml\Category
 */
class Save extends \Aheadworks\Blog\Controller\Adminhtml\Category
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
     * Save action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $catId = $this->getRequest()->getParam('cat_id');
            /** @var $category \Aheadworks\Blog\Model\Category */
            $category = $this->categoryFactory->create()->load($catId);
            $category->setData($data);
            $back = $this->getRequest()->getParam('back');
            try {
                $category->save();
                $this->messageManager->addSuccess(__('Category was successfully saved.'));
                $this->_getSession()->setFormData(false);
                if ($back == 'edit') {
                    return $resultRedirect->setPath('*/*/' . $back, ['cat_id' => $category->getCatId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the category.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['cat_id' => $catId]);
        }
        $this->messageManager->addError(__('Something went wrong while saving the category.'));
        return $resultRedirect->setPath('*/*/');
    }
}