<?php
/*@codingStandardIgnoreFile*/
namespace Smv\Ebaygallery\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class ajaxPagination extends \Smv\Ebaygallery\Controller\Index
{
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Smv\Ebaygallery\Helper\Data $helper,
	  	\Smv\Ebaygallery\Model\ImgFactory $photogalleryimgFactory,
	  	\Smv\Ebaygallery\Model\Img $photogalleryimg,
  	  	\Magento\Framework\App\ResourceConnection $coreresource
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_photogalleryimgFactory = $photogalleryimgFactory;
		$this->_photogalleryimg = $photogalleryimg;
		$this->_storeManager = $storeManager;
		$this->_helper = $helper;
  		$this->_coreresource = $coreresource;
        parent::__construct($context,$customerSession,$resultPageFactory);
    }


    public function execute()
    {
		
        $_pagesCount = null;
        $_itemsOnPage =  $this->_helper->getPagination();;
        $itemsLimit=null;
        $_pages=5;
        
        $_currentPage = $this->getRequest()->getParam('page');
        $_pages = $this->getRequest()->getParam('pages');
        $_page = $_currentPage +1;
        $_block_num = $_currentPage - 1;
        $collection = $collection = $this->_photogalleryimgFactory->create()->getCollection();
        $collection->getSelect()->join( array('pht_item'=> $this->_coreresource->getTableName('photogallery')), 'main_table.photogallery_id = pht_item.photogallery_id');
        $collection->getSelect()->order('main_table.img_order ASC');
        $collection->addFieldToFilter('disabled');

        if ($itemsLimit!=null && $itemsLimit<$collection->getSize()) {
        $_pagesCount = ceil($itemsLimit/$_itemsOnPage);
        } else {
        $_pagesCount = ceil($collection->getSize()/$_itemsOnPage);
        }
        for ($i=1; $i<=$_pagesCount;$i++) {
        $this->_pages[] = $i;
        }
        

        $offset = $_itemsOnPage*($_currentPage-1);
        if ($itemsLimit!=NULL) {
        $_itemsCurrentPage = $itemsLimit - $offset;
        if ($_itemsCurrentPage > $_itemsOnPage) {
        $_itemsCurrentPage = $_itemsOnPage;
        }
        $collection->getSelect()->limit($_itemsCurrentPage, $offset);
        } else {
        $collection->getSelect()->limit($_itemsOnPage, $offset);
        }
      
        $html = array();
         $html = "<div class='cbp-loadMore-block".$_block_num."'>";
         foreach ($collection as $_gimage){
                    $ph_id  = $_gimage['img_id'];
                    $targetPath = $this->_helper->getMediaUrl($_gimage["img_name"]);;
                    $thumbPath = $this->_helper->getThumbsDirPath($targetPath);
                    $arrayName = explode('/',$_gimage["img_name"]);
                    $gallery_name = $_gimage['gal_name'];
                    $thumbnail_path =  $thumbPath . $arrayName[3];
                    $image_path = $this->_helper->getMediaUrl($_gimage["img_name"]);
                    $description = $_gimage["description"];
                    $picture_name = $_gimage["img_label"];
                    $picture_descrip = $_gimage["img_description"];
       
        $html .= "<li class= '".$_gimage['photogallery_id']." cbp-item' >";
        $html .=  "<div class='cbp-caption'>";
        $html .= "<div class='cbp-caption-defaultWrap'>";
        $html .= "<img src='$thumbnail_path' alt='' width='100%' height='100%'></div>";
        $html .= "<div class='cbp-caption-activeWrap'>";
        $html .= "<div class='cbp-l-caption-alignCenter'>";
        $html .= "<div class='cbp-l-caption-body'>";
        $html .= "<a href='$image_path' class='cbp-lightbox cbp-l-caption-buttonRight' data-title=''>View larger</a>";
        $html .= "</div>";
        $html .= "</div>" ;                       
        $html .= "</div>" ;                  
        $html .= "</div>"  ;           
        $html .= "<div class='cbp-l-grid-projects-title'>$picture_name</div>";
        $html .= "<div class='cbp-l-grid-projects-desc'>$picture_descrip</div>";
        $html .= "</li>";
        
        }
        $html .= "</div>";
        
        
       $collection->addFieldToFilter('img_id',array('from'>$ph_id));
       if($_currentPage<$_pages)
       {
        $html .= "<input type='hidden' value='".$this->_storeManager->getStore()->getUrl('photogallery/index/ajaxPagination',array('page' => $_page,'pages'=>$_pages))."' id='next_pages'/>";
        $html .= "<div class='cbp-loadMore-block".$_currentPage."'>";
        foreach ($collection as $_gimage){
                    $ph_id  = $_gimage['img_id'];
                    $targetPath = $this->_helper->getMediaUrl($_gimage["img_name"]);;
                    $thumbPath = $this->_helper->getThumbsDirPath($targetPath);
                    $arrayName = explode('/',$_gimage["img_name"]);
                    $gallery_name = $_gimage['gal_name'];
                    $thumbnail_path =  $thumbPath . $arrayName[3];
                    $image_path = $this->_helper->getMediaUrl($_gimage["img_name"]);
                    $description = $_gimage["description"];
                    $picture_name = $_gimage["img_label"];
                    $picture_descrip = $_gimage["img_description"];
        $html .= "<li class= '".$_gimage['photogallery_id']." cbp-item' >";
        $html .=  "<div class='cbp-caption'>";
        $html .= "<div class='cbp-caption-defaultWrap'>";
        $html .= "<img src='$thumbnail_path' alt='' ></div>";
        $html .= "<div class='cbp-caption-activeWrap'>";
        $html .= "<div class='cbp-l-caption-alignCenter'>";
        $html .= "<div class='cbp-l-caption-body'>";
        $html .= "<a href='$image_path' class='cbp-lightbox cbp-l-caption-buttonRight' data-title=''>View larger</a>";
        $html .= "</div>";
        $html .= "</div>" ;                       
        $html .= "</div>" ;                  
        $html .= "</div>"  ;           
        $html .= "<div class='cbp-l-grid-projects-title'>$picture_name</div>";
        $html .= "<div class='cbp-l-grid-projects-desc'>$picture_descrip</div>";
        $html .= "</li>";
        
        }
        $html .= "</div>";
       }
        echo $html;
        
    
    }
    
}
/*@codingStandardIgnoreFile*/