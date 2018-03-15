<?php
namespace Aheadworks\Blog\Block\Adminhtml\Page;

/**
 * Class Menu
 * @package Aheadworks\Blog\Block\Adminhtml\Page
 */
class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_Blog::page/menu.phtml';

    /**
     * @var string
     */
    protected $className = 'aw-blog-menu';

    /**
     * @var \Aheadworks\Blog\Helper\Disqus
     */
    protected $disqusHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Aheadworks\Blog\Helper\Disqus $disqusHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Aheadworks\Blog\Helper\Disqus $disqusHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->disqusHelper = $disqusHelper;
    }

    /**
     * Get menu container class name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return array|null
     */
    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                'post' => [
                    'title' => __('Posts'),
                    'url' => $this->getUrl('*/post/index'),
                    'resource' => 'Aheadworks_Blog::posts'
                ],
                'comments' => [
                    'title' => __('Comments'),
                    'url' => $this->disqusHelper->getAdminUrl(),
                    'resource' => 'Aheadworks_Blog::comments',
                    'attr' => [
                        'target' => '_blank'
                    ]
                ],
                'category' => [
                    'title' => __('Categories'),
                    'url' => $this->getUrl('*/category/index'),
                    'resource' => 'Aheadworks_Blog::categories'
                ],
                'system_config' => [
                    'title' => __('Settings'),
                    'url' => $this->getUrl('adminhtml/system_config/edit', ['section' => 'aw_blog'])
                ],
                'readme' => [
                    'title' => __('Readme'),
                    'url' => 'http://confluence.aheadworks.com/display/EUDOC/Blog+-+Magento+2',
                    'attr' => [
                        'target' => '_blank'
                    ],
                    'separator' => true
                ],
                'support' => [
                    'title' => __('Get Support'),
                    'url' => 'http://ecommerce.aheadworks.com/contacts/',
                    'attr' => [
                        'target' => '_blank'
                    ]
                ]
            ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }
            $this->items = $items;
        }
        return $this->items;
    }

    /**
     * @return array
     */
    public function getCurrentItemTitle()
    {
        $items = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName]['title'];
        }
        return '';
    }

    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }
        return $result;
    }

    /**
     * @param string $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        return $itemIndex == $this->getRequest()->getControllerName();
    }
}
