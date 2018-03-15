<?php
namespace Aheadworks\Blog\Block\Adminhtml\Category;

/**
 * Class Grid
 * @package Aheadworks\Blog\Block\Adminhtml\Category
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('awBlogCategoryGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->categoryCollectionFactory->create());
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            [
                'header' => __('Category Name'),
                'index' => 'name',
                'renderer' => '\Aheadworks\Blog\Block\Adminhtml\Category\Grid\Column\Renderer\Name'
            ]
        );
         $this->addColumn(
            'url_key',
            [
                'header' => __('URL-Key'),
                'index' => 'url_key'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'status',
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')],
            ]
        );
        $this->addColumn(
            'store_id',
            [
                'header' => __('Store View'),
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'sortable' => false,
                'filter_condition_callback' => [$this, '_filterStoreCondition'],
                'index' => 'store_id'
            ]
        );
        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'index' => 'sort_order'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return "";
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('aw_blog_admin/*/grid', ['_current' => true]);
    }


    /**
     * Filter store condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }

}
