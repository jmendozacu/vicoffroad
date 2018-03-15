<?php
namespace Smv\Ebaygallery\Block\Adminhtml\Photogallery;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
   
  
  /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Smv\Ebaygallery\Model\PhotogalleryFactory $photogalleryphotogalleryFactory
     * @param \Smv\Ebaygallery\Model\Photogallery $photogalleryphotogallery
     * @param array $data
     */
   

   public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Smv\Ebaygallery\Model\PhotogalleryFactory $photogalleryphotogalleryFactory,
        \Smv\Ebaygallery\Model\Photogallery $photogalleryphotogallery,
        \Magento\Framework\App\ResourceConnection $coreResource,
        array $data = array()
    ) {
        $this->_photogalleryphotogalleryFactory = $photogalleryphotogalleryFactory;
        $this->_photogalleryphotogallery = $photogalleryphotogallery;
        $this->_coreResource = $coreResource;
        parent::__construct($context, $backendHelper, $data);
    }
    /**
     * Function -> Constructor
    */
  public function _construct()
    {

        parent::_construct();
        $this->setId('photogalleryGrid');
        $this->setDefaultSort('photogallery_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    /**
     * Function -> Prepare Collection
     * @return collection
    */
   protected function _prepareCollection()
    {
        
        $photogallery_images_table = $this->_coreResource->getTableName('photogallery_images');
        $collection = $this->_photogalleryphotogalleryFactory->create()->getCollection();
        $collection->getSelect()
      ->joinLeft(array('pi' => $photogallery_images_table),
                        'main_table.photogallery_id=pi.photogallery_id AND (pi.img_id != 0)',
                        array(
                            'images_count' => new \Zend_Db_Expr('count(pi.img_id)'),
                        )
                )
            ->group('main_table.photogallery_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


  /**
     * Function -> Prepare Columns
    */  
  protected function _prepareColumns()
  {
      
      $this->addColumn(
            'photogallery_id',
            [
                'header'    => __('ID'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'photogallery_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

       $this->addColumn(
            'gal_name',
            [
                'header'    => __('Name'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'gal_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

       $this->addColumn(
            'gorder',
            [
                'header'    => __('Order'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'gorder',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        
        $this->addColumn(
            'images_count',
            [
                'header'    => __('Images Attached'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'images_count',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
      
    
    
      $this->addColumn('status', [
          'header'    => __('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ]);
    
   
    
        $this->addColumn('action',
           [
                'header'    =>  __('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => __('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ]);
    
     
    $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
  }

    /**
     * Retrive row Url
     * @return string
    */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
     /**
     * Allow Action In Grid
     * @return boolean
    */

    protected function _isAllowedAction($resourceId)
    {
      return $this->_authorization->isAllowed($resourceId);
    }
}
