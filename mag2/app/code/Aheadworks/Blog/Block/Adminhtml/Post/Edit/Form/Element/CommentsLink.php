<?php
namespace Aheadworks\Blog\Block\Adminhtml\Post\Edit\Form\Element;

/**
 * Class CommentsLink
 * @package Aheadworks\Blog\Block\Adminhtml\Status\Edit\Form\Element
 */
class CommentsLink extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Aheadworks\Blog\Helper\Disqus
     */
    protected $disqusHelper;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Aheadworks\Blog\Helper\Disqus $disqusHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Aheadworks\Blog\Helper\Disqus $disqusHelper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->disqusHelper = $disqusHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getElementHtml()
    {
        $attributes = new \Magento\Framework\DataObject(
            [
                'id' => $this->getHtmlId(),
                'href' => $this->disqusHelper->getAdminUrl(),
                'target' => '_blank'
            ]
        );
        return '<a ' . $attributes->serialize() . ' >' . __('Go To Comments') . '</a>';
    }
}
