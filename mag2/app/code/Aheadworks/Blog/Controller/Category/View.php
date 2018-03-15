<?php
namespace Aheadworks\Blog\Controller\Category;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class View
 * @package Aheadworks\Blog\Controller\Category
 */
class View extends \Aheadworks\Blog\Controller\Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('id');
        try {
            /** @var \Aheadworks\Blog\Model\Category $categoryModel */
            $categoryModel = $this->categoryFactory->create()->load($categoryId);
            if (!$categoryModel->getId()
                || !$categoryModel->getStatus()
                || (!in_array($this->storeManager->getStore()->getId(), $categoryModel->getStores())
                    && !in_array(0, $categoryModel->getStores())
                )
            ) {
                /** @var \Magento\Framework\Controller\Result\Forward $forward */
                $forward = $this->resultForwardFactory->create();
                return $forward
                    ->setModule('cms')
                    ->setController('noroute')
                    ->forward('index');
            }
            $this->coreRegistry->register('aw_blog_category', $categoryModel);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->goBack();
        }
        return $this->getResultPage(
            [
                'title' => $categoryModel->getName(),
                'meta' => [
                    'description' => $categoryModel->getMetaDescription()
                ],
                'crumbs' => [['name' => 'category_view', 'info' => ['label' => $categoryModel->getName()]]]
            ]
        );
    }
}
