<?php
namespace Smv\Ebaygallery\Block\Adminhtml\Photogallery\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    const BASIC_TAB_GROUP_CODE = 'basic';
	
    const ADVANCED_TAB_GROUP_CODE = 'advanced';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Adminhtml catalog
     *
     * @var \Magento\Catalog\Helper\Catalog
     */
    protected $_helperCatalog = null;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $_translateInline;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Helper\Catalog $helperCatalog
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Helper\Catalog $helperCatalog,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        array $data = []
    ) {
        $this->_moduleManager = $moduleManager;
        $this->_helperCatalog = $helperCatalog;
        $this->_catalogData = $catalogData;
        $this->_coreRegistry = $registry;
        $this->_translateInline = $translateInline;
        $this->_layout = $context->getLayout();
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }
    protected function _construct()
    {
        parent::_construct();
        $this->setId('photogallery_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Photogallery Information'));
    }

    protected function _prepareLayout()
    {
    	$this->addTab(
                    'file-info',
                    [
                        'label' => __('Photo Gallery Information'),
                        'content' =>$this->_translateHtml(
                            $this->getLayout()->createBlock(
                                'Smv\Ebaygallery\Block\Adminhtml\Photogallery\Edit\Tab\Form'
                            )->toHtml())
                        ,
                        'group_code' => self::BASIC_TAB_GROUP_CODE
                    ]
                );

        $this->addTab(
                'gal_images',
                [
                    'label' => __('Images'),
                    'content' => 
                            $this->getLayout()->createBlock(
                                'Smv\Ebaygallery\Block\Adminhtml\Photogallery\Edit\Tab\Images'
                            )->toHtml(),
                    'group_code' => self::ADVANCED_TAB_GROUP_CODE
                ]
            );

//    	$this->addTab(
//                'related_products',
//                [
//                    'label' => __('Attach With Products'),
//                    'url' => $this->getUrl('*/*/products', ['_current' => true]),
//                    'class' => 'ajax',
//                    'group_code' => self::ADVANCED_TAB_GROUP_CODE
//                ]
//            );

//        $form = "page_cat_ids";
//        $block =  $this->getLayout()->createBlock("\Smv\Ebaygallery\Block\Adminhtml\Photogallery\Edit\Tab\Categories",
//                "gallery_category_ids",
//                ["data" => ["js_form_object" => $form ]]
//                );
//        $this->addTab(
//                    "add-cat",
//                    [
//                        "label" => __("Attach With Categories"),
//                        "content" =>$this->_translateHtml(
//                            $block->toHtml()),
//                        "group_code" => self::BASIC_TAB_GROUP_CODE
//                    ]
//                );

    }
    public function isAdvancedTabGroupActive()
    {
        return $this->_tabs[$this->_activeTab]->getGroupCode() == self::ADVANCED_TAB_GROUP_CODE;
    }

    protected function _translateHtml($html)
    {
        $this->_translateInline->processResponseBody($html);
        return $html;
    }
}
