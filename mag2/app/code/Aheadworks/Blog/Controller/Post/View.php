<?php
namespace Aheadworks\Blog\Controller\Post;

use Aheadworks\Blog\Model\Source\Post\Status;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class View
 * @package Aheadworks\Blog\Controller\Post
 */
class View extends \Aheadworks\Blog\Controller\Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('id');
        $categoryId = $this->getRequest()->getParam('category_id');
        try {
            /** @var \Aheadworks\Blog\Model\Post $postModel */
            $postModel = $this->postFactory->create()->load($postId);
            if (!$postModel->getId()
                || $postModel->getVirtualStatus() != Status::PUBLICATION_PUBLISHED
                || (!in_array($this->storeManager->getStore()->getId(), $postModel->getStores())
                    && !in_array(0, $postModel->getStores())
                )
            ) {
                /** @var \Magento\Framework\Controller\Result\Forward $forward */
                $forward = $this->resultForwardFactory->create();
                return $forward
                    ->setModule('cms')
                    ->setController('noroute')
                    ->forward('index');
            }

            $this->coreRegistry->register('aw_blog_post', $postModel);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->goBack();
        }
        $crumbs = [];
        if ($categoryId) {
            if (in_array($categoryId, $postModel->getCategories())) {
                /** @var \Aheadworks\Blog\Model\Category $categoryModel */
                $categoryModel = $this->categoryFactory->create()->load($categoryId);
                if ($categoryModel->getId()) {
                    $crumbs[] = [
                        'name' => 'category_view',
                        'info' => [
                            'label' => $categoryModel->getName(),
                            'link' => $this->urlHelper->getCategoryUrl($categoryModel)
                        ]
                    ];
                }
            } else {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl($this->urlHelper->getPostUrl($postModel));
                return $resultRedirect;
            }
        }
        $crumbs[] = [
            'name' => 'post_view',
            'info' => ['label' => $postModel->getTitle()]
        ];
        return $this->getResultPage(
            [
                'title' => $postModel->getTitle(),
                'meta' => [
                    'description' => $postModel->getMetaDescription()
                ],
                'crumbs' => $crumbs
            ]
        );
    }
}
