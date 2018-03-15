<?php
namespace Aheadworks\Blog\Controller\Index;

/**
 * Class Index
 * @package Aheadworks\Blog\Controller\Index
 */
class Index extends \Aheadworks\Blog\Controller\Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $forward */
        $forward = $this->resultForwardFactory->create();
        return $forward->forward('list');
    }
}
