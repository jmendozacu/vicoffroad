<?php
namespace Aheadworks\Blog\Controller\Index;

use Aheadworks\Blog\Helper\Config;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ListAction
 * @package Aheadworks\Blog\Controller\Index
 */
class ListAction extends \Aheadworks\Blog\Controller\Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $title = $this->getBlogTitle();
        $crumbs = [];
        if ($tagName = $this->getRequest()->getParam('tag')) {
            try {
                /** @var \Aheadworks\Blog\Model\Tag $tagModel */
                $tagModel = $this->tagFactory->create()->loadByName($tagName);
                if (!$tagModel->getId()) {
                    throw new LocalizedException(__('Tag doesn\'t exists'));
                }
                $this->coreRegistry->register('aw_blog_tag', $tagModel);

                $title = __("Tagged with '%1'", $tagModel->getName());
                $crumbs[] = ['name' => 'search_by_tag', 'info' => ['label' => $title]];
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                return $this->goBack();
            }
        }

        return $this->getResultPage(
            [
                'title' => $title,
                'meta' => [
                    'description' => $this->configHelper->getValue(Config::XML_SEO_META_DESCRIPTION)
                ],
                'crumbs' => $crumbs
            ]
        );
    }
}
