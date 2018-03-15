<?php
namespace Smv\Ebaygallery\Block\Adminhtml\Photogallery\Edit\Tab;


use Magento\Framework\Data\Tree\Node;

class Categories extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    /**
     * @var int[]
     */
    protected $_selectedIds = [];

    /**
     * @var array
     */
    protected $_expandedPath = [];

   /**
    * 
    * @param \Magento\Backend\Block\Template\Context       $context         
    * @param \Magento\Catalog\Model\Resource\Category\Tree $categoryTree    
    * @param \Magento\Framework\Registry                   $registry        
    * @param \Magento\Catalog\Model\CategoryFactory        $categoryFactory 
    * @param \Magento\Framework\Json\EncoderInterface      $jsonEncoder     
    * @param \Magento\Framework\DB\Helper                  $resourceHelper  
    * @param \Magento\Backend\Model\Auth\Session           $backendSession  
    * @param \Magento\Framework\ObjectManagerInterface     $objectManager   
    * @param array                                         $data            
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Smv\Ebaygallery\Model\Photogallery $photogalleryModel,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_photogalleryModel = $photogalleryModel;
        $this->coreRegistry = $registry;
        parent::__construct($context, $categoryTree, $registry, $categoryFactory,$jsonEncoder,$resourceHelper,$backendSession, $data);
    }

    /**
     * @return void
     */
    
    protected function _prepareLayout()
    {
        $this->setTemplate('photogallery/cattree.phtml');
    }

    /**
     * @return int[]
     */
    public function getCategoryIds()
    {
        $result = array();
        $catIds = $this->coreRegistry->registry('photogallery_data')->getPhotogalleryCategories();
        if($catIds!= "") {
                $catIds = explode(",", $catIds);         
                $result = array_unique($catIds);
            }
        return $result;
    }

    /**
     * @param mixed $ids
     * @return $this
     */
    public function setCategoryIds($ids)
    {
        if (empty($ids)) {
            $ids = [];
        } elseif (!is_array($ids)) {
            $ids = [(int)$ids];
        }
        $this->_selectedIds = $ids;
        return $this;
    }

    /**
     * @return array
     */
    protected function getExpandedPath()
    {
        return $this->_expandedPath;
    }

    /**
     * @param string $path
     * @return $this
     */
    protected function setExpandedPath($path)
    {
        $this->_expandedPath = array_merge($this->_expandedPath, explode('/', $path));
        return $this;
    }

    /**
     * @param array|Node $node
     * @param int $level
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = [];
        $item['text'] = $this->escapeHtml($node->getName());
        if ($this->_withProductCount) {
            $item['text'] .= ' (' . $node->getProductCount() . ')';
        }
        $item['id'] = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['allowDrop'] = false;
        $item['allowDrag'] = false;
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $this->setExpandedPath($node->getData('path'));
            $item['checked'] = true;
        }
        if ($node->getLevel() < 2) {
            $this->setExpandedPath($node->getData('path'));
        }
        if ($node->hasChildren()) {
            $item['children'] = [];
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child, $level + 1);
            }
        }
        if (empty($item['children']) && (int)$node->getChildrenCount() > 0) {
            $item['children'] = [];
        }
        $item['expanded'] = in_array($node->getId(), $this->getExpandedPath());
        return $item;
    }
}